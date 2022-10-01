<?php
    /**
     * @var string $zeroSslId ID for ZeroSSL Credentials
     * @var string $zeroSslHmacKey Key for ZeroSSL Credentials
     * @var string $user The clientID of the current user
     * @var mysqli $conn The DB Object
     * @var string $cloudflareKey The Cloudflare Key
     * @var string $cloudflareEmail The Cloudflare Email
     * @var string $cloudflareZone The Cloudflare Zone
     */
    require_once('securityCheck.php');

    if (!isset($_POST['certificateID'])) {
        // Redirect back to picking domain for new certificate
        header("Location: ../ssl.php"); die();
    }

    $certificateID = $conn->real_escape_string($_POST['certificateID']);

    $getCertificateInfoSQL = "SELECT * FROM `certificates` WHERE `certificateID` LIKE '$certificateID'";
    $certificate = $conn->query($getCertificateInfoSQL)->fetch_assoc();

    use AcmePhp\Core\Challenge\Dns\DnsDataExtractor;
    use AcmePhp\Core\Http\Base64SafeEncoder;
    use AcmePhp\Core\Http\SecureHttpClientFactory;
    use AcmePhp\Core\Http\ServerErrorHandler;
    use AcmePhp\Core\AcmeClient;
    use AcmePhp\Ssl\CertificateRequest;
    use AcmePhp\Ssl\DistinguishedName;
    use AcmePhp\Ssl\Generator\KeyPairGenerator;
    use AcmePhp\Ssl\KeyPair;
    use AcmePhp\Ssl\PrivateKey;
    use AcmePhp\Ssl\PublicKey;
    use AcmePhp\Ssl\Parser\KeyParser;
    use AcmePhp\Ssl\Signer\CertificateRequestSigner;
    use AcmePhp\Ssl\Signer\DataSigner;
    use GuzzleHttp\Client as GuzzleHttpClient;

    $secureHttpClientFactory = new SecureHttpClientFactory(
        new GuzzleHttpClient(),
        new Base64SafeEncoder(),
        new KeyParser(),
        new DataSigner(),
        new ServerErrorHandler()
    );
    $keyPairGenerator = new KeyPairGenerator();

    // Get keys for issuing certificate requests
    $publicKeyPath = '../../config/keys/account.pub.pem';
    $privateKeyPath = '../../config/keys/account.pem';

    $publicKey = new PublicKey(file_get_contents($publicKeyPath));
    $privateKey = new PrivateKey(file_get_contents($privateKeyPath));

    $keyPair = new KeyPair($publicKey, $privateKey);

    // Create A Secure HttpClient
    $secureHttpClient = $secureHttpClientFactory->createSecureHttpClient($keyPair);

    $acmeClient = new AcmeClient($secureHttpClient, 'https://acme.zerossl.com/v2/DV90');

    // Register on Server (Only If New Keys) - Also create new HmacKey
    // $acmeClient->registerAccount('ultifreehosting@gmail.com', new \AcmePhp\Core\Protocol\ExternalAccount($zeroSslId, $zeroSslHmacKey));

    // Get Authorization Challenges
    $currOrder = $acmeClient->requestOrder(array($certificate['domain']));

    $authorizationChallenge = $currOrder->getAuthorizationChallenges($certificate['domain'])[1];

    $dnsExtractor = new DnsDataExtractor(new Base64SafeEncoder());
    $txtRecord = $dnsExtractor->getRecordValue($authorizationChallenge);

    // Cloudflare API creates TXT record on CNAME URL
    $key = new Cloudflare\API\Auth\APIToken($cloudflareKey);
    $adapter = new Cloudflare\API\Adapter\Guzzle($key);
    $dns = new Cloudflare\API\Endpoints\DNS($adapter);

    $addTxtRecordResult = $dns->addRecord($cloudflareZone, "TXT", $certificate['subdomainID'] . ".acme.ultifreehosting.com", $txtRecord, 0, false);

    if (!$addTxtRecordResult[0]) {
        // Cloudflare API Error
        $errorMessage = $conn->real_escape_string($conn->error);
        $errorLocation = "Verify Free SSL (Cloudflare API, Create Record)";

        // Insert into DB (if possible)
        $insertDatabaseErrorSQL = "INSERT INTO `errors` (`errorText`, `errorLocation`, `errorTime`) VALUES ('$errorMessage', '$errorLocation', now())";
        $conn->query($insertDatabaseErrorSQL);

        //Redirect to error page
        header("Location: ../../error.php?errorCode=" . $conn->insert_id); die();
    }

    try {
        $acmeClient->challengeAuthorization($authorizationChallenge, 180);

        // Remove TXT Record
        $dns->deleteRecord($cloudflareZone, $addTxtRecordResult[1]);

        $dn = new DistinguishedName($certificate['domain']);

        // Make a new key pair. We'll keep the private key as our cert key
        $domainKeyPair = $keyPairGenerator->generateKeyPair(new \AcmePhp\Ssl\Generator\RsaKey\RsaKeyOption(2048));

        $csr = new CertificateRequest($dn, $domainKeyPair);
        $certPrivateKey = $domainKeyPair->getPrivateKey()->getPem();

        $certificateResponse = $acmeClient->finalizeOrder($currOrder, $csr, 240);

        // The certificate (public key)
        $cert = $certificateResponse->getCertificate()->getPem();

        // For Let's Encrypt, client will need the intermediate too (Unnecessary for ZeroSSL)
        // $intermediate = $certificateResponse->getCertificate()->getIssuerCertificate()->getPEM();

        // Save to DB
        $saveCsrKeySQL = "UPDATE `certificates` SET `status` = '1',
                                                `expireDate` = now() + interval 90 day,
                                                `privateKey` = '$certPrivateKey',
                                                `cert` = '$cert' WHERE `certificateID` LIKE '$certificateID'";
        $saveCsrKeyResult = $conn->query($saveCsrKeySQL);

        if (!$saveCsrKeyResult) {
            // Database Error
            $errorMessage = $conn->real_escape_string($conn->error);
            $errorLocation = "Verify Free SSL (DB)";

            // Insert into DB (if possible)
            $insertDatabaseErrorSQL = "INSERT INTO `errors` (`errorText`, `errorLocation`, `errorTime`) VALUES ('$errorMessage', '$errorLocation', now())";
            $conn->query($insertDatabaseErrorSQL);

            //Redirect to error page
            header("Location: ../../error.php?errorCode=" . $conn->insert_id); die();
        } else {
            // Redirect back to main SSL Page
            header("Location: ../ssl.php");
        }
    } catch (Exception $e) {
        // Remove TXT Record
        $dns->deleteRecord($cloudflareZone, $addTxtRecordResult[1]);

        // Exception Error
        $errorMessage = $conn->real_escape_string($e->getMessage());
        $errorLocation = "Verify SSL (Exception)";

        // Insert into DB (if possible)
        $insertExceptionErrorSQL = "INSERT INTO `errors` (`errorText`, `errorLocation`, `errorTime`) VALUES ('$errorMessage', '$errorLocation', now())";
        $conn->query($insertExceptionErrorSQL);

        //Redirect to error page
        header("Location: ../../error.php?errorCode=" . $conn->insert_id); die();
    }