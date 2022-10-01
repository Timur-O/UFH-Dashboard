<?php
/**
 * @var array $allAnnouncements The array of announcements
 * @var string $verification If the verification
 * @var string $user The clientID
 */
  session_start();
  // If submitted by post, then redirect to step two of creation process
  $nameServersInvalid = false;
  $alreadyRegistered = false;

  if ($_SERVER['REQUEST_METHOD'] == "GET") {
      if (isset($_GET['error'])) {
          if (trim($_GET['error']) == 'ns') {
              $nameServersInvalid = true;
          } else if (trim($_GET['error']) == 'taken') {
              $alreadyRegistered = true;
          }
      }
  }

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Store the submitted domain in a session variable to send it to the next page
    $_SESSION['newDomain'] = htmlspecialchars($_POST['domain']);

    // Check if nameservers are set up correctly
    $dnsNS = dns_get_record($_SESSION['newDomain'], DNS_NS);
    $firstNameserver = $dnsNS[0]['target'];
    $secondNameserver = $dnsNS[1]['target'];

    $dnsCNAME = dns_get_record($_SESSION['newDomain'], DNS_CNAME);
    $cnameRecord = $dnsCNAME[0]['target'];

    if ((gethostbyname('ns1.ultihost.net') == gethostbyname($firstNameserver) &&
        gethostbyname('ns2.ultihost.net') == gethostbyname($secondNameserver)) ||
        str_contains($cnameRecord, ".BODIS.com")) {
        header("Location: ./createAccountInfo.php"); die();
    } else {
        $nameServersInvalid = true;
    }
  }
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
            <a class="navbar-brand" href="javascript:">Create New Hosting Account</a>
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
                      <?php echo "<br />"; insertAds('dash11', false);?>
                  </div>
              </div>

            <div class="row">
                <div class="col-md-8">
                  <div class="card">
                    <div class="card-header text-center">
                      <h5 class="card-title">Enter your domain:</h5>
                    </div>
                    <div class="card-content">
                      <table class="table table-borderless">
                        <tbody>
                          <tr>
                            <td>
                                <form class="text-center col-md-10 offset-lg-1 offset-md-0" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                    <input type="text" id="domain" name="domain" class="form-control text-center" placeholder="yourdomain.com" aria-describedby="domainHelpBlock">
                                    <small id="domainHelpBlock" class="form-text text-muted">
                                        You can add more domains once your account is created.
                                    </small>
                                    <br/>
                                    <!-- <button type="submit" class="btn btn-primary">Continue</button> -->
                                    <a id="continueButton" onclick="this.closest('form').submit(); return false;" class="btn btn-primary" disabled>Continue</a>
                                </form>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>

                    <?php
                        if ($nameServersInvalid) {
                            echo '<div id="nameserverError" class="alert alert-danger">';
                                echo '<span class="text-dark">This domain\'s nameservers are not pointing to ours! Please make sure the nameservers are set to: ns1.ultihost.net and ns2.ultihost.net</span><br/></br>';
                                echo '<span class="text-dark">Sometimes it may take up to 72 hours for the nameserver change to propagate. If you keep getting this error and already changed the nameservers, please try again later.</span>';
                            echo '</div>';
                        }

                        if ($alreadyRegistered) {
                            echo '<script>
                                    document.getElementById("domainError").children[0].textContent = "This domain is not available, please try a different one.";
                                    // Show error message
                                    document.getElementById("domainError").style.display = "block";
                                    document.getElementById("domainSuccess").style.display = "none";
                                </script>';
                        }
                    ?>

                  <div id="domainError" class="alert alert-danger">
                      <span>This domain is already taken!</span>
                  </div>

                  <div id="domainSuccess" class="alert alert-success">
                      <span>This domain is available!</span>
                  </div>

                </div>

                <div class="col-md-4">
                  <div class="card">
                    <div class="card-header text-center">
                      <h5 class="card-title"><i class="nc-icon nc-alert-circle-i"></i> IMPORTANT <i class="nc-icon nc-alert-circle-i"></i></h5>
                    </div>
                    <div class="card-content">
                      <table class="table table-borderless">
                        <tbody>
                          <tr>
                            <td class="text-justify">
                                <p>
                                    Make sure your domain is pointing to Ultifree Hosting nameservers:
                                </p>
                                <ul>
                                    <li>ns1.ultihost.net</li>
                                    <li>ns2.ultihost.net</li>
                                </ul>
                                <p>
                                    To change your nameservers go to your DNS provider (usually the company you bought your domain from) and change your domain's nameservers. 
                                </p>
                                <p>
                                Usually googling the company's name and "change nameservers" will provide you with further information on doing this. For example, if you bought your domain from GoDaddy: "GoDaddy change nameservers".
                                </p>
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
                      <?php echo "<br />"; insertAds('dash12', false);?>
                  </div>
              </div>

          </div>
        </div>
      </div>

      <?php include './footer.php'; ?>

    </div>
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
