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

    $dn = array(
        "commonName" => $domain
    );

    // Generate a new private (and public) key pair
    $privateKey = openssl_pkey_new(array(
        "private_key_bits" => 2048,
        "private_key_type" => OPENSSL_KEYTYPE_RSA,
    ));

    // Generate a certificate signing request
    $csr = openssl_csr_new($dn, $privateKey, array('digest_alg' => 'sha256'));

    // Generate a self-signed cert, valid for 10 years
    $x509 = openssl_csr_sign($csr, null, $privateKey, $days=3653, array('digest_alg' => 'sha256'));

    // Save your CSR, self-signed cert and private key for later use
    // openssl_csr_export($csr, $csrout) and var_dump($csrout);
    openssl_x509_export($x509, $certString);
    openssl_pkey_export($privateKey, $privateKeyString);

    // Show any errors that occurred here
    while (($e = openssl_error_string()) !== false) {
        // Exception Error
        $errorMessage = $conn->real_escape_string($e);
        $errorLocation = "Create Self-Signed SSL Certificate (OpenSSL)";

        // Insert into DB (if possible)
        $insertDatabaseErrorSQL = "INSERT INTO `errors` (`errorText`, `errorLocation`, `errorTime`) VALUES ('$errorMessage', '$errorLocation', now())";
        $conn->query($insertDatabaseErrorSQL);

        //Redirect to error page
        header("Location: ../../error.php?errorCode=" . $conn->insert_id); die();
    }

    // Save into DB
    $insertCertificateInfoSQL = "INSERT INTO `certificates` (`clientID`,
                                                            `type`,
                                                            `domain`, 
                                                            `status`, 
                                                            `expireDate`, 
                                                            `privateKey`, 
                                                            `cert`) VALUES ('$user',
                                                            'self', 
                                                            '$domain', 
                                                            '1', 
                                                            now() + interval 3653 day,
                                                            '$privateKeyString',
                                                            '$certString')";
    $result = $conn->query($insertCertificateInfoSQL);

    if (!$result) {
        // Database Error
        $errorMessage = $conn->real_escape_string($conn->error);
        $errorLocation = "Create Self-Signed SSL Certificate (DB)";

        // Insert into DB (if possible)
        $insertDatabaseErrorSQL = "INSERT INTO `errors` (`errorText`, `errorLocation`, `errorTime`) VALUES ('$errorMessage', '$errorLocation', now())";
        $conn->query($insertDatabaseErrorSQL);

        //Redirect to error page
        header("Location: ../../error.php?errorCode=" . $conn->insert_id); die();
    } else {
        // Redirect to Next Page
        header("Location: ../ssl.php"); die();
    }