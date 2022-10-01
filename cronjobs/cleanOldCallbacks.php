<?php
    /**
     * @var mysqli $conn The DB Connection Object
     */
    require_once '../dashboard/api/settings.php';

    // Removes the callbacks for permanently deleted hosting accounts

    $sql = "SELECT `username`, `status` FROM `callbacks`";
    $fullResult = $conn->query($sql);

    while ($row = $fullResult->fetch_assoc()) {
        $username = $row['username'];
        $status = $row['status'];

        $sql = "SELECT `username` FROM `accounts` WHERE `username` = '$username'";
        $result = $conn->query($sql);

        if ($result->num_rows == 0) {
            $sql = "DELETE FROM `callbacks` WHERE `username` = '$username' AND `status` = '$status'";
            $conn->query($sql);
        }
    }