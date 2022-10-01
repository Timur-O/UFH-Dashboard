<?php
/**
 * @var array $allAnnouncements The array of announcements
 * @var string $verification If the client is verified or not
 * @var string $user the clientID
 */
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <link rel="apple-touch-icon" sizes="76x76" href="./assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="./assets/img/favicon.png">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <title>
    Dashboard - Ultifree Hosting
  </title>
  <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
  <!--     Fonts and icons     -->
  <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700,200" rel="stylesheet" />
  <link href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css" rel="stylesheet">
  <!-- CSS Files -->
  <link href="./assets/css/bootstrap.min.css" rel="stylesheet" />
  <link href="./assets/css/paper-dashboard.css?v=2.0.1" rel="stylesheet" />
  <link href="./assets/css/custom.css" rel="stylesheet" />
</head>

<body class="">
  <div class="wrapper ">

      <?php
          require_once './header.php';
          require_once '../ad_inserter.php';
      ?>

    <div class="main-panel" style="height: 100vh;">
      <!-- Navbar -->
      <nav class="navbar navbar-expand-lg navbar-absolute fixed-top navbar-transparent">
        <div class="container-fluid">
          <div class="navbar-wrapper">
            <div class="navbar-toggle">
              <button type="button" class="navbar-toggler">
                <span class="navbar-toggler-bar bar1"></span>
                <span class="navbar-toggler-bar bar2"></span>
                <span class="navbar-toggler-bar bar3"></span>
              </button>
            </div>
            <a class="navbar-brand" href="javascript;">Create New Hosting Account</a>
          </div>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navigation" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-bar navbar-kebab"></span>
            <span class="navbar-toggler-bar navbar-kebab"></span>
            <span class="navbar-toggler-bar navbar-kebab"></span>
          </button>
          <div class="collapse navbar-collapse justify-content-end" id="navigation">
          <ul class="navbar-nav">
              <li class="nav-item">
                  <a class="btn btn-default btn-round" href="settings.php">Settings</a>
              </li>
              <li class="nav-item">
                <a class="btn btn-info btn-round" href="../logout.php">Log Out</a>
              </li>
            </ul>
          </div>
        </div>
      </nav>
      <!-- End Navbar -->
      <div class="content">
        <div class="row">
          <div class="col-md-12">

          <?php

            if ($verification != "YES") {
                $_SESSION['clientIDtoVerify'] = $user;

                echo '<div class="alert alert-danger fade show" data-notify="container">';
                    echo '<span data-notify="message">';
                        echo 'Please verify your email! <a class="text-dark" href="../verify.php">Resend Verification Email</a>';
                    echo '</span>';
                echo '</div>';
            }

            // Get Announcements and convert to HTML
            foreach($allAnnouncements as $row) {
              if ($row["location"] == "dash") {
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
                  <div class="col-12">
                      <?php echo "<br />"; insertAds('dash07', false);?>
                  </div>
              </div>

            <div class="row">
                <div class="col-md-6">
                  <div class="card">
                    <div class="card-header text-center">
                      <h5 class="card-title">Create Account with Sub-Domain</h5>
                      <p class="card-category">We will provide you with a free sub-domain!</p>
                    </div>
                    <div class="card-content">
                      <table class="table table-borderless">
                        <tbody>
                          <tr>
                            <td>
                              <a href="createAccountSubDomain.php" class="btn btn-round btn-success btn-block"><i class="nc-icon nc-simple-add"></i> Create Sub-Domain Account</a>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="card">
                    <div class="card-header text-center">
                      <h5 class="card-title">Create Account with Custom Domain</h5>
                      <p class="card-category">You need to purchase/own a domain!</p>
                    </div>
                    <div class="card-content">
                    <table class="table table-borderless">
                        <tbody>
                          <tr>
                            <td>
                              <a href="createAccountCustomDomain.php" class="btn btn-round btn-success btn-block"><i class="nc-icon nc-simple-add"></i> Create Custom Domain Account</a>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
            </div>

              <div class="row">
                  <div class="col-12">
                      <?php echo "<br />"; insertAds('dash08', false);?>
                  </div>
              </div>

          </div>
        </div>
      </div>
    </div>

    <?php include 'footer.php'; ?>

  </div>
  <!--   Core JS Files   -->
  <script src="./assets/js/core/jquery.min.js"></script>
  <script src="./assets/js/core/popper.min.js"></script>
  <script src="./assets/js/core/bootstrap.min.js"></script>
  <!--  Notifications Plugin    -->
  <script src="./assets/js/plugins/bootstrap-notify.js"></script>
  <!-- Control Center for Now Ui Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="./assets/js/paper-dashboard.js?v=2.0.1" type="text/javascript"></script>
  <!-- Custom Javascript -->
  <script src="./assets/js/custom.js" type="text/javascript"></script>
</body>

</html>
