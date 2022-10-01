<?php
/**
 * @var array $allAnnouncements The array of announcements
 * @var integer $numberOfAccounts The number of current hosting accounts
 * @var string $accountID1 The accountID of account #1
 * @var string $accountID2 The accountID of account #2
 * @var string $accountID3 The accountID of account #3
 * @var string $accUsername1 The username of account #1
 * @var string $accUsername2 The username of account #2
 * @var string $accUsername3 The username of account #3
 * @var string $accLabel1 The label of account #1
 * @var string $accLabel2 The label of account #2
 * @var string $accLabel3 The label of account #3
 * @var string $accStatus1 The status of account #1
 * @var string $accStatus2 The status of account #2
 * @var string $accStatus3 The status of account #3
 * @var string $verification If the verification
 * @var string $user The clientID
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
            <a class="navbar-brand" href="javascript;">All Accounts</a>
          </div>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navigation" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-bar navbar-kebab"></span>
            <span class="navbar-toggler-bar navbar-kebab"></span>
            <span class="navbar-toggler-bar navbar-kebab"></span>
          </button>
          <div class="collapse navbar-collapse justify-content-end" id="navigation">
            <ul class="navbar-nav">
              <li class="nav-item">
                  <a class="btn btn-dark btn-round" href="settings.php">Settings</a>
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
                      <?php echo "<br />"; insertAds('dash03', false);?>
                  </div>
              </div>

            <div class="row">
              <div class="col-md-12">
                <div class="card ">
                  <div class="card-header ">
                    <h5 class="card-title">Your Hosting Accounts</h5>
                    <p class="card-category">Accounts: <?php echo $numberOfAccounts; ?>/3</p>
                  </div>
                  <div class="card-body ">
                    
                    <table class="table table-responsive-sm">
                      <thead class=" text-primary">
                        <th>
                          Username
                        </th>
                        <th>
                          Label
                        </th>
                        <th>
                          Status
                        </th>
                        <th>
                          Actions
                        </th>
                      </thead>
                      <tbody>

                        <?php
                          // List all of the accounts currently owned by user
                          if ($numberOfAccounts > 0) {
                            $accountDisplayNumberValue = 1;

                            if ($accountID1 != NULL) {
                              $accountDisplayNumberValue++;
                              echo '<tr>';
                                echo '<td>';
                                  echo '<a href="./hostAccount.php?acc=1&accDisp=' . $accountDisplayNumberValue . '">' . $accUsername1 . '</a>';
                                echo '</td>';
                                echo '<td>';
                                  echo $accLabel1;
                                echo '</td>';
                                echo '<td>';
                                  if ($accStatus1 == "A") {
                                    echo 'Active';
                                  } elseif ($accStatus1 == "D") {
                                    echo 'Deleted';
                                  } elseif ($accStatus1 == "P") {
                                    echo 'Processing';
                                  } elseif ($accStatus1 == "S") {
                                    echo 'Suspended';
                                  } else {
                                    echo 'Inactive';
                                  }
                                echo '</td>';
                                echo '<td>';
                                  echo '<a href="./hostAccount.php?acc=1&accDisp=' . $accountDisplayNumberValue . '" class="btn btn-round btn-success"><i class="nc-icon nc-app"></i> Manage</a>';
                                echo '</td>';
                              echo '</tr>';
                            }

                            if ($accountID2 != NULL) {
                              $accountDisplayNumberValue++;
                              echo '<tr>';
                                echo '<td>';
                                  echo '<a href="./hostAccount.php?acc=2&accDisp=' . $accountDisplayNumberValue . '">' . $accUsername2 . '</a>';
                                echo '</td>';
                                echo '<td>';
                                  echo $accLabel2;
                                echo '</td>';
                                echo '<td>';
                                  if ($accStatus2 == "A") {
                                    echo 'Active';
                                  } elseif ($accStatus2 == "D") {
                                    echo 'Deleted';
                                  } elseif ($accStatus2 == "P") {
                                    echo 'Processing';
                                  } elseif ($accStatus2 == "S") {
                                    echo 'Suspended';
                                  } else {
                                    echo 'Inactive';
                                  }
                                echo '</td>';
                                echo '<td>';
                                  echo '<a href="./hostAccount.php?acc=2&accDisp=' . $accountDisplayNumberValue . '" class="btn btn-round btn-success"><i class="nc-icon nc-app"></i> Manage</a>';
                                echo '</td>';
                              echo '</tr>';
                            }

                            if ($accountID3 != NULL) {
                              $accountDisplayNumberValue++;
                              echo '<tr>';
                                echo '<td>';
                                  echo '<a href="./hostAccount.php?acc=3&accDisp=' . $accountDisplayNumberValue . '">' . $accUsername3 . '</a>';
                                echo '</td>';
                                echo '<td>';
                                  echo $accLabel3;
                                echo '</td>';
                                echo '<td>';
                                  if ($accStatus3 == "A") {
                                    echo 'Active';
                                  } elseif ($accStatus3 == "D") {
                                    echo 'Deleted';
                                  } elseif ($accStatus3 == "P") {
                                    echo 'Processing';
                                  } elseif ($accStatus3 == "S") {
                                    echo 'Suspended';
                                  } else {
                                    echo 'Inactive';
                                  }
                                echo '</td>';
                                echo '<td>';
                                  echo '<a href="./hostAccount.php?acc=3&accDisp=' . $accountDisplayNumberValue . '" class="btn btn-round btn-success"><i class="nc-icon nc-app"></i> Manage</a>';
                                echo '</td>';
                              echo '</tr>';
                            }
                          } else {
                            echo '<tr><td colspan="4" class="text-center">No Accounts Yet :(</td></tr>';
                          }
                        ?>
                      </tbody>
                    </table>

                  </div>
                  <div class="card-footer justify-content-center">
                    <hr>
                      
                    <table class="w-100">
                      <tbody>
                        <tr>
                          <td class="text-center">
                            <a href="createAccountType.php" class="btn btn-round btn-default btn-block"><i class="nc-icon nc-simple-add"></i> Create New Account</a>
                          </td>
                          <td class="text-center pl-2">
                            <a href="upgrade.php" class="btn btn-round btn-premium btn-block" onclick="PremiumEvent('Dashboard Home', 'Premium CTA');"><i class="nc-icon nc-diamond"></i> Upgrade to Premium</a>
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
                      <?php echo "<br />"; insertAds('dash04', false);?>
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
  <!-- Control Center for Now Ui Dashboard: parallax effects etc -->
  <script src="./assets/js/paper-dashboard.js?v=2.0.1" type="text/javascript"></script>
  <!-- Custom Javascript -->
  <script src="./assets/js/custom.js" type="text/javascript"></script>
</body>

</html>
