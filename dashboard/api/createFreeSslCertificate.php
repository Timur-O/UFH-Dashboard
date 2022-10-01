<?php
    /**
     * @var string $user The clientID of the current user
     * @var mysqli $conn The DB Object
     */
    require_once('securityCheck.php');

    if (!isset($_SESSION['sslDomain'])) {
        // Redirect back to picking domain for new certificate
        header("Location: ../createSslDomain.php"); die();
    }

    $domain = $_SESSION['sslDomain'];

    // Generate ID for Subdomain
    $uniqueID = uniqid();

    // Save into DB
    $insertCertificateInfoSQL = "INSERT INTO `certificates` (`clientID`, `type`, `domain`, `status`, `subdomainID`) VALUES ('$user', 'zero', '$domain', '0', '$uniqueID')";
    $result = $conn->query($insertCertificateInfoSQL);

    if (!$result) {
        // Database Error
        $errorMessage = $conn->real_escape_string($conn->error);
        $errorLocation = "Create Free SSL Certificate (DB)";

        // Insert into DB (if possible)
        $insertDatabaseErrorSQL = "INSERT INTO `errors` (`errorText`, `errorLocation`, `errorTime`) VALUES ('$errorMessage', '$errorLocation', now())";
        $conn->query($insertDatabaseErrorSQL);

        //Redirect to error page
        header("Location: ../../error.php?errorCode=" . $conn->insert_id); die();
    } else {
        // Redirect to Next Page
        header("Location: ../ssl.php"); die();
    }