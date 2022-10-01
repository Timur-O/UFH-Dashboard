<?php
/**
 * @var array $allAnnouncements The array of announcements
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

    <?php require_once 'header.php' ?>

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
            <a class="navbar-brand" href="javascript;">Upgrade to Premium</a>
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
            <div class="col-md-10 ml-auto mr-auto">
            <div class="card card-upgrade">
              <div class="card-header text-center">
                <h4 class="card-title">Upgrade to Premium</h4>
                  <p class="card-category">Are you looking for more power? Fewer limits? Check out our Premium plans.</p>
              </div>
              <div class="card-body">
                <div class="table-responsive-md table-upgrade">
                  <table class="table">
                    <thead>
                      <th></th>
                      <th class="text-center">Free</th>
                      <th class="text-center">Starter</th>
                      <th class="text-center">Super</th>
                      <th class="text-center">Ultimate</th>
                    </thead>
                    <tbody>
                      <tr>
                        <td>Disk Space</td>
                        <td class="text-center">Unlimited</td>
                        <td class="text-center">5 GB</td>
                        <td class="text-center">Unlimited</td>
                        <td class="text-center">Unlimited</td>
                      </tr>
                      <tr>
                        <td>Monthly Bandwidth</td>
                        <td class="text-center">Unlimited</td>
                        <td class="text-center">250 GB</td>
                        <td class="text-center">250 GB</td>
                        <td class="text-center">Unlimited</td>
                      </tr>
                      <tr>
                        <td>Email Accounts</td>
                        <td class="text-center">0</td>
                        <td class="text-center">1</td>
                        <td class="text-center">100</td>
                        <td class="text-center">Unlimited</td>
                      </tr>
                      <tr>
                        <td>Domains (Addon, Sub, Parked)</td>
                        <td class="text-center">Unlimited</td>
                        <td class="text-center">1</td>
                        <td class="text-center">20</td>
                        <td class="text-center">Unlimited</td>
                      </tr>
                      <tr>
                        <td>MySQL Databases</td>
                        <td class="text-center">400</td>
                        <td class="text-center">1</td>
                        <td class="text-center">20</td>
                        <td class="text-center">Unlimited</td>
                      </tr>
                      <tr>
                        <td>Free Domain</td>
                        <td class="text-center"><i class="nc-icon nc-simple-remove text-danger"></i></td>
                        <td class="text-center"><i class="nc-icon nc-check-2 text-success"></i></td>
                        <td class="text-center"><i class="nc-icon nc-check-2 text-success"></i> + 5 Bonus Domains</td>
                        <td class="text-center"><i class="nc-icon nc-check-2 text-success"></i> + 21 Bonus Domains</td>
                      </tr>
                      <tr>
                        <td>Premium Support (24/7, 15 minute response time)</td>
                        <td class="text-center"><i class="nc-icon nc-simple-remove text-danger"></i></td>
                        <td class="text-center"><i class="nc-icon nc-check-2 text-success"></i></td>
                        <td class="text-center"><i class="nc-icon nc-check-2 text-success"></i></td>
                        <td class="text-center"><i class="nc-icon nc-check-2 text-success"></i></td>
                      </tr>
                      <tr>
                        <td></td>
                        <td class="text-center">Free</td>
                        <td class="text-center">$19.99 / Year</td>
                        <td class="text-center">$4.99 / Month</td>
                        <td class="text-center">$7.99 / Month</td>
                      </tr>
                      <tr>
                        <td class="text-center"></td>
                        <td class="text-center">
                          <a href="#" class="btn btn-round btn-default disabled">Current Plan</a>
                        </td>
                        <td class="text-center">
                          <a target="_blank" href="https://ifastnet.com/portal/aff.php?aff=26864&a=add&pid=78" onclick="iFastNetEvent('Dashboard Upgrade Page', 'Starter');" class="btn btn-round btn-primary">Upgrade to Starter</a>
                        </td>
                        <td class="text-center">
                          <a target="_blank" href="https://ifastnet.com/portal/aff.php?aff=26864&a=add&pid=1" onclick="iFastNetEvent('Dashboard Upgrade Page', 'Super Premium');" class="btn btn-round btn-primary">Upgrade to Super</a>
                        </td>
                        <td class="text-center">
                          <a target="_blank" href="https://ifastnet.com/portal/aff.php?aff=26864&a=add&pid=6" onclick="iFastNetEvent('Dashboard Upgrade Page', 'Ultimate Premium');" class="btn btn-round btn-primary">Upgrade to Ultimate</a>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
            </div>

            <div class="row">
              <!-- Ads would go here! -->
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
