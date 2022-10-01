<?php
    /**
     * @var mysqli $conn the DB Connection
     * @var InfinityFree\MofhClient\Client $mofhClient the iFastNet API connection
     * @var string $user the clientID
     */
    require_once('./securityCheck.php');

    $certificateID = $conn->real_escape_string($_POST['certificateID']);

    $deleteCertificateSQL = "DELETE FROM `certificates` WHERE `certificateID` = '$certificateID'";
    $deleteCertificateResult = $conn->query($deleteCertificateSQL);

    if (!$deleteCertificateResult) {
        // Database Error
        $errorMessage = $conn->real_escape_string($conn->error);
        $errorLocation = "Delete SSL Certificate (DB)";

        // Insert into DB (if possible)
        $insertDatabaseErrorSQL = "INSERT INTO `errors` (`errorText`, `errorLocation`, `errorTime`) VALUES ('$errorMessage', '$errorLocation', now())";
        $conn->query($insertDatabaseErrorSQL);

        //Redirect to error page
        header("Location: ../../error.php?errorCode=" . $conn->insert_id); die();
    }

    //Redirect
    header("Location: ../ssl.php"); die();