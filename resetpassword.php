<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <link rel="apple-touch-icon" sizes="76x76" href="./dashboard/assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="./dashboard/assets/img/favicon.png">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <title>
    Reset Password - Ultifree Hosting
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

        $fetchAnnouncements = "SELECT * FROM `announcements`";

        // Collect Information
        $allAnnouncements = $conn->query($fetchAnnouncements)->fetch_all(MYSQLI_ASSOC);
    ?>

    <div class="main-panel bg-muted">
      <div class="content">
        <div class="row">
          <div class="col-md-12">

            <?php
              session_start();

              $clientID = $verificationHash = "";
              $verify = false;
              if (isset($_GET['client']) && isset($_GET['resetCode'])) {
                  $fromSettingsPage = false;
                  if (isset($_SESSION['resetPassFromSettingsPage'])) {
                      if ($_SESSION['resetPassFromSettingsPage']) {
                          $fromSettingsPage = true;
                      }
                  }
                  $_SESSION['clientReset'] = $clientID = $conn->real_escape_string(test_input($_GET['client']));
                  $verificationHash = $conn->real_escape_string(test_input($_GET['resetCode']));
              } else {
                //Redirect
                header("Location: ../login.php"); die();
              }

              $getPassResetHashSQL = "SELECT `passReset` FROM `clients` WHERE `clientID` = '$clientID'";
              $getPassResetHashResult = $conn->query($getPassResetHashSQL)->fetch_assoc();
              $verificationHashDB = $getPassResetHashResult['passReset'];

              if ($verificationHashDB === $verificationHash) {
                $verify = true;
              }

              // Get Announcements and convert to HTML
              foreach($allAnnouncements as $row) {
                if ($row["location"] == "resetpassword") {
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

              $passwordErr = $errorBorder = "";
              $passwordsValid = $dbError = false;

              if ($_SERVER["REQUEST_METHOD"] == "POST") {
                  if (empty($_POST["password"])) {
                      $passwordErr = "A new password is required";
                  } else {
                      $password = $conn->real_escape_string(test_input($_POST["password"]));

                      if (empty($_POST["confirmPassword"])) {
                          $passwordErr = "Confirming the password is required";
                      } else {
                          $confPassword = $conn->real_escape_string(test_input($_POST["confirmPassword"]));

                          if ($password != $confPassword) {
                              $passwordErr = "New passwords don't match";
                          } else {
                              if ($fromSettingsPage) {
                                  $currentPassword = $conn->real_escape_string(test_input($_POST["currentPassword"]));

                                  if (empty($currentPassword)) {
                                      $passwordErr = "Current password is required";
                                  } else {

                                      $currentPasswordHashSQL = "SELECT `password` FROM `clients` WHERE `clientID` = '$clientID'";
                                      $currentPasswordHashResult = $conn->query($currentPasswordHashSQL);

                                      $passwordHash = $currentPasswordHashResult->fetch_assoc()['password'];

                                      if (password_verify($currentPassword, $passwordHash)) {
                                          $_SESSION['resetPassFromSettingsPage'] = false;

                                          changePassword($conn, $clientID, $password);
                                      } else {
                                          $passwordErr = "Current password is incorrect";
                                      }
                                  }
                              } else {
                                  changePassword($conn, $clientID, $password);
                              }
                          }
                      }
                  }
              }

              function changePassword($conn, $clientID, $password) {
                  $hashedPass = $conn->real_escape_string(password_hash($password, PASSWORD_DEFAULT));

                  $changePasswordSQL = "UPDATE `clients` SET `password` = '$hashedPass' WHERE `clientID` = '$clientID'";
                  $changePasswordResult = $conn->query($changePasswordSQL);

                  $tempHash = md5(rand(0,1000));
                  $setTempHashSQL = "UPDATE `clients` SET `passReset` = '$tempHash' WHERE `clientID` = '$clientID'";
                  $conn->query($setTempHashSQL);

                  if (!$changePasswordResult) {
                      // Password Not Changed
                      $dbError = true;

                      // Database Error
                      $errorMessage = $conn->real_escape_string($conn->error);
                      $errorLocation = "Reset Password - Ultifree Hosting Account (Password DB Error)";

                      // Insert into DB (if possible)
                      $insertDatabaseErrorSQL = "INSERT INTO `errors` (`errorText`, `errorLocation`, `errorTime`) VALUES ('$errorMessage', '$errorLocation', now())";
                      $conn->query($insertDatabaseErrorSQL);

                      //Redirect to error page
                      header("Location: error.php?errorCode=" . $conn->insert_id); die();
                  } else {
                      // Update last password change date
                      $currTime = time();
                      $updatePasswordChangeDateSQL = "UPDATE `clients` SET `passChangedTimestamp` = '$currTime' WHERE `clientID` = '$clientID'";
                      $updatePasswordChangeDateResult = $conn->query($updatePasswordChangeDateSQL);

                      $passwordsValid = true;

                      if (!$updatePasswordChangeDateResult) {
                          // Database Error
                          $errorMessage = $conn->real_escape_string($conn->error);
                          $errorLocation = "Reset Password - Ultifree Hosting Account (PassChangedDate DB Error)";

                          // Insert into DB (if possible)
                          $insertDatabaseErrorSQL = "INSERT INTO `errors` (`errorText`, `errorLocation`, `errorTime`) VALUES ('$errorMessage', '$errorLocation', now())";
                          $conn->query($insertDatabaseErrorSQL);
                      }

                      // Redirect to login page
                      header("Location: login.php"); die();
                  }
              }
            ?>

            <div class="row">
                <div class="col-xl-2 offset-xl-1 col-md-12">
                    <?php insertAds('rpas01', false);?>
                </div>

                <div class="col-xl-4 col-md-8 offset-xl-1 offset-md-2">
                    <a href="index.php" class="text-center">
                        <img id="logo" class="mx-auto d-block p-2" src="./dashboard/assets/img/logo.png" alt="Ultifree Hosting Logo" />
                    </a>

                    <div class="card">
                        <div class="card-body ">
                            <h5 class="card-title text-center">Reset Your Password</h5>

                            <?php
                                if (($_SERVER["REQUEST_METHOD"] == "POST") && $passwordsValid) {
                                    // Successfully changed
                                    echo '<p class="text-center text-success">Your password has been successfully changed. You can now log into your account using your new password!</p>';
                                    echo '<p class="text-center"><a href="login.php">Log in now!</a></p>';
                                } else if ($verify OR (($_SERVER["REQUEST_METHOD"] == "POST") && !$passwordsValid)) {
                                    echo '
                                        <form action="' . basename($_SERVER['REQUEST_URI']) . '" method="post">';
                                    if ($fromSettingsPage) {
                                        echo '
                                            <div class="form-group">
                                                <label for="currentPassword">Current Password</label>
                                                <input type="password" class="form-control' . $errorBorder . '" id="currentPassword" name="currentPassword" placeholder="Enter your current password..." required>
                                            </div>
                                        ';
                                    }
                                    echo '
                                            <div class="form-group">
                                                <label for="password">New Password</label>
                                                <input type="password" class="form-control' . $errorBorder . '" id="password" name="password" placeholder="Enter your new password..." required>
                                            </div>
                                            <div class="form-group">
                                                <label for="confirmPassword">Confirm Password</label>
                                                <input type="password" class="form-control' . $errorBorder . '" id="confirmPassword" name="confirmPassword" placeholder="Enter your new password again..." required>
                                            </div>
                                            <small id="passwordError" class="form-text text-danger">' . $passwordErr . '</small>
                                            <button type="submit" class="btn btn-primary">Reset Password</button>
                                        </form>
                                    ';
                                    echo '<p class="text-center"><a href="login.php">Nevermind, I changed my mind!</a></p>';
                                } else if ($dbError) {
                                    echo '<p class="text-center text-danger">Something went wrong. Please try again.</p>';
                                    echo '<p class="text-center"><a href="forgotpassword.php">Try again!</a></p>';
                                } else {
                                    echo '<p class="text-center text-danger">The password reset link is expired. Please try again.</p>';
                                    echo '<p class="text-center"><a href="forgotpassword.php">Try again!</a></p>';
                                }
                            ?>
                        </div>
                    </div>
                </div>

                <div class="col-xl-2 offset-xl-1 col-md-12">
                    <?php insertAds('rpas02', false);?>
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
  <script src="./dashboard/assets/js/loggedOut.js"></script>
  <!--  Notifications Plugin    -->
  <script src="./dashboard/assets/js/plugins/bootstrap-notify.js"></script>
  <!-- Control Center for Now Ui Dashboard: parallax effects etc -->
  <script src="./dashboard/assets/js/paper-dashboard.js?v=2.0.1" type="text/javascript"></script>
</body>

</html>
