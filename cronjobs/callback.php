<?php
  /**
   * @var mysqli $conn The DB Connection
   */
  require_once '../dashboard/api/settings.php';

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string($_POST['username']);
    $status = $conn->real_escape_string($_POST['status']);
    $comments = $conn->real_escape_string($_POST['comments']);

    $sql = "INSERT INTO `callbacks` (`username`, `status`, `comments`) VALUES ('$username', '$status', '$comments')";
    $conn->query($sql);

    switch($status) {
      case 'ACTIVATED':
        $sql = "UPDATE `accounts` SET `status` = 'A' WHERE `username` = '$username'";
        $result = $conn->query($sql);
        break;
      case 'REACTIVATE':
        $sql = "UPDATE `accounts` SET `status` = 'A' WHERE `username` = '$username'";
        $result = $conn->query($sql);

        $date = date("d-m-Y");
        $time = date("H:i");
        $dateAndTime = $date . " " . $time;
        $sql = "UPDATE `suspensions` SET `reactivationDate` = '$dateAndTime', `suspensionStatus` = 'INACTIVE' WHERE `username` = '$username' ORDER BY `suspensionID` DESC LIMIT 1";
        $result = $conn->query($sql);
        break;
      case 'SUSPENDED':
        $date = date("d-m-Y");
        $time = date("H:i");
        $dandt = $date . " " . $time;
        if (str_starts_with($comments, "RES_CLOSE : DELETION")) {
          // Deleted through control panel
          $reason = "Requested Deletion.";
          $sql = "INSERT INTO `suspensions` (`username`, `reason`, `date`) VALUES ('$username', '$reason', '$dandt')";
          $result = $conn->query($sql);
          $sql = "UPDATE `accounts` SET `status` = 'D' WHERE `username` = '$username'";
          $result = $conn->query($sql);
        } else if (str_starts_with($comments, "AUTO_IDLE;")) {
          // Account has been idle so automatically deactivated
          $reason = "Account Unused for Extended Period of Time.";
          $sql = "INSERT INTO `suspensions` (`username`, `reason`, `date`) VALUES ('$username', '$reason', '$dandt')";
          $result = $conn->query($sql);
          $sql = "UPDATE `accounts` SET `status` = 'D' WHERE `username` = '$username'";
          $result = $conn->query($sql);
        } else {
          // Usually admin suspension / MOFH Abuse System
          if (str_contains($comments, "PHISHING") ||
              str_contains($comments, "LINKED_PHISH_mail") ||
              str_contains($comments, "linked to phished") ||
              str_contains($comments, "SOCIAL_ENGINEERING")) {
              $reason = "TOS Violation (Phishing) - Permanent Suspension.";
          } else if (str_contains($comments, "ABUSE_COMPLAINT")) {
              $reason = "Abuse Complaint - Permanent Suspension.";
          } else if (str_contains($comments, "FAUCET SITE")) {
              $reason = "TOS Violation (Faucet Site) - Permanent Suspension.";
          } else if (str_contains($comments, "MYSQL_OVERLOAD") ||
                     str_contains($comments, "mysql_time_counters")) {
              $reason = "Overloaded MySQL - 24 Hour Suspension.";
          } else if (str_contains($comments, "DAILY_HIT")) {
              $reason = "Daily Hit Limit Reached - 24 Hour Suspension.";
          } else if (str_contains($comments, "DAILY_ io")) {
              $reason = "Daily IO (PHP Read/Write) Limit Reached - 24 Hour Suspension.";
          } else if (str_contains($comments, "DAILY_ ep")) {
              $reason = "Daily EP (Entry Process, PHP Requests) Limit Reached - 24 Hour Suspension.";
          } else if (str_contains($comments, "DAILY_ cpu")) {
              $reason = "Daily CPU Limit Reached - 24 Hour Suspension.";
          } else if (str_contains($comments, "BAD") ||
                     str_contains($comments, "RUNNING DDOS / DOS SCRIPTS") ||
                     str_contains($comments, "SHELLD/HACKED SITE") ||
                     str_contains($comments, "CHAN CONTENT")) {
              $reason = "TOS Violation - Permanent Suspension.";
          } else {
              $reason = substr($comments, 13);
          }

          $sql = "INSERT INTO `suspensions` (`username`, `reason`, `date`) VALUES ('$username', '$reason', '$dandt')";
          $result = $conn->query($sql);

          $sql = "UPDATE `accounts` SET `status` = 'S' WHERE `username` = '$username'";
          $result = $conn->query($sql);
        }
        break;
      case "DELETE":
        $sql = "SELECT `accountID`, `clientID` FROM `accounts` WHERE `username` = '$username'";
        $result = $conn->query($sql)->fetch_assoc();
        $accountID = $result['accountID'];
        $clientID = $result['clientID'];

        $sql2 = "SELECT `account1`, `account2`, `account3` FROM `clients` WHERE `clientID` = '$clientID'";
        $result2 = $conn->query($sql2)->fetch_assoc();
        $account1 = $result2['account1'];
        $account2 = $result2['account2'];
        $account3 = $result2['account3'];
        for ($x = 1; $x <= 3; $x++) {
          if (${'account' . $x} == $accountID) {
            $accToSet = 'account' . $x;
            $sql3 = "UPDATE `clients` SET `$accToSet` = NULL WHERE `clientID` = '$clientID'";
            $result3 = $conn->query($sql3);
          }
        }

        $sql4 = "DELETE FROM `accounts` WHERE `accountID` = '$accountID'";
        $result = $conn->query($sql4);
        break;
      default:
        if ($comments == "SQL_SERVER") {
          $sql = "UPDATE `accounts` SET `sqlServer` = '$status' WHERE `username` = '$username'";
          $result = $conn->query($sql);
        }
        break;
    }
  }

  die();