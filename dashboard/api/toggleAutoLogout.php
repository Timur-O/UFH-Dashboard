<?php
    /**
     * @var mysqli $conn the DB Connection
     * @var string $user the iFastNet API connection
     */
    require_once('./securityCheck.php');

    $previousStatus = test_input($_POST['prevAutoLogout']);

    // Toggle the status
    if ($previousStatus) {
        $newStatus = 0;
    } else {
        $newStatus = 1;
    }

    $sql = "UPDATE `clients` SET `autoLogout` = '$newStatus' WHERE `clientID` = '$user'";
    $result = $conn->query($sql);

    if (!$result) {
        // Database Error
        $errorMessage = $conn->real_escape_string($conn->error);
        $errorLocation = "Toggle Auto-logout";

        // Insert into DB (if possible)
        $insertDatabaseErrorSQL = "INSERT INTO `errors` (`errorText`, `errorLocation`, `errorTime`) VALUES ('$errorMessage', '$errorLocation', now())";
        $conn->query($insertDatabaseErrorSQL);

        //Redirect to error page
        header("Location: ../../error.php?errorCode=" . $conn->insert_id); die();
    }

    //Redirect back to settings page
    header("Location: ../settings.php"); die();