<?php
    /**
     * @var mysqli $conn the DB Connection
     * @var InfinityFree\MofhClient\Client $mofhClient the iFastNet API connection
     * @var string $user the clientID
    */
    require_once('./api/securityCheck.php');

    $user = $conn->real_escape_string($user); // Escape user - just in case (this should be possible to modify though)

    // Initialize List of Variables
    $accountID = NULL; // Used for prepared statement with $fetchAccountData, stores the AccountID to fetch
    $accountUsername = NULL; // Used for prepared statement with $fetchDeactivationData, stores the Account's Username to fetch
    $numberOfAccounts = 0; // Stores the number of accounts a user currently has (max: 3)
    $numberOfCertificates = 0;

    $email = NULL; // Client's email
    $tosDate = NULL; // Date client signed TOS
    $autoLogoutOn = NULL; // Boolean, true if autoLogout is on
    $passChangeHash = NULL; // Hash for changing password
    $verification = NULL; // "YES" if account completed email verification
    $accountID1 = NULL; // The first accountID, null if no account
    $accountID2 = NULL; // The second accountID, null if no account
    $accountID3 = NULL; // The third accountID, null if no account
    $lastLoginTime = NULL; // Contains last time of login
    $lastLoginIP = NULL; // Contains last used IP to login

    $allAnnouncements = NULL; // Contains all current announcements (all SQL rows in table announcements)

    // Prepare SQL Statements
    $fetchAllClientData = "SELECT `email`, `dateTermsAccepted`, `account1`, `account2`, `account3`, `verification`, `passReset`, `autoLogout`, `lastLogin`, `lastLoginIP` FROM `clients` WHERE `clientID` = '$user'";

    $fetchAccountData = $conn->prepare("SELECT `ultifreeID`, `username`, `password`, `domain`, `label`, `status`, `sqlServer`, `hostingVolume`, `apiDomain`, `creationDate` FROM `accounts` WHERE `accountID` = ?");
    $fetchAccountData->bind_param('i', $accountID);

    $fetchDeactivationData = $conn->prepare("SELECT * FROM `suspensions` WHERE CONVERT(username USING utf8) LIKE ?"); // Have to convert to utf8 for compatability with string
    $fetchDeactivationData->bind_param('s', $accountUsername);

    $fetchAnnouncements = "SELECT * FROM `announcements`";

    $fetchSslCertificates = "SELECT * FROM `certificates` WHERE `clientID` LIKE '$user'";

    // Collect Information
    $allAnnouncements = $conn->query($fetchAnnouncements)->fetch_all(MYSQLI_ASSOC);

    $allCertificates = $conn->query($fetchSslCertificates)->fetch_all(MYSQLI_ASSOC);
    $numberOfCertificates = sizeof($allCertificates);

    $accountDataResult = $conn->query($fetchAllClientData)->fetch_assoc();

    $email = $accountDataResult['email'];
    $tosDate = $accountDataResult['dateTermsAccepted'];
    $accountID1 = $accountDataResult['account1'];
    $accountID2 = $accountDataResult['account2'];
    $accountID3 = $accountDataResult['account3'];
    $verification = $accountDataResult['verification'];
    $passChangeHash = $accountDataResult['passReset'];
    $autoLogoutOn = $accountDataResult['autoLogout'];
    $lastLogin = $accountDataResult['lastLogin'];
    $lastLoginIP = $accountDataResult['lastLoginIP'];

    // Make email a session variable (b/c necessary to use in api)
    $_SESSION['email'] = $email;

    // For each non-null account fetch all account information
    if ($accountID1 != NULL) {
        $accountID = $accountID1;
        $fetchAccountData->execute();
        $accountResult1 = $fetchAccountData->get_result()->fetch_assoc();

        $ultifreeID1 = $accountResult1['ultifreeID'];
        $accUsername1 = $accountResult1['username'];
        $accPassword1 = $accountResult1['password'];
        $accDomain1 = $accountResult1['domain'];
        $accLabel1 = $accountResult1['label'];
        $accStatus1 = $accountResult1['status'];
        $accSQLServer1 = $accountResult1['sqlServer'];
        $accHostingVolume1 = $accountResult1['hostingVolume'];
        $accApiDomain1 = $accountResult1['apiDomain'];
        $accCreationDate1 = $accountResult1['creationDate'];
        $numberOfAccounts++;

        // Fetch Deactivations for Account
        $accountUsername = $accUsername1;
        $fetchDeactivationData->execute();
        $accDeactivations1 = $fetchDeactivationData->get_result()->fetch_all(MYSQLI_ASSOC); // Stores all deactivations for that account

        // If HostingVolume or ApiDomain Null -> Fetch that info
        if ($accHostingVolume1 == NULL && $accStatus1 != 'P') {
            $getDomainInfoRequest = $mofhClient->getDomainUser([
                'domain' => $accDomain1
            ]);
            $getDomainInfoResponse = $getDomainInfoRequest->send();

            if ($getDomainInfoResponse->isSuccessful()) {
                $homeDirectory = $getDomainInfoResponse->getDocumentRoot();

                $explodedHomeDirectory = explode("/", $homeDirectory);

                $currHostingVolume = $explodedHomeDirectory[2];
                $currApiDomain = $explodedHomeDirectory[3];

                // Put into DB
                $updateHostingVolumeAndApiDomain = "UPDATE `accounts` SET `hostingVolume` = '$currHostingVolume', `apiDomain` = '$currApiDomain' WHERE `accountID` = '$accountID1'";
                $conn->query($updateHostingVolumeAndApiDomain);

                $accHostingVolume1 = $currHostingVolume;
                $accApiDomain1 = $currApiDomain;
            }
        }

        // Get the current domains of the user connected to this account
        $getUserDomainsRequest = $mofhClient->getUserDomains([
            'username' => $accountUsername
        ]);

        $getUserDomainsResponse = $getUserDomainsRequest->send();

        if ($getUserDomainsResponse->isSuccessful()) {
            $listOfDomainsAccount1 = $getUserDomainsResponse->getDomains();
        } else {
            $listOfDomainsAccount1 = ["Error fetching domains, please try again later."];
        }
    }

    if ($accountID2 != NULL) {
        $accountID = $accountID2;
        $fetchAccountData->execute();
        $accountResult2 = $fetchAccountData->get_result()->fetch_assoc();

        $ultifreeID2 = $accountResult2['ultifreeID'];
        $accUsername2 = $accountResult2['username'];
        $accPassword2 = $accountResult2['password'];
        $accDomain2 = $accountResult2['domain'];
        $accLabel2 = $accountResult2['label'];
        $accStatus2 = $accountResult2['status'];
        $accSQLServer2 = $accountResult2['sqlServer'];
        $accHostingVolume2 = $accountResult2['hostingVolume'];
        $accApiDomain2 = $accountResult2['apiDomain'];
        $accCreationDate2 = $accountResult2['creationDate'];
        $numberOfAccounts++;

        // Fetch Deactivations for Account
        $accountUsername = $accUsername2;
        $fetchDeactivationData->execute();
        $accDeactivations2 = $fetchDeactivationData->get_result()->fetch_all(MYSQLI_ASSOC); // Stores all deactivations for that account

        // If HostingVolume or ApiDomain Null -> Fetch that info
        if ($accHostingVolume2 == NULL && $accStatus2 != 'P') {
            $getDomainInfoRequest = $mofhClient->getDomainUser([
                'domain' => $accDomain2
            ]);
            $getDomainInfoResponse = $getDomainInfoRequest->send();

            if ($getDomainInfoResponse->isSuccessful()) {
                $homeDirectory = $getDomainInfoResponse->getDocumentRoot();

                $explodedHomeDirectory = explode("/", $homeDirectory);

                $currHostingVolume = $explodedHomeDirectory[2];
                $currApiDomain = $explodedHomeDirectory[3];

                // Put into DB
                $updateHostingVolumeAndApiDomain = "UPDATE `accounts` SET `hostingVolume` = '$currHostingVolume', `apiDomain` = '$currApiDomain' WHERE `accountID` = '$accountID2'";
                $conn->query($updateHostingVolumeAndApiDomain);

                $accHostingVolume2 = $currHostingVolume;
                $accApiDomain2 = $currApiDomain;
            }
        }

        // Get the current domains of the user connected to this account
        $getUserDomainsRequest = $mofhClient->getUserDomains([
            'username' => $accountUsername
        ]);

        $getUserDomainsResponse = $getUserDomainsRequest->send();

        if ($getUserDomainsResponse->isSuccessful()) {
            $listOfDomainsAccount2 = $getUserDomainsResponse->getDomains();
        } else {
            $listOfDomainsAccount2 = ["Error fetching domains, please try again later."];
        }
    }

    if ($accountID3 != NULL) {
        $accountID = $accountID3;
        $fetchAccountData->execute();
        $accountResult3 = $fetchAccountData->get_result()->fetch_assoc();

        $ultifreeID3 = $accountResult3['ultifreeID'];
        $accUsername3 = $accountResult3['username'];
        $accPassword3 = $accountResult3['password'];
        $accDomain3 = $accountResult3['domain'];
        $accLabel3 = $accountResult3['label'];
        $accStatus3 = $accountResult3['status'];
        $accSQLServer3 = $accountResult3['sqlServer'];
        $accHostingVolume3 = $accountResult3['hostingVolume'];
        $accApiDomain3 = $accountResult3['apiDomain'];
        $accCreationDate3 = $accountResult3['creationDate'];
        $numberOfAccounts++;

        // Fetch Deactivations for Account
        $accountUsername = $accUsername3;
        $fetchDeactivationData->execute();
        $accDeactivations3 = $fetchDeactivationData->get_result()->fetch_all(MYSQLI_ASSOC); // Stores all deactivations for that account

        // If HostingVolume or ApiDomain Null -> Fetch that info
        if ($accHostingVolume3 == NULL && $accStatus3 != 'P') {
            $getDomainInfoRequest = $mofhClient->getDomainUser([
                'domain' => $accDomain3
            ]);
            $getDomainInfoResponse = $getDomainInfoRequest->send();

            if ($getDomainInfoResponse->isSuccessful()) {
                $homeDirectory = $getDomainInfoResponse->getDocumentRoot();

                $explodedHomeDirectory = explode("/", $homeDirectory);

                $currHostingVolume = $explodedHomeDirectory[2];
                $currApiDomain = $explodedHomeDirectory[3];

                // Put into DB
                $updateHostingVolumeAndApiDomain = "UPDATE `accounts` SET `hostingVolume` = '$currHostingVolume', `apiDomain` = '$currApiDomain' WHERE `accountID` = '$accountID3'";
                $conn->query($updateHostingVolumeAndApiDomain);

                $accHostingVolume3 = $currHostingVolume;
                $accApiDomain3 = $currApiDomain;
            }
        }

        // Get the current domains of the user connected to this account
        $getUserDomainsRequest = $mofhClient->getUserDomains([
            'username' => $accountUsername
        ]);

        $getUserDomainsResponse = $getUserDomainsRequest->send();

        if ($getUserDomainsResponse->isSuccessful()) {
            $listOfDomainsAccount3 = $getUserDomainsResponse->getDomains();
        } else {
            $listOfDomainsAccount3 = ["Error fetching domains, please try again later."];
        }
    }

    // Reformat variables to necessary format
    $tosDate = substr($tosDate,0,2) . "/" . substr($tosDate,2,2) . "/" . substr($tosDate,4); // Reformat to DD/MM/YYY

    if ($verification == "YES") { // Make verification into a proper boolean
        $verification = true;
    } else {
        $verification = false;
    }