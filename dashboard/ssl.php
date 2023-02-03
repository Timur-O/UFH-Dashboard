<?php
/**
 * @var array $allAnnouncements The array of announcements
 * @var integer $numberOfCertificates The number of current ssl certificates
 * @var array $allCertificates The array of certificates
 * @var string $verification If the verification
 * @var string $user The clientID
 * @var mysqli $conn The Database Object
 */

$sslVerificationTimedOut = false;
$cnameRefresh = false;

if ($_SERVER['REQUEST_METHOD'] == "GET") {
    if (isset($_GET['error'])) {
        if (trim($_GET['error']) == 'timeout') {
            $sslVerificationTimedOut = true;
        }
    }

    if (isset($_GET['refresh'])) {
        if (trim($_GET['refresh']) == 'cname') {
            $cnameRefresh = true;
        }
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
            <a class="navbar-brand" href="javascript;">All SSL Certificates</a>
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
                      <?php echo "<br />"; insertAds('dash19', false);?>
                  </div>
              </div>

            <div class="row">
              <div class="col-xl-8">
                <div class="card ">
                  <div class="card-header ">
                    <h5 class="card-title">Your SSL Certificates</h5>
                  </div>
                  <div class="card-body ">
                    
                    <table class="table <?php if ($numberOfCertificates > 0) { echo "table-responsive-sm"; } ?>">
                      <thead class=" text-primary">
                        <th>
                          Domain
                        </th>
                        <th>
                          Type
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
                          // List all the certificates currently owned by user
                          if ($numberOfCertificates > 0) {
                              foreach($allCertificates as $certificate) {
                                  echo '<tr>';
                                  echo '<td>' . $certificate['domain'] . '</td>';
                                  $type = $certificate['type'];
                                  if ($type == "zero") {
                                      echo '<td>Free</td>';
                                  } else {
                                      echo '<td>Self-Signed</td>';
                                  }
                                  $status = $certificate['status'];

                                  // Check Expire Date of Certificate + Update If Necessary
                                  if ($status == 1) {
                                      $currentTimestamp = time();
                                      $certExpire = strtotime(Date('Y-m-d', strtotime($certificate['expireDate'])));

                                      if (($certExpire + 86400) - $currentTimestamp < 0) {
                                          // Cert Expired -> Mark As Such
                                          $certificateID = $certificate['certificateID'];
                                          $markAsExpiredSQL = "UPDATE `certificates` SET `status` = '2' WHERE `certificateID` LIKE '$certificateID'";
                                          $result = $conn->query($markAsExpiredSQL);

                                          if (!$result) {
                                              // Database Error
                                              $errorMessage = $conn->real_escape_string($conn->error);
                                              $errorLocation = "Mark SSL Certificate As Expired (DB)";

                                              // Insert into DB (if possible)
                                              $insertDatabaseErrorSQL = "INSERT INTO `errors` (`errorText`, `errorLocation`, `errorTime`) VALUES ('$errorMessage', '$errorLocation', now())";
                                              $conn->query($insertDatabaseErrorSQL);

                                              //Redirect to error page
                                              header("Location: ../../error.php?errorCode=" . $conn->insert_id); die();
                                          }

                                          $status = 2;
                                      }
                                  }

                                  switch ($status) {
                                      case 0:
                                          echo '<td><span class="badge badge-primary">Pending Verification</span><br/><span class="badge badge-danger">Action Required!</span></td>';
                                          echo '<td><a class="btn btn-round btn-success mr-2 mb-2 mb-md-0" href="#" data-toggle="modal" data-target="#verifySslModal" ">Verify</a>
                                                <a class="btn btn-round btn-danger" href="#" data-toggle="modal" data-target="#removeSslCertificateModal">Delete</a></td>';
                                            echo '<div class="modal fade" id="removeSslCertificateModal" tabindex="-1" role="dialog" aria-labelledby="Remove SSL Certificate" aria-hidden="true">';
                                                echo '<div class="modal-dialog modal-dialog-centered" role="document">';
                                                    echo '<div class="modal-content">';
                                                      // Loading Screen
                                                      echo '<div class="loading_overlay_modal text-center text-primary">';
                                                      echo '<div class="spinner-grow" style="width: 5rem; height: 5rem;" role="status">';
                                                      echo '<span class="sr-only">Loading...</span>';
                                                      echo '</div>';
                                                      echo '</div>';
                                                      // Loading Screen End
                                                        echo '<div class="modal-header">';
                                                            echo '<h5 class="modal-title" id="removeSslCertificateModalTitle">Delete SSL Certificate</h5>';
                                                            echo '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
                                                                echo '<span aria-hidden="true">&times;</span>';
                                                            echo '</button>';
                                                        echo '</div>';
                                                        echo '<div class="modal-body text-left">';
                                                            echo '<p class="text-danger">';
                                                                echo 'Are you sure you would like to delete this certificate?';
                                                            echo '</p>';
                                                            echo '<form class="d-none" action="./api/removeSslCertificate.php" method="post" id="removeSslCertificateForm">';
                                                                echo '<div class="form-group d-none">';
                                                                    echo '<input type="text" class="d-none" id="removeSslID" name="certificateID" value="' . $certificate["certificateID"] . '">';
                                                                echo '</div>';
                                                            echo '</form>';
                                                        echo '</div>';
                                                        echo '<div class="modal-footer">';
                                                            echo '<button type="button" class="btn btn-danger" onclick="$(\'.loading_overlay_modal\').css(\'visibility\', \'visible\'); $(\'#removeSslCertificateForm\').submit(); return false;">Delete SSL Certificate</button>';
                                                        echo '</div>';
                                                    echo '</div>';
                                                echo '</div>';
                                            echo '</div>';

                                          echo '<div class="modal fade" id="verifySslModal" tabindex="-1" role="dialog" aria-labelledby="Verify SSL Certificate" aria-hidden="true">';
                                              echo '<div class="modal-dialog modal-dialog-centered" role="document">';
                                              echo '<div class="modal-content">';
                                              // Loading Screen
                                              echo '<div class="loading_overlay_modal text-center text-primary">';
                                                echo '<div class="spinner-grow" style="width: 5rem; height: 5rem;" role="status">';
                                                    echo '<span class="sr-only">Loading...</span>';
                                                echo '</div>';
                                              echo '</div>';
                                              // Loading Screen End
                                              echo '<div class="modal-header">';
                                              echo '<h5 class="modal-title" id="verifySslModalTitle">Verify SSL Certificate</h5>';
                                              echo '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
                                              echo '<span aria-hidden="true">&times;</span>';
                                              echo '</button>';
                                              echo '</div>';
                                              echo '<div class="modal-body text-left">';
                                              echo '<p class="text-dark">';
                                              echo 'To verify your SSL Certificate please create the following CNAME record:';
                                              echo '</p>';
                                              echo '<p class="text-dark">';
                                              echo 'Source: _acme-challenge (.' . $certificate['domain'] . ')';
                                              echo '</p>';
                                              echo '<p class="text-dark">';
                                              echo 'Destination: ' . $certificate['subdomainID'] . '.acme.ultifreehosting.com';
                                              echo '</p>';
                                              echo '<div class="alert alert-with-icon alert-info">';
                                              echo '<span data-notify="icon" class="nc-icon nc-alert-circle-i"></span>';
                                              echo '<span>Due to DNS Propagation it may take up to 72 hours for CNAME records to be detected.</span>';
                                              echo '</div>';
                                              echo '<p class="text-dark">';
                                                  $cnameRecords = dns_get_record('_acme-challenge.' . $certificate['domain'], DNS_CNAME);
                                                  if (sizeof($cnameRecords) == 0) {
                                                      echo "Current Destination: None";
                                                  } else {
                                                      echo "Current Destination: " . $cnameRecords[0]['target'];
                                                  }
                                              echo '</p>';
                                              if ($sslVerificationTimedOut) {
                                                  echo '<div class="alert alert-danger" role="alert">';
                                                  echo 'Error: SSL Verification Timed-Out. Please try again later.';
                                                  echo '</div>';
                                                  echo '<script>window.addEventListener("load", () => {const verifySslModal = new bootstrap.Modal(document.getElementById("verifySslModal")); verifySslModal.show();})</script>';
                                              }
                                              echo '<form class="d-none" action="./api/createFreeSslCertificateVerification.php" method="post" id="verifySslCertificateForm">';
                                              echo '<div class="form-group d-none">';
                                              echo '<input type="text" class="d-none" id="verifySslId" name="certificateID" value="' . $certificate["certificateID"] . '">';
                                              echo '</div>';
                                              echo '</form>';
                                              echo '</div>';
                                              echo '<div class="modal-footer">';
                                                if (sizeof($cnameRecords) > 0 && $cnameRecords[0]['target'] == $certificate['subdomainID'] . '.acme.ultifreehosting.com') {
                                                    echo '<button type="button" class="btn btn-danger" onclick="$(\'.loading_overlay_modal\').css(\'visibility\', \'visible\'); $(\'#verifySslCertificateForm\').submit(); return false;">Verify SSL Certificate</button>';
                                                } else {
                                                    echo '<button type="button" class="btn btn-danger" onclick="$(\'.loading_overlay_modal\').css(\'visibility\', \'visible\'); location.assign(location.protocol + \'//\' + location.host + location.pathname + \'?refresh=cname\'); return false;">Verify CNAME Record</button>';
                                                }
                                                if ($cnameRefresh) {
                                                    echo '<script>window.addEventListener("load", () => {const verifySslModal = new bootstrap.Modal(document.getElementById("verifySslModal")); verifySslModal.show();})</script>';
                                                }
                                              echo '</div>';
                                              echo '</div>';
                                              echo '</div>';
                                          echo '</div>';
                                          break;
                                      case 1:
                                          echo '<td><span class="badge badge-success">Issued</span></td>';
                                          echo '<td><a class="btn btn-round btn-success" href="sslCertificate.php?cert=' . $certificate["certificateID"] . '">Manage</a></td>';
                                          break;
                                      case 2:
                                          echo '<div class="modal fade" id="removeSslCertificateModal" tabindex="-1" role="dialog" aria-labelledby="Remove SSL Certificate" aria-hidden="true">';
                                          echo '<div class="modal-dialog modal-dialog-centered" role="document">';
                                          echo '<div class="modal-content">';
                                          // Loading Screen
                                          echo '<div class="loading_overlay_modal text-center text-primary">';
                                          echo '<div class="spinner-grow" style="width: 5rem; height: 5rem;" role="status">';
                                          echo '<span class="sr-only">Loading...</span>';
                                          echo '</div>';
                                          echo '</div>';
                                          // Loading Screen End
                                          echo '<div class="modal-header">';
                                          echo '<h5 class="modal-title" id="removeSslCertificateModalTitle">Delete SSL Certificate</h5>';
                                          echo '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
                                          echo '<span aria-hidden="true">&times;</span>';
                                          echo '</button>';
                                          echo '</div>';
                                          echo '<div class="modal-body text-left">';
                                          echo '<p class="text-danger">';
                                          echo 'Are you sure you would like to delete this certificate?';
                                          echo '</p>';
                                          echo '<form class="d-none" action="./api/removeSslCertificate.php" method="post" id="removeSslCertificateForm">';
                                          echo '<div class="form-group d-none">';
                                          echo '<input type="text" class="d-none" id="removeSslID" name="certificateID" value="' . $certificate["certificateID"] . '">';
                                          echo '</div>';
                                          echo '</form>';
                                          echo '</div>';
                                          echo '<div class="modal-footer">';
                                          echo '<button type="button" class="btn btn-danger" onclick="$(\'.loading_overlay_modal\').css(\'visibility\', \'visible\'); $(\'#removeSslCertificateForm\').submit(); return false;">Delete SSL Certificate</button>';
                                          echo '</div>';
                                          echo '</div>';
                                          echo '</div>';
                                          echo '</div>';

                                          echo '<td><span class="badge badge-danger">Expired</span></td>';
                                          echo '<td><a class="btn btn-round btn-danger" href="#" data-toggle="modal" data-target="#removeSslCertificateModal">Delete</a></td>';
                                          break;
                                      case 3:
                                          echo '<div class="modal fade" id="removeSslCertificateModal" tabindex="-1" role="dialog" aria-labelledby="Remove SSL Certificate" aria-hidden="true">';
                                          echo '<div class="modal-dialog modal-dialog-centered" role="document">';
                                          echo '<div class="modal-content">';
                                          // Loading Screen
                                          echo '<div class="loading_overlay_modal text-center text-primary">';
                                          echo '<div class="spinner-grow" style="width: 5rem; height: 5rem;" role="status">';
                                          echo '<span class="sr-only">Loading...</span>';
                                          echo '</div>';
                                          echo '</div>';
                                          // Loading Screen End
                                          echo '<div class="modal-header">';
                                          echo '<h5 class="modal-title" id="removeSslCertificateModalTitle">Delete SSL Certificate</h5>';
                                          echo '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
                                          echo '<span aria-hidden="true">&times;</span>';
                                          echo '</button>';
                                          echo '</div>';
                                          echo '<div class="modal-body text-left">';
                                          echo '<p class="text-danger">';
                                          echo 'Are you sure you would like to delete this certificate?';
                                          echo '</p>';
                                          echo '<form class="d-none" action="./api/removeSslCertificate.php" method="post" id="removeSslCertificateForm">';
                                          echo '<div class="form-group d-none">';
                                          echo '<input type="text" class="d-none" id="removeSslID" name="certificateID" value="' . $certificate["certificateID"] . '">';
                                          echo '</div>';
                                          echo '</form>';
                                          echo '</div>';
                                          echo '<div class="modal-footer">';
                                          echo '<button type="button" class="btn btn-danger" onclick="$(\'.loading_overlay_modal\').css(\'visibility\', \'visible\'); $(\'#removeSslCertificateForm\').submit(); return false;">Delete SSL Certificate</button>';
                                          echo '</div>';
                                          echo '</div>';
                                          echo '</div>';
                                          echo '</div>';

                                          echo '<td><span class="badge badge-danger">Revoked</span></td>';
                                          echo '<td><a class="btn btn-round btn-danger" href="#" data-toggle="modal" data-target="#removeSslCertificateModal">Delete</a></td>';
                                          break;
                                      default:
                                          echo '<td>N/A</td>';
                                          echo '<td>No Actions Available</td>';
                                          break;
                                  }
                              echo '</tr>';
                              }
                          } else {
                            echo '<tr><td colspan="4" class="text-center">No Certificates Yet :(</td></tr>';
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
                            <a href="createSslDomain.php" class="btn btn-round btn-default btn-block"><i class="nc-icon nc-simple-add"></i> Create New Certificate</a>
                          </td>
                          <td class="text-center pl-2">
                            <a href="upgrade.php" class="btn btn-round btn-premium btn-block" onclick="PremiumEvent('Dashboard SSL List', 'Premium CTA');"><i class="nc-icon nc-diamond"></i> Upgrade to Premium</a>
                          </td>
                        </tr>
                      </tbody>
                    </table>

                  </div>
                </div>
              </div>

              <div class="col-xl-4">
                  <div class="card text-justify">
                      <div class="card-header ">
                          <h5 class="card-title">Verifying Your Domain</h5>
                      </div>
                      <div class="card-body">
                          <p>
                              If your SSL certificate is currently in the "pending verification" state this means you need to verify your domain. This can be done as follows:
                          </p>
                          <ol>
                              <li>Click on the "Verify" button on the right of your domain.</li>
                              <li>Create a CNAME record with the records specified in the box that opens. If your nameservers are pointing to ours you can create a CNAME record in the control panel, otherwise please check on the website where you bought your domain.</li>
                              <li>Wait for DNS propagation - this can take up to 72 hours, although it usually takes around 10-15 minutes. You can check if it's completed by clicking "Verify", then clicking "Verify CNAME Record".</li>
                              <li>Finally, click "Verify", then click "Verify" in the popup box and wait for your certificate to be generated!</li>
                          </ol>
                          <p>
                              Now your certificate is ready! Click on "Manage" to install it!
                          </p>
                      </div>
                  </div>
              </div>
            </div>

              <div class="row">
                  <div class="col-12">
                      <?php echo "<br />"; insertAds('dash20', false);?>
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
