<?php
    require_once 'vendor/autoload.php';
    //Google Social Logout
    //Make object of Google API Client for call Google API
    $google_client = new Google_Client();
    //Set the OAuth 2.0 Client ID
    $google_client->setClientId('***REMOVED***');
    //Set the OAuth 2.0 Client Secret key
    $google_client->setClientSecret('***REMOVED***');
    //Set the OAuth 2.0 Redirect URI
    $google_client->setRedirectUri('https://ultifreehosting.com/sociallogin.php');
    $google_client->addScope('email');
    $google_client->addScope('profile');

    session_start();

    // Google Login End
    $google_client->revokeToken();

    // Reset Remember Me
    setcookie("user", "", time()-3600, "/");
    setcookie("remKey", "", time()-3600, "/");

    //Redirect
    if (isset($_SESSION['connectedByAdmin'])) {
        if ($_SESSION['connectedByAdmin']) {
            $tempAdminValue = $_SESSION['adminUser'];
            session_destroy();
            session_start();
            $_SESSION['adminUser'] = $tempAdminValue;
            header("Location: https://admin.ultifreehosting.com/manageusers.php"); die();
        } else {
            session_destroy();
            header("Location: login.php"); die();
        }
    } else {
        session_destroy();
        header("Location: login.php"); die();
    }