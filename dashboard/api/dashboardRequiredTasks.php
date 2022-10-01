<?php
    /**
     * @var mysqli $conn the DB Connection
     * @var InfinityFree\MofhClient\Client $mofhClient the iFastNet API connection
     * @var string $user the clientID
     * @var string $verification variable from dashboardDataFetch (if email verified)
     * @var int $numberOfAccounts variable from dashboardDataFetch (number of accounts)
     * @var boolean $autoLogoutOn variable from dashboardDataFetch (if auto logout is on)
     */
    require_once("./api/dashboardDataFetch.php");

    // Save the User's IP and last login time (current time) - Except when admin is connecting to account
    if (!isset($_SESSION['connectedByAdmin']) || !$_SESSION['connectedByAdmin']) {
        cloudflareIPRewrite();
        $updateLastLoginAndIP = "UPDATE `clients` SET `lastLogin` = now(), `lastLoginIP` = '{$_SERVER['REMOTE_ADDR']}' WHERE `clientID` = '$user'";
        $conn->query($updateLastLoginAndIP);
    }

    // Share verification with JavaScript
    if ($verification != "YES") {
        echo "<script>emailVerified = false; numberOfAccounts = " . $numberOfAccounts . "; autoLogoutOn = " . $autoLogoutOn . ";</script>";
    } else {
        echo "<script>emailVerified = true; numberOfAccounts = " . $numberOfAccounts . "; autoLogoutOn = " . $autoLogoutOn . ";</script>";
    }

