<?php
    /**
     * @var mysqli $conn the DB Connection
     * @var InfinityFree\MofhClient\Client $mofhClient the iFastNet API connection
     * @var string $user the clientID
     */
    require_once('./securityCheck.php');

    $ultifreeID = $conn->real_escape_string(test_input($_POST['accountID']));
    $returnDomain = $_POST['returnDomain'];

    $getClientIDForAccountSQL = "SELECT `clientID` FROM `accounts` WHERE `ultifreeID` = '$ultifreeID'";
    $getClientIDForAccountResult = $conn->query($getClientIDForAccountSQL) or die($conn->error);

    if ($getClientIDForAccountResult->fetch_assoc()['clientID'] != $user) {
        // User making request isn't the owner of the account
        header("Location: ../../login.php"); die();
    } else {
        $reactivationRequest = $mofhClient->unsuspend([
           'username' => $ultifreeID
        ]);

        $reactivationResult = $reactivationRequest->send();

        if ($reactivationResult->isSuccessful()) {
            // Change status to processing (callback will mark as active)
            $markAsProcessingSQL = "UPDATE `accounts` SET `status` = 'P' WHERE `ultifreeID` = '$ultifreeID'";
            $result = $conn->query($markAsProcessingSQL);

            if (!$result) {
                // Database Error
                $errorMessage = $conn->real_escape_string($conn->error);
                $errorLocation = "Reactivate Hosting Account (DB)";

                // Insert into DB (if possible)
                $insertDatabaseErrorSQL = "INSERT INTO `errors` (`errorText`, `errorLocation`, `errorTime`) VALUES ('$errorMessage', '$errorLocation', now())";
                $conn->query($insertDatabaseErrorSQL);

                //Redirect to error page
                header("Location: ../../error.php?errorCode=" . $conn->insert_id); die();
            }
        } else {
            // API Error
            $errorMessage = $conn->real_escape_string($reactivationRequest->getMessage());
            $errorLocation = "Reactivate Hosting Account (API)";

            // Insert into DB (if possible)
            $insertDatabaseErrorSQL = "INSERT INTO `errors` (`errorText`, `errorLocation`, `errorTime`) VALUES ('$errorMessage', '$errorLocation', now())";
            $conn->query($insertDatabaseErrorSQL);

            //Redirect to error page
            header("Location: ../../error.php?errorCode=" . $conn->insert_id); die();
        }
    }

    //Redirect
    header("Location: ../hostAccount.php" . $returnDomain); die();