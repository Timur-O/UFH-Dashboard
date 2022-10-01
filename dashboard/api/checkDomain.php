<?php
    /**
     * @var mysqli $conn the DB Connection
     * @var InfinityFree\MofhClient\Client $mofhClient the iFastNet API connection
     * @var string $user the clientID
     */
    require_once('./securityCheck.php');

    // Get 
    $domainToCheck = test_input($_POST['domain']);

    $checkDomainRequest = $mofhClient->availability([
        'domain' => $domainToCheck
    ]);

    $checkDomainResponse = $checkDomainRequest->send();

    if ($checkDomainResponse->isSuccessful()) {
        // Domain is available for registration
        echo "true";
    } else {
        // Domain unavailable for registration
        echo "false";
    }