<?php
    /**
     * @var mysqli $conn The DB Connection Object
     */
    require_once '../dashboard/api/settings.php';

    // Removes the suspensions for permanently deleted hosting accounts

    $sql = "SELECT `username` FROM `suspensions` GROUP BY `username`";
    $fullResult = $conn->query($sql);

    while ($row = $fullResult->fetch_assoc()) {
        $username = $row['username'];

        $sql = "SELECT `username` FROM `accounts` WHERE `username` = '$username'";
        $result = $conn->query($sql);

        if ($result->num_rows == 0) {
            $sql = "DELETE FROM `suspensions` WHERE `username` = '$username'";
            $conn->query($sql);
        }
    }