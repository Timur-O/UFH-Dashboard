<?php

    if(!isset($_SESSION)) {
        session_start();
    }

    // Session Fixation Safeguard
    if (!isset($_SESSION['initialized'])) {
        session_regenerate_id();
        $_SESSION['initialized'] = true;
    }

    if (!isset($_SESSION['connectedByAdmin']) || $_SESSION['connectedByAdmin'] == false) {
        // Session Hijacking Safeguard
        $seed = "SuperSecretSeed";
        if (isset($_SESSION['HTTP_USER_AGENT'])) {
            if ($_SESSION['HTTP_USER_AGENT'] != md5($_SERVER['HTTP_USER_AGENT'] . $seed)) {
                // Redirect to login
                session_destroy();
                header("Location: ../login.php"); die();
            }
        } else {
            $_SESSION['HTTP_USER_AGENT'] = md5($_SERVER['HTTP_USER_AGENT'] . $seed);
        }
    }

    /**
     * @var mysqli $conn the DB Connection
     */
    // Check if in the API or Dashboard folder (due to includes)
    $currPath = getcwd();
    if (str_contains($currPath, "api") !== false) {
        // Settings - Api Folder
        require_once './settings.php';
    } else {
        // Settings - Dashboard Folder
        require_once './api/settings.php';
    }

    if (isset($_SESSION['user'])) {
        $user = test_input($_SESSION['user']);

        // Session Takeover Safeguard
        $getLastPasswordChangeTimeSQL = "SELECT `passChangedTimestamp` FROM `clients` WHERE `clientID` = '$user'";
        $getLastPasswordChangeTimeResult = $conn->query($getLastPasswordChangeTimeSQL)->fetch_assoc();

        $lastPasswordChangeTime = $getLastPasswordChangeTimeResult['passChangedTimestamp'];

        if (($_SESSION['loginTime'] - $lastPasswordChangeTime) < 0) {
            session_destroy();

            // Check if in the API or Dashboard folder (due to includes)
            $currPath = getcwd();
            if (str_contains($currPath, "api") !== false) {
                // Settings - Api Folder
                // If not logged in -> Redirect to login page
                header("Location: ../../login.php"); die();
            } else {
                // Settings - Dashboard Folder
                // If not logged in -> Redirect to login page
                header("Location: ../login.php"); die();
            }
        }
    } else {
        // Check if in the API or Dashboard folder (due to includes)
        $currPath = getcwd();
        if (str_contains($currPath, "api") !== false) {
            // Settings - Api Folder
            // If not logged in -> Redirect to login page
            header("Location: ../../login.php"); die();
        } else {
            // Settings - Dashboard Folder
            // If not logged in -> Redirect to login page
            header("Location: ../login.php"); die();
        }
    }

    if (isset($_SESSION['affiliateLogin']) && $_SESSION['affiliateLogin'] == true) {
        header("Location: https://affiliates.ultifreehosting.com/index.php");
    }

    if (isset($_SESSION['adminLogin']) && $_SESSION['adminLogin'] == true) {
        header("Location: https://admin.ultifreehosting.com/index.php");
    }