<?php
    /**
     * @var mysqli $conn The DB Connection
     */
    require_once '../dashboard/api/settings.php';

    // Removes any hosting account numbers from ultifree hosting accounts if they don't exist in the accounts DB

    $sql = "SELECT `clientID`, `account1`, `account2`, `account3` FROM `clients`"; // In case of timeout add:  WHERE `clientID` BETWEEN 4000 AND 5000
    $fullResult = $conn->query($sql) or die($conn->error);

    while ($row = $fullResult->fetch_assoc()) {
        $account1 = $row['account1'];
        $account2 = $row['account2'];
        $account3 = $row['account3'];
        $clientID = $row['clientID'];

        if (!is_null($account1)) {
            $sql2 = "SELECT * FROM `accounts` WHERE `accountID` = $account1";
            $result2 = $conn->query($sql2);

            if ($result2->num_rows == 0) {
                $sql3 = "UPDATE `clients` SET `account1` = NULL WHERE `account1` = $account1";
                $conn->query($sql3);
            }
        }

        if (!is_null($account2)) {
            $sql2 = "SELECT * FROM `accounts` WHERE `accountID` = $account2";
            $result2 = $conn->query($sql2);

            if ($result2->num_rows == 0) {
                $sql3 = "UPDATE `clients` SET `account2` = NULL WHERE `account2` = $account2";
                $conn->query($sql3);
            }
        }

        if (!is_null($account3)) {
            $sql2 = "SELECT * FROM `accounts` WHERE `accountID` = $account3";
            $result2 = $conn->query($sql2);

            if ($result2->num_rows == 0) {
                $sql3 = "UPDATE `clients` SET `account3` = NULL WHERE `account3` = $account3";
                $conn->query($sql3);
            }
        }
    }