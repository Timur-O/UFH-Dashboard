<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <link rel="apple-touch-icon" sizes="76x76" href="./dashboard/assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="./dashboard/assets/img/favicon.png">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <title>
    Verify Your Email - Ultifree Hosting
  </title>
  <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
  <!--     Fonts and icons     -->
  <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700,200" rel="stylesheet" />
  <link href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css" rel="stylesheet">
  <!-- CSS Files -->
  <link href="./dashboard/assets/css/bootstrap.min.css" rel="stylesheet" />
  <link href="./dashboard/assets/css/paper-dashboard.css?v=2.0.1" rel="stylesheet" />
  <link href="./dashboard/assets/css/login.css" rel="stylesheet" />
</head>

<body class="">
  <div class="wrapper ">

    <?php
        /**
         * @var mysqli $conn the DB Connection
         */
        require_once('dashboard/api/settings.php');
        require_once('ad_inserter.php');
        session_start();

        $fetchAnnouncements = "SELECT * FROM `announcements`";

        // Collect Information
        $allAnnouncements = $conn->query($fetchAnnouncements)->fetch_all(MYSQLI_ASSOC);

        $verified = false;

        if (isset($_GET['client']) && isset($_GET['verificationCode'])) {
            $clientID = $conn->real_escape_string($_GET['client']);
            $verificationHash = $conn->real_escape_string($_GET['verificationCode']);

            $sql = "SELECT `verification` FROM `clients` WHERE `clientID` = '$clientID'";
            $result = $conn->query($sql)->fetch_assoc();
            $verificationHashDB = $result['verification'];

            if ($verificationHashDB == $verificationHash) {
                $verified = true;
            }

            if ($verified) {
                $sql = "UPDATE `clients` SET `verification` = 'YES' WHERE `clientID` = '$clientID'";
                $result = $conn->query($sql);

                if (isset($_COOKIE['_apjsu'])) {
                    // Unset Affiliate Cookie
                    unset($_COOKIE['_apjsu']);
                    setcookie('_apjsu', null, -1, '/');

                    $conversionType = 'Free Signup';
                    $conversionValue = 0.5;

                    if (isset($_COOKIE['ref'])) {
                        $referralCode = $conn->real_escape_string($_COOKIE['ref']);

                        $sql = "UPDATE `affiliates` SET `conversions` =  `conversions` + 1 WHERE `affiliateID` = '$referralCode'";
                        $conn->query($sql);

                        $vpnProxyValue = 0;
                        $countryValue = "Check Failed";
                        cloudflareIPRewrite();
                        $ipAddress = $_SERVER['REMOTE_ADDR'];

                        $curl = curl_init();

                        curl_setopt($curl, CURLOPT_URL, 'http://v2.api.iphub.info/ip/' . $ipAddress);
                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                            'X-Key: MTE3ODQ6Z1ZnaG1mWnQ3M2FQYndXa3QzSHVmMXhnRkQyZGNCc3k='
                        ));

                        $resultObject = json_decode(curl_exec($curl));
                        $vpnProxyValue = $resultObject->block;
                        $countryValue = $resultObject->countryName;

                        curl_close($curl);

                        if ($vpnProxyValue == 1) {
                            $autoApprove = 2;
                            $note = "Auto Rejection (VPN/Proxy)";
                        } else if ($vpnProxyValue == 2) {
                            $autoApprove = 0;
                            $note = "Manual Review Required";
                        } else {
                            $autoApprove = 0;
                            $note = "Seems Alright";
                        }

                        $sql2 = "INSERT INTO  `conversions` (`affiliate`, `date`, `type`, `commissionAmount`, `referredClientID`, `approved`, `note`, `httpReferer`, `ipAddress`, `ipProxyAddress`, `country`, `blockValue`) VALUES ($referralCode, now(), '$conversionType', '$conversionValue', '$clientID', '$autoApprove', '$note', '{$conn->real_escape_string($_COOKIE['_apurl'])}', '{$conn->real_escape_string($_SERVER['REMOTE_ADDR'])}', '{$conn->real_escape_string($_SERVER['HTTP_X_FORWARDED_FOR'])}', '$countryValue', '$vpnProxyValue')";
                        $conn->query($sql2);
                    }
                }
            }
        }
    ?>

    <div class="main-panel bg-muted">
      <div class="content">
        <div class="row">
          <div class="col-md-12">

            <?php
              // Get Announcements and convert to HTML
              foreach($allAnnouncements as $row) {
                if ($row["location"] == "verified") {
                  echo '<div class="alert alert-info alert-dismissible fade show" data-notify="container">';
                    echo '<button type="button" aria-hidden="true" class="close" data-dismiss="alert" aria-label="Close">';
                      echo '<i class="nc-icon nc-simple-remove"></i>';
                    echo '</button>';
                    echo '<span data-notify="message">';
                      echo '<strong>' . $row["title"] . '</strong> - ';
                      echo $row["text"];
                      echo $row["extra"];
                    echo '</span>';
                  echo '</div>';
                }
              }
            ?>

            <div class="row">
                <div class="col-xl-2 offset-xl-1 col-md-12">
                    <?php insertAds('sver01', false);?>
                </div>

                <div class="col-xl-4 col-md-8 offset-xl-1 offset-md-2">
                    <a href="index.php" class="text-center">
                        <img id="logo" class="mx-auto d-block p-2" src="./dashboard/assets/img/logo.png" alt="Ultifree Hosting Logo" />
                    </a>

                    <div class="card">
                        <div class="card-body ">
                            <p class="text-center">
                                <?php
                                    if ($verified) {
                                        echo "Your email is now verified! Now all that's left is to login to your account!";
                                    } else {
                                        echo "An error occurred during the verification of your email address. Please try again.";
                                    }
                                ?>
                            </p>

                            <p class="text-center"><a href="login.php">Login to your account!</a></p>
                        </div>
                    </div>
                </div>

                <div class="col-xl-2 offset-xl-1 col-md-12">
                    <?php insertAds('sver02', false);?>
                </div>
            </div>

          </div>
        </div>
      </div>

    </div>
  </div>
  <!--   Core JS Files   -->
  <script src="./dashboard/assets/js/core/jquery.min.js"></script>
  <script src="./dashboard/assets/js/core/popper.min.js"></script>
  <script src="./dashboard/assets/js/core/bootstrap.min.js"></script>
  <!-- Custom JS Files -->
  <script src="./dashboard/assets/js/loggedOut.js" <?php if ($verified) { echo 'onload="function completeVerificationEventWhenReady() { if (typeof CompleteVerificationEvent != \'undefined\') { CompleteVerificationEvent(); } else { setTimeout(completeVerificationEventWhenReady, 100); } } completeVerificationEventWhenReady();"'; } ?>></script>
  <!--  Notifications Plugin    -->
  <script src="./dashboard/assets/js/plugins/bootstrap-notify.js"></script>
  <!-- Control Center for Now Ui Dashboard: parallax effects etc -->
  <script src="./dashboard/assets/js/paper-dashboard.js?v=2.0.1" type="text/javascript"></script>
</body>

</html>
