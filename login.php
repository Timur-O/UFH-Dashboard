<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <link rel="apple-touch-icon" sizes="76x76" href="./dashboard/assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="./dashboard/assets/img/favicon.png">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <title>
    Login - Ultifree Hosting
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

        //Make object of Google API Client for call Google API
        $google_client = new Google_Client();
        //Set the OAuth 2.0 Client ID
        $google_client->setClientId('***REMOVED***');
        //Set the OAuth 2.0 Client Secret key
        $google_client->setClientSecret('***REMOVED***');
        //Set the OAuth 2.0 Redirect URI
        $google_client->setRedirectUri('https://' . $_SERVER['SERVER_NAME'] . '/sociallogin.php');
        $google_client->addScope('email');
        $google_client->addScope('profile');

        // Check if remember me is set
        if (isset($_COOKIE['user']) && isset($_COOKIE['remKey'])) {
            $user = $conn->real_escape_string(test_input($_COOKIE["user"]));
            $remKeyCookie = $conn->real_escape_string(test_input($_COOKIE["remKey"]));

            $sql = "SELECT `rememberMe` FROM `clients` WHERE `clientID` = '$user'";
            $result = $conn->query($sql)->fetch_assoc();
            $remKeyData = $result['rememberMe'];

            if ($remKeyData == $remKeyCookie) {
                //Set session vars
                $_SESSION['user'] = $user;
                $_SESSION['newSession'] = true;

                $_SESSION['loginTime'] = time();
                //Redirect
                header("Location: dashboard/home.php"); die();
            }
        } else if (isset($_SESSION['user'])) {
            header("Location: dashboard/home.php"); die();
        } else {
            $google_login_button = '<a href="'.$google_client->createAuthUrl().'"  onclick="LoginEvent(\'Social - Google\');"><img id="google_login_button" src="dashboard/assets/img/google_login.png" /></a>';
        }

        $fetchAnnouncements = "SELECT * FROM `announcements`";

        // Collect Information
        $allAnnouncements = $conn->query($fetchAnnouncements)->fetch_all(MYSQLI_ASSOC);
    ?>

    <div class="main-panel bg-muted">
      <div class="content">
        <div class="row">
          <div class="col-md-12">

            <?php

            $errorMessage = $emailError = $passwordError = $emailBorder = $passwordBorder = "";
            $emailValid = $passwordValid = false;

            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                if (empty($_POST["email"])) {
                    $emailError = "Email is required";
                    $emailBorder = "border border-danger";
                } else {
                    $email = $conn->real_escape_string(test_input($_POST["email"]));

                    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $emailError = "Invalid email format";
                        $emailBorder = "border border-danger";
                    } else {
                        // CHECK DB TO SEE IF ACCOUNT WITH EMAIL EXIST
                        $sql = "SELECT * FROM `clients` WHERE `email` = '$email'";
                        $result = $conn->query($sql);

                        if (empty($result) OR $result->num_rows === 0) {
                            $errorMessage = "User doesn't exist or password is incorrect";
                            $emailBorder = "border border-danger";
                            $passwordBorder = "border border-danger";
                        } else {
                            $emailValid = true;
                        }
                    }
                }

                if (empty($_POST["password"])) {
                    $passwordError = "Password is required";
                    $passwordBorder = "border border-danger";
                } else {
                    $password = $conn->real_escape_string(test_input($_POST["password"]));

                    if ($emailValid) {
                        $sql = "SELECT `password` FROM `clients` WHERE `email` = '$email'";
                        $result = $conn->query($sql);
                        $result = $result->fetch_assoc();
                        $hashPass = $result['password'];

                        if (password_verify($password, $hashPass)) {
                            $passwordValid = true;
                        } else {
                            $errorMessage = "User doesn't exist or password is incorrect";
                            $emailBorder = "border border-danger";
                            $passwordBorder = "border border-danger";
                        }
                    }
                }

                if ($emailValid && $passwordValid) {
                    $sql = "SELECT `clientID` FROM `clients` WHERE `email` = '$email'";
                    $result = $conn->query($sql)->fetch_assoc();
                    $clientID = $result['clientID'];

                    if (isset($_POST['rememberMe'])) {
                        //Set cookie and in DB to check if logged in.
                        $remKey = md5(rand(99999,999999999999));
                        $cookie_name = "user";
                        $cookie_value = $clientID;
                        setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/"); // 86400 = 1 day so 30 days
                        $cookie_name1 = "remKey";
                        $cookie_value1 = $remKey;
                        setcookie($cookie_name1, $cookie_value1, time() + (86400 * 30), "/"); // 86400 = 1 day so 30 days

                        $sql = "UPDATE `clients` SET `rememberMe` = '$remKey' WHERE `clientID` = '$clientID'";
                        $result = $conn->query($sql);
                    }

                    $_SESSION['user'] = $clientID;
                    $_SESSION['newSession'] = true;

                    $_SESSION['loginTime'] = time();

                    //Redirect
                    header("Location: dashboard/home.php"); die();
                    die();
                }
            }

              // Get Announcements and convert to HTML
              foreach($allAnnouncements as $row) {
                if ($row["location"] == "login") {
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
                    <?php insertAds('logi01', false);?>
                </div>

                <div class="col-xl-4 col-md-8 offset-xl-1 offset-md-2">
                    <a href="index.php" class="text-center">
                        <img id="logo" class="mx-auto d-block p-2" src="./dashboard/assets/img/logo.png" alt="Ultifree Hosting Logo" />
                    </a>

                    <div class="card">
                        <div class="card-body ">
                            <h5 class="card-title text-center">Log Into Your Account</h5>

                            <form action="login.php" method="post">
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
                                <div>
                                    <label class="form-group ml-4" for="rememberMe">
                                        <input type="checkbox" class="form-check-input" value="" id="rememberMe" name="rememberMe">
                                        <span class="form-check-label">Remember Me</span>
                                    </label>
                                </div>
                                <small id="error" class="form-text text-danger"><?php echo $errorMessage; ?></small>
                                <button type="submit" class="btn btn-primary" onclick="LoginEvent('Email');">Login</button>
                            </form>

                            <p><a href="forgotpassword.php">Forgot password?</a></p>
                            <p><a href="signup.php" onclick="BeginSignupEvent('Login Page', 'Create New Account Button');">Create a new account</a></p>

                            <hr>

                            <?php
                                echo $google_login_button;
                            ?>
                        </div>
                    </div>
                </div>

                <div class="col-xl-2 offset-xl-1 col-md-12">
                    <?php insertAds('logi02', false);?>
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
