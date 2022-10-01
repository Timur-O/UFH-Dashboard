<?php
    /**
     * @var mysqli $conn the DB Connection
     * @var InfinityFree\MofhClient\Client $mofhClient the iFastNet API connection
     * @var string $user the clientID
     */
    require_once('./securityCheck.php');

    $deleteAccountSQL = "DELETE FROM `clients` WHERE `clientID` = '$user'";
    $deleteAccountResult = $conn->query($deleteAccountSQL);

    if (!$deleteAccountResult) {
        // Database Error
        $errorMessage = $conn->real_escape_string($conn->error);
        $errorLocation = "Close Account";

        // Insert into DB (if possible)
        $insertDatabaseErrorSQL = "INSERT INTO `errors` (`errorText`, `errorLocation`, `errorTime`) VALUES ('$errorMessage', '$errorLocation', now())";
        $conn->query($insertDatabaseErrorSQL);

        //Redirect to error page
        header("Location: ../../error.php?errorCode=" . $conn->insert_id); die();
    }

    //Redirect
    header("Location: ../../logout.php"); die();