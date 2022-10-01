<?php
    require_once('./securityCheck.php');

    if (isset($_GET['client']) && isset($_GET['resetCode'])) {
        $_SESSION['resetPassFromSettingsPage'] = true;
        // Redirect to password changing page
        header("Location: ../../resetpassword.php?client=" . test_input($_GET['client']) . "&resetCode=" . test_input($_GET['resetCode'])); die();
    } else {
        // Redirect back to settings
        header("Location: ../settings.php"); die();
    }