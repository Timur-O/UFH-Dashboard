<?php
    /**
     * @var mysqli $conn The database connection
     */
    require_once 'dashboard/api/settings.php';
    session_start();

    $email = null;

    //Make object of Google API Client for call Google API
    $google_client = new Google_Client();
    //Set the OAuth 2.0 Client ID
    $google_client->setClientId('***REMOVED***');
    //Set the OAuth 2.0 Client Secret key
    $google_client->setClientSecret('***REMOVED***');
    //Set the OAuth 2.0 Redirect URI
    $google_client->setRedirectUri('https://app.ultifreehosting.com/sociallogin.php');
    $google_client->addScope('email');
    $google_client->addScope('profile');

    if(isset($_GET["code"])) {
        // Attempt to exchange code for a valid token
        $token = $google_client->fetchAccessTokenWithAuthCode($_GET["code"]);

        if (!isset($token['error'])) {
            // Set the access token used for requests
            $google_client->setAccessToken($token['access_token']);
            // Store "access_token" value in $_SESSION variable for future use.
            $_SESSION['access_token'] = $token['access_token'];
            // Create Object of Google Service OAuth 2 class
            $google_service = new Google_Service_Oauth2($google_client);
            // Get user profile data from google
            $data = $google_service->userinfo->get();

            if(!empty($data['email'])) {
                $email = $conn->real_escape_string($data['email']);

                // Check DB to see if account with email exists
                $getAccountWithEmailSQL = "SELECT * FROM `clients` WHERE `email` = '$email'";
                $getAccountWithEmailResult = $conn->query($getAccountWithEmailSQL);

                if ($getAccountWithEmailResult->num_rows === 0) {
                    // Account Doesn't Exist
                    $tempPassHash = $conn->real_escape_string(md5(rand(9999,9999999)));
                    $passChangeHash = $conn->real_escape_string(md5(rand(9999,9999999)));
                    $dateTermsAccepted = $conn->real_escape_string(date("dmY"));

                    $createAccountSQL = "INSERT INTO clients (email, password, dateTermsAccepted, verification, passReset, lastLoginIP) VALUES ('$email', '$tempPassHash', '$dateTermsAccepted', 'YES', '$passChangeHash', '{$conn->real_escape_string($_SERVER['REMOTE_ADDR'])}')";
                    $createAccountResult = $conn->query($createAccountSQL);

                    if (!$createAccountResult) {
                        // Cannot Create Account in DB
                        echo "Please try using a different login method. Thank you!";

                        // Database Error
                        $errorMessage = $conn->real_escape_string($conn->error);
                        $errorLocation = "Social Login - New Account DB Error";

                        // Insert into DB (if possible)
                        $insertDatabaseErrorSQL = "INSERT INTO `errors` (`errorText`, `errorLocation`, `errorTime`) VALUES ('$errorMessage', '$errorLocation', now())";
                        $conn->query($insertDatabaseErrorSQL);

                        header('location: login.php'); die();
                    }

                    $getClientIdSQL = "SELECT `clientID` FROM `clients` WHERE `email` = '$email'";
                    $getClientIdResult = $conn->query($getClientIdSQL)->fetch_assoc();

                    $clientID = $conn->real_escape_string($getClientIdResult['clientID']);
                    $_SESSION['user'] = $clientID;

                    $changePassLink = "resetpassword.php?client=" . $clientID . "&resetCode=" . $passChangeHash;

                    $_SESSION['loginTime'] = time();

                    //Redirect
                    header("Location: {$changePassLink}"); die();
                } else {
                    // Account Exists
                    $getClientIdSQL = "SELECT `clientID` FROM `clients` WHERE `email` = '$email'";
                    $getClientIdResult = $conn->query($getClientIdSQL)->fetch_assoc();

                    $clientID = $conn->real_escape_string($getClientIdResult['clientID']);
                    $_SESSION['user'] = $clientID;
                    $_SESSION['loginTime'] = time();

                    // Redirect
                    header("Location: dashboard/home.php"); die();
                }
            } else {
                // Cannot Access Email
                echo "Please try using a different login method. Thank you!";

                // Database Error
                $errorMessage = $conn->real_escape_string($conn->error);
                $errorLocation = "Social Login - Cannot Access Email (API Error)";

                // Insert into DB (if possible)
                $insertDatabaseErrorSQL = "INSERT INTO `errors` (`errorText`, `errorLocation`, `errorTime`) VALUES ('$errorMessage', '$errorLocation', now())";
                $conn->query($insertDatabaseErrorSQL);

                header('location: login.php'); die();
            }
        } else {
            echo "Please try using a different login method. Thank you!";

            // Database Error
            $errorMessage = $conn->real_escape_string($token['error']);
            $errorLocation = "Social Login - API Error";

            // Insert into DB (if possible)
            $insertDatabaseErrorSQL = "INSERT INTO `errors` (`errorText`, `errorLocation`, `errorTime`) VALUES ('$errorMessage', '$errorLocation', now())";
            $conn->query($insertDatabaseErrorSQL);

            header('location: login.php'); die();
        }
    }