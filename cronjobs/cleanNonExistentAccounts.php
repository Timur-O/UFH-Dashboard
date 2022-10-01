<?php
    /**
     * @var mysqli $conn The DB Object
     * @var InfinityFree\MofhClient\Client $mofhClient THe API Object
     */
    require_once '../dashboard/api/settings.php';

    // NOT WORKING - getUserDomains == Null if suspended
    // Removes accounts which do not exist in the API system

//    $getAllAccountsSQL = "SELECT `clientID`, `accountID`, `username`, `status` FROM `accounts`";
//    $allAccountsResult = $conn->query($getAllAccountsSQL) or die($conn->error);
//
//    while ($account = $allAccountsResult->fetch_assoc()) {
//        $username = $account['username'];
//
//        $getUserDomainsRequest = $mofhClient->getUserDomains([
//            'username' => $username
//        ]);
//
//        $getUserDomainsResponse = $getUserDomainsRequest->send();
//
//        if ($getUserDomainsResponse->isSuccessful()) {
//            // Account Gone From API (Fully)
//            if (is_null($getUserDomainsResponse->getStatus())) {
//                // Remove from client
//                $accountID = $account['accountID'];
//
//                // Don't Remove from Clients (Other Script Job)
//                $deleteAccountSQL = "DELETE FROM `accounts` WHERE `accountID` = '$accountID'";
//                $conn->query($deleteAccountSQL);
//            }
//        }
//    }