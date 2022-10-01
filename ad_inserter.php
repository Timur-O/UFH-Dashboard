<?php
    /**
     * @var mysqli $conn The DB Connection
     */

    affiliateDetection($conn);

    $sql = "SELECT `location`, `adCode` FROM `ads`";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
      $numberOfAds = $result->num_rows;
      while ($row = $result->fetch_assoc()) {
        $temp = $row['location'];
        $results[$temp] = $row['adCode'];
      }
    }

    function insertAds($givenLocation, $padding)
    {
        global $results;
        foreach ($results as $location => $adCode) {
          if ($givenLocation == $location) {
            if ($padding) {
              echo "<div class='leaderad_container' style='margin-bottom: -5%;padding-top: 3%;'>" . $adCode . "</div>";
              echo '<div class="banner-funding" style="margin-bottom: -5%;margin-top: 5%;">
                  Ultifree Hosting is made possible by displaying online advertisements to our visitors.<br>
                  Please consider supporting us by disabling your ad blocker on our website.
                  </div>';
            } else {
              echo "<div class='leaderad_container'>" . $adCode . "</div>";
              echo '<div class="banner-funding">
                  Ultifree Hosting is made possible by displaying online advertisements to our visitors.<br>
                  Please consider supporting us by disabling your ad blocker on our website.
                  </div>';
            }
          }
        }
    }

    function affiliateDetection($conn) {
      $affiliatesTableName = 'affiliates';

      if (isset($_GET['ref'])) {
          $referralCode = $_GET['ref'];

          $sql = "UPDATE `$affiliatesTableName` SET `clicks` =  `clicks` + 1 WHERE `affiliateID` = $referralCode";
          $conn->query($sql);

          setcookie('ref', $referralCode, time() + (86400 * 30), "/"); // 86400 = 1 day * 30 = 30 Days
          $httpReferer = $_SERVER['HTTP_REFERER'];
          if ($httpReferer == "") {
            $httpReferer = "Direct";
          }

          setcookie('_apurl', $httpReferer, time() + (86400 * 30), "/");
          header("Location: index.php"); die();
      }
    }