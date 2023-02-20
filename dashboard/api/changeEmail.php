<?php
    /**
     * @var mysqli $conn the DB Connection
     * @var string $user the clientID of the currently logged-in user
     */
    require_once('./securityCheck.php');

    $newEmail = $conn->real_escape_string(test_input($_POST['email']));

    $getClientIDForEmailSQL = "SELECT `clientID` FROM `clients` WHERE `email` LIKE '$newEmail'";
    $getClientIDForEmailResult = $conn->query($getClientIDForEmailSQL);

    if ($getClientIDForEmailResult->num_rows > 0) {
        // A user with this email already exists
        header("Location: ../../login.php"); die();
    } else {
        $updateEmailSQL = "UPDATE `clients` SET `email` = '$newEmail' WHERE `clientID` = '$user'";
        $updateEmailResult = $conn->query($updateEmailSQL);

        if (!$updateEmailResult) {
            // Database Error
            $errorMessage = $conn->real_escape_string($conn->error);
            $errorLocation = "Change Email";

            // Insert into DB (if possible)
            $insertDatabaseErrorSQL = "INSERT INTO `errors` (`errorText`, `errorLocation`, `errorTime`) VALUES ('$errorMessage', '$errorLocation', now())";
            $conn->query($insertDatabaseErrorSQL);

            //Redirect to error page
            header("Location: ../../error.php?errorCode=" . $conn->insert_id); die();
        }

        $_SESSION['clientIDtoVerify'] = $user;

        //Redirect to send verification email
        header("Location: ../../verify.php"); die();
    }

    die();