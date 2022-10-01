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

    <?php 
      require_once './header.php';
      require_once '../ad_inserter.php';
      $accountNumber = $_GET['acc'];
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
            <a class="navbar-brand" href="javascript;">Deactivation History</a>
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
                  <div class="col-12">
                      <?php echo "<br />"; insertAds('dash17', false);?>
                  </div>
              </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-content">
                            <table class="table table-responsive-md">
                                <thead>
                                    <th>Reason</th>
                                    <th>Deactivation Date</th>
                                    <th>Reactivation Date</th>
                                    <th>Deactivation Status</th>
                                </thead>
                                <tbody>
                                    <?php 
                                      // Check if there are any deactivations to print
                                      if (count(${'accDeactivations' . $accountNumber}) > 0) {
                                        // Go through all suspensions and print them out
                                        foreach(${'accDeactivations' . $accountNumber} as $row) {
                                          echo '<tr>';
                                            echo '<td>';
                                              echo $row['reason'];
                                            echo '</td>';
                                            echo '<td>'; 
                                              echo $row['date'];
                                            echo '</td>';
                                            if ($row['suspensionStatus'] == "ACTIVE") {
                                              echo '<td>N/A</td>';
                                              echo '<td>Active</td>';
                                            } else {
                                              echo '<td>' . $row['reactivationDate'] . '</td>';
                                              echo '<td>Inactive</td>';
                                            }
                                          echo '</tr>';
                                        }
                                      } else {
                                        echo '<tr><td colspan="4" class="text-center">No Deactivations! :)</td></tr>';
                                      }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

              <div class="row">
                  <div class="col-12">
                      <?php echo "<br />"; insertAds('dash18', false);?>
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
