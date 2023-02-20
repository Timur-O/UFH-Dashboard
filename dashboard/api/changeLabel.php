<?php
    /**
     * @var mysqli $conn the DB Connection
     * @var string $user the clientID of the currently logged-in user
     */
    require_once('./securityCheck.php');

    $newLabel = $conn->real_escape_string(test_input($_POST['newLabel']));
    $accountID = $conn->real_escape_string(test_input($_POST['accountID']));
    $returnDomain = $_POST['returnDomain'];

    $getClientIDForAccountSQL = "SELECT `clientID` FROM `accounts` WHERE `accountID` = '$accountID'";
    $getClientIDForAccountResult = $conn->query($getClientIDForAccountSQL);

    if ($getClientIDForAccountResult->fetch_assoc()['clientID'] != $user) {
        // User making request isn't the owner of the account
        header("Location: ../../login.php"); die();
    } else {
        $sql = "UPDATE `accounts` SET `label` = '$newLabel' WHERE `accountID` = '$accountID'";
        $result = $conn->query($sql);

        if (!$result) {
            // Database Error
            $errorMessage = $conn->real_escape_string($conn->error);
            $errorLocation = "Change Label";

            // Insert into DB (if possible)
            $insertDatabaseErrorSQL = "INSERT INTO `errors` (`errorText`, `errorLocation`, `errorTime`) VALUES ('$errorMessage', '$errorLocation', now())";
            $conn->query($insertDatabaseErrorSQL);

            //Redirect to error page
            header("Location: ../../error.php?errorCode=" . $conn->insert_id); die();
        }

        //Redirect
        header("Location: ../hostAccount.php" . $returnDomain); die();
    }

    die();