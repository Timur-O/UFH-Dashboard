<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <link rel="apple-touch-icon" sizes="76x76" href="./dashboard/assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="./dashboard/assets/img/favicon.png">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <title>
    Signup - Ultifree Hosting
  </title>
  <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
  <!--     Fonts and icons     -->
  <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700,200" rel="stylesheet" />
  <link href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css" rel="stylesheet">
  <!-- CSS Files -->
  <link href="./dashboard/assets/css/bootstrap.min.css" rel="stylesheet" />
  <link href="./dashboard/assets/css/paper-dashboard.css?v=2.0.1" rel="stylesheet" />
  <link href="./dashboard/assets/css/login.css" rel="stylesheet" />
  <!-- hCaptcha -->
  <script src='https://js.hCaptcha.com/1/api.js' async defer></script>
</head>

<body class="">
  <div class="wrapper ">

    <?php
        /**
         * @var mysqli $conn the DB Connection
         * @var string $googleClientId the client id for Google authentication
         * @var string $googleClientSecret the client secret for Google authentication
         */
        require_once('dashboard/api/settings.php');
        require_once('ad_inserter.php');
        session_start();

        //Make object of Google API Client for call Google API
        $google_client = new Google_Client();
        //Set the OAuth 2.0 Client ID
        $google_client->setClientId($googleClientId);
        //Set the OAuth 2.0 Client Secret key
        $google_client->setClientSecret($googleClientSecret);
        //Set the OAuth 2.0 Redirect URI
        $google_client->setRedirectUri('https://' . $_SERVER['SERVER_NAME'] . '/sociallogin.php');
        $google_client->addScope('email');
        $google_client->addScope('profile');

        $google_login_button = '<a href="'.$google_client->createAuthUrl().'" onclick="LoginEvent(\'Social - Google\');"><img id="google_login_button" src="dashboard/assets/img/google_login.png" /></a>';

        $fetchAnnouncements = "SELECT * FROM `announcements`";

        // Collect Information
        $allAnnouncements = $conn->query($fetchAnnouncements)->fetch_all(MYSQLI_ASSOC);
    ?>

    <div class="main-panel bg-muted">
      <div class="content">
        <div class="row">
          <div class="col-md-12">

            <?php
              // Get Announcements and convert to HTML
              foreach($allAnnouncements as $row) {
                if ($row["location"] == "signup") {
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

              $emailError = $passwordError = $confPasswordError = $tosError = $captchaError = $emailBorder = $passwordBorder = $confPasswordBorder = "";
              $passwordsValid = $termsAccepted = $emailValid = $captchaValid = false;

              if ($_SERVER["REQUEST_METHOD"] == "POST") {
                  if(isset($_POST['h-captcha-response']) && !empty($_POST['h-captcha-response'])) {
                      $data = array(
                          'secret' => "0x88Cb816997D56dDd57538d22d9CbceEd3893542e",
                          'response' => $_POST['h-captcha-response']
                      );
                      $verify = curl_init();
                      curl_setopt($verify, CURLOPT_URL, "https://hcaptcha.com/siteverify");
                      curl_setopt($verify, CURLOPT_POST, true);
                      curl_setopt($verify, CURLOPT_POSTFIELDS, http_build_query($data));
                      curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
                      $response = curl_exec($verify);

                      $responseData = json_decode($response, true);

                      if($responseData['success'] == 1) {
                          $captchaValid = true;
                      } else {
                          $captchaError = "Captcha verification failed. Please try again";
                      }
                  } else {
                      $captchaError = "Please fill out the captcha";
                  }

                  if (empty($_POST["email"])) {
                      $emailError = "Email is required";
                      $emailBorder = "border border-danger";
                  } else {
                      $email = $conn->real_escape_string(test_input($_POST["email"]));

                      if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                          $emailError = "Invalid email format";
                          $emailBorder = "border border-danger";
                      } else {
                          // Check if disposable
                          $ch = curl_init();
                          curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
                          curl_setopt($ch, CURLOPT_URL, "https://disposable.debounce.io/?email=" . $email);
                          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                          $curlResult = curl_exec($ch);
                          $curlResult = json_decode($curlResult);



                          if ($curlResult->disposable != "false") {
                              $emailError = "Temporary emails are not allowed";
                              $emailBorder = "border border-danger";
                          } else {
                              //CHECK DB TO SEE IF ANOTHER ACCOUNT WITH EMAIL EXIST
                              $sql = "SELECT * FROM `clients` WHERE `email` = '$email'";
                              $result = $conn->query($sql);

                              if (empty($result) OR $result->num_rows === 0) {
                                  // No existing account
                                  $emailValid = true;
                              } else {
                                  $emailError = "This email address is already associated with another account";
                                  $emailBorder = "border border-danger";
                              }
                          }
                      }
                  }

                  if (empty($_POST["password"])) {
                      $passwordError = "Password is required";
                      $passwordBorder = "border border-danger";
                  } else {
                      $password = $conn->real_escape_string(test_input($_POST["password"]));

                      if (empty($_POST["confirmPassword"])) {
                          $confPasswordError = "Confirming password is required";
                          $confPasswordBorder = "border border-danger";
                      } else {
                          $confPassword = $conn->real_escape_string(test_input($_POST["confirmPassword"]));

                          if ($password != $confPassword) {
                              $passwordError = "Passwords don't match";
                              $passwordBorder = "border border-danger";
                              $confPasswordError = "Passwords don't match";
                              $confPasswordBorder = "border border-danger";
                          } else {
                              $passwordsValid = true;
                          }
                      }
                  }

                  if (isset($_POST['tos'])) {
                      $dateTermsAccepted = $conn->real_escape_string(date("dmY"));
                      $termsAccepted = true;
                  } else {
                      $tosError = "You need to accept the tos and privacy policy";
                  }

                  if ($emailValid && $passwordsValid && $termsAccepted && $captchaValid) {
                      // All Valid -> Create Account
                      $hashedPassword = $conn->real_escape_string(password_hash($password, PASSWORD_DEFAULT));

                      $tempHash = $conn->real_escape_string(md5(rand(9999,9999999)));
                      cloudflareIPRewrite();

                      $createAccountSQL = "INSERT INTO clients (email, password, dateTermsAccepted, passReset, lastLoginIP) VALUES ('$email', '$hashedPassword', '$dateTermsAccepted', '$tempHash', '{$_SERVER['REMOTE_ADDR']}')";
                      $createAccountResult = $conn->query($createAccountSQL);

                      if (!$createAccountResult) {
                          // Database Error
                          $errorMessage = $conn->real_escape_string($conn->error);
                          $errorLocation = "Signup (DB Error)";

                          // Insert into DB (if possible)
                          $insertDatabaseErrorSQL = "INSERT INTO `errors` (`errorText`, `errorLocation`, `errorTime`) VALUES ('$errorMessage', '$errorLocation', now())";
                          $conn->query($insertDatabaseErrorSQL);

                          //Redirect to error page
                          header("Location: error.php?errorCode=" . $conn->insert_id); die();
                      } else {
                          $sql = "SELECT `clientID` FROM `clients` WHERE `email` = '$email'";
                          $result = $conn->query($sql)->fetch_assoc();
                          $clientID = $result['clientID'];

                          setcookie('_apjsu', 1, time() + (86400 * 30), "/"); // 86400 = 1 day * 30 = 30 Days

                          $_SESSION['clientIDtoVerify'] = $clientID;

                          //Redirect
                          header("Location: verify.php"); die();
                      }
                  }
              }
            ?>

            <div class="row">
                <div class="col-xl-2 offset-xl-1 col-md-12">
                    <?php insertAds('sign01', false);?>
                </div>

                <div class="col-xl-4 col-md-8 offset-xl-1 offset-md-2">
                    <a href="index.php" class="text-center">
                        <img id="logo" class="mx-auto d-block p-2" src="./dashboard/assets/img/logo.png" alt="Ultifree Hosting Logo" />
                    </a>

                    <div class="card">
                        <div class="card-body ">
                            <h5 class="card-title text-center">Sign Up For A Free Account</h5>

                            <form action="signup.php" method="post">
                                <div class="form-group">
                                    <label for="email">Email address</label>
                                    <input type="email" class="form-control <?php echo $emailBorder; ?>" id="email" name="email" placeholder="Enter your email..." required>
                                    <small id="emailError" class="form-text text-danger"><?php echo $emailError; ?></small>
                                </div>
                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input type="password" class="form-control <?php echo $passwordBorder; ?>" id="password" name="password" placeholder="Enter your password..." required>
                                    <small id="passwordError" class="form-text text-danger"><?php echo $passwordError; ?></small>
                                </div>
                                <div class="form-group">
                                    <label for="confirmPassword">Confirm Password</label>
                                    <input type="password" class="form-control <?php echo $confPasswordBorder; ?>" id="confirmPassword" name="confirmPassword" placeholder="Enter your password again..." required>
                                    <small id="confPasswordError" class="form-text text-danger"><?php echo $confPasswordError; ?></small>
                                </div>
                                <div class="h-captcha" data-sitekey="4c363fb1-5b1c-4108-a97f-61475057c623"></div>
                                <small id="captchaError" class="form-text text-danger"><?php echo $captchaError; ?></small>
                                <div>
                                    <label class="form-group ml-4" for="tos">
                                        <input type="checkbox" class="form-check-input" value="" id="tos" name="tos" required>
                                        <span class="form-check-label">I've read and agree with the <a href="legal/tos.php" target="_blank">terms of service</a> and <a href="legal/privacy.php" target="_blank">privacy policy</a></span>
                                        <small id="tosError" class="form-text text-danger"><?php echo $tosError; ?></small>
                                    </label>
                                </div>
                                <button type="submit" class="btn btn-primary" onclick="CompleteSignupEvent();">Sign Up</button>
                            </form>

                            <p><a href="login.php">Already Have An Account?</a></p>

                            <hr>

                            <?php
                                echo $google_login_button;
                            ?>
                        </div>
                    </div>
                </div>

                <div class="col-xl-2 offset-xl-1 col-md-12">
                    <?php insertAds('sign02', false);?>
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
