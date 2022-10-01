<?php
    /**
     * @var mysqli $conn the DB Connection
     * @var InfinityFree\MofhClient\Client $mofhClient the iFastNet API connection
     * @var string $user the clientID
     */
    require_once('./securityCheck.php');

    $newPassword = $conn->real_escape_string(test_input($_POST['newPassword']));
    $ultifreeID = $conn->real_escape_string(test_input($_POST['ultifreeID']));
    $returnDomain = $_POST['returnDomain'];

    $getClientIDForAccountSQL = "SELECT `clientID` FROM `accounts` WHERE `ultifreeID` = '$ultifreeID'";
    $getClientIDForAccountResult = $conn->query($getClientIDForAccountSQL);

    if ($getClientIDForAccountResult->fetch_assoc()['clientID'] != $user) {
        // User making request isn't the owner of the account
        header("Location: ../../login.php"); die();
    } else {
        $changePasswordRequest = $mofhClient->password([
            'username' => $ultifreeID,
            'password' => $newPassword
        ]);

        // Send the API request and keep the response.
        $changePasswordResponse = $changePasswordRequest->send();

        // Check whether the request was successful.
        if ($changePasswordResponse->isSuccessful()) {
            $sql = "UPDATE `accounts` SET `password` = '$newPassword' WHERE `ultifreeID` = '$ultifreeID'";
            $result = $conn->query($sql);

            if (!$result) {
                // Password Changed, But Not Stored
                // Database Error
                $errorMessage = $conn->real_escape_string($conn->error);
                $errorLocation = "Change Password (DB Error)";

                // Insert into DB (if possible)
                $insertDatabaseErrorSQL = "INSERT INTO `errors` (`errorText`, `errorLocation`, `errorTime`) VALUES ('$errorMessage', '$errorLocation', now())";
                $conn->query($insertDatabaseErrorSQL);

                //Redirect to error page
                header("Location: ../../error.php?errorCode=" . $conn->insert_id); die();
            }
        } else {
            // API Error
            $errorMessage = $conn->real_escape_string($changePasswordResponse->getMessage());
            $errorLocation = "Change Password (API Error)";

            // Insert into DB (if possible)
            $insertDatabaseErrorSQL = "INSERT INTO `errors` (`errorText`, `errorLocation`, `errorTime`) VALUES ('$errorMessage', '$errorLocation', now())";
            $conn->query($insertDatabaseErrorSQL);

            //Redirect to error page
            header("Location: ../../error.php?errorCode=" . $conn->insert_id); die();
        }

        //Redirect
        header("Location: ../hostAccount.php" . $returnDomain); die();
    }
