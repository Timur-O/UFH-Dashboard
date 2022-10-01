<?php
    /**
     * @var string $siteProUsername The username for the SitePro API
     * @var string $siteProKey The key for the SitePro API
     * @var mysqli $conn The DB object
     * @var InfinityFree\MofhClient\Client $mofhClient The MOFH API Object
     */
    require_once 'securityCheck.php';
    include '../../vendor/SiteProApiClient.php';

    use Profis\SitePro\SiteProApiClient;

    $siteProApi = new SiteProApiClient('https://site.pro/api/', $siteProUsername, $siteProKey);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $domain = $conn->real_escape_string($_POST['domainToEdit']);
        $ftpUsername = $conn->real_escape_string($_POST['ftpUser']);
        $ftpPassword = $conn->real_escape_string($_POST['ftpPass']);

        $getDomainInfoRequest = $mofhClient->getDomainUser([
            'domain' => $domain
        ]);
        $getDomainInfoResponse = $getDomainInfoRequest->send();

        if ($getDomainInfoResponse->isSuccessful()) {
            $homeDirectory = $getDomainInfoResponse->getDocumentRoot();

            $explodedHomeDirectory = explode("/", $homeDirectory);
            $shortHomeDirectory = implode("/", array_slice($explodedHomeDirectory, 5, sizeof($explodedHomeDirectory)));

            try {
                $siteProResponse = $siteProApi->remoteCall('requestLogin', array(
                    'type' => 'external',
                    'domain' => $domain,
                    'lang' => 'en',
                    'username' => $ftpUsername,	// FTP Username
                    'password' => $ftpPassword,	// FTP Password
                    'apiUrl' => 'ftp.ultihost.net',			            // FTP Hostname
                    'uploadDir' => $shortHomeDirectory,	                // FTP Directory
                    'panel' => 'Ultifree Hosting Panel'
                ));
                if (!$siteProResponse || !is_object($siteProResponse)) {
                    // Caught and handled in catch field
                    throw new ErrorException('Response Format Error');
                } else if (isset($siteProResponse->url) && $siteProResponse->url) {
                    header("Location: " . $siteProResponse->url); die();
                } else {
                    // Caught and handled in catch field
                    throw new ErrorException((isset($siteProResponse->error->message) && $siteProResponse->error->message) ? $siteProResponse->error->message : 'Unknown Error - ' . print_r($siteProResponse, true) . ' - Username: ' . $ftpUsername . ', Domain: ' . $domain . ', Home Dir: ' . $shortHomeDirectory);
                }
            } catch (Exception $e) {
                // Exception Error
                $errorMessage = $conn->real_escape_string($e->getMessage());
                $errorLocation = "Generate Site.Pro Link - Exception Error";

                // Insert into DB (if possible)
                $insertDatabaseErrorSQL = "INSERT INTO `errors` (`errorText`, `errorLocation`, `errorTime`) VALUES ('$errorMessage', '$errorLocation', now())";
                $conn->query($insertDatabaseErrorSQL);

                //Redirect to error page
                header("Location: ../../error.php?errorCode=" . $conn->insert_id); die();
            }
        } else {
            // MOFH API Error
            $errorMessage = $conn->real_escape_string($getDomainInfoResponse->getMessage());
            $errorLocation = "Generate Site.Pro Link - MOFH API";

            // Insert into DB (if possible)
            $insertDatabaseErrorSQL = "INSERT INTO `errors` (`errorText`, `errorLocation`, `errorTime`) VALUES ('$errorMessage', '$errorLocation', now())";
            $conn->query($insertDatabaseErrorSQL);

            //Redirect to error page
            header("Location: ../../error.php?errorCode=" . $conn->insert_id); die();
        }
    } else {
        // Redirect to login page
        header("Location: ../../login.php"); die();
    }