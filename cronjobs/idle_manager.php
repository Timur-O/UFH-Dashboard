<?php
    /**
     * @var mysqli $conn The DB Connection
     */
    require_once '../dashboard/api/settings.php';

    // Deletes ultifree hosting accounts if they are idle for 31+ days AND have no hosting accounts attached to them

    $sql = "SELECT `clientID`, `account1`, `account2`, `account3`, `lastLogin` FROM `clients`";
    $fullResult = $conn->query($sql);

    $currentDate = time();

    while ($row = $fullResult->fetch_assoc()) {
      $clientID = $row['clientID'];
      $account1 = $row['account1'];
      $account2 = $row['account2'];
      $account3 = $row['account3'];
      $lastLogin = strtotime($row['lastLogin']);
      if (($account1 == NULL) && ($account2 == NULL) && ($account3 == NULL) && (($currentDate - $lastLogin) > 2678400)) { // 2678400 = 31 Days (OR 5184000 = 60 Days)
        $clientIDSQL = $conn->real_escape_string($clientID);
        $sql = "DELETE FROM `clients` WHERE `clientID` = '{$clientIDSQL}'";
        $conn->query($sql);
      }
      $account1 = $account2 = $account3 = $lastLogin = $clientID = $clientIDSQL = "";
    }