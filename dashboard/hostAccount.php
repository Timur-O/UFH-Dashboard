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

      $displayAccountNumber = $_GET['accDisp'];
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
              <a class="navbar-brand" href="#">Account #<?php echo $displayAccountNumber; ?> <span class="d-none d-md-inline">- <?php echo ${'accLabel' . $accountNumber}; ?></span></a>
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

            // If account is processing show processing bar
            if (${'accStatus' . $accountNumber} == "P") {
              echo '<div class="alert alert-warning text-center">';
                echo '<span class="text-dark">Account is currently being processed, please reload this page to see if the account is active!</span>';
                echo '<div class="progress">';
                  echo '<div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></div>';
                echo '</div>';
              echo '</div>';
            }
          ?>

              <div class="row">
                  <div class="col-12">
                      <?php echo "<br />"; insertAds('dash05', false);?>
                  </div>
              </div>

                <div class="row">

                  <div class="col-xl-6">
                    <div class="card ">
                      <div class="card-body ">
                      <table class="table table-responsive-sm">
                        <thead class="text-primary">
                          <th colspan="2" class="text-center">
                            Account Details - Information about your Hosting Account
                          </th>
                        </thead>
                        <tbody>
                          <tr>
                            <th>Username:</th>
                            <td>
                              <?php echo ${"accUsername" . $accountNumber}; ?>
                            </td>
                          </tr>

                          <tr>
                            <th>Password:</th>
                            <td>
                              <table class="table-borderless table-responsive-stack">
                                <tr>
                                  <td class="pl-0">
                                    <input id="accDetailsPass" disabled type="password" value="<?php echo ${"accPassword" . $accountNumber}; ?>" class="displayPasswordInput" />
                                  </td>
                                  <td class="pl-0">
                                    <a href="#" for="accDetailsPass" class="btn btn-warning btn-sm showHidePass">Show/Hide</a>
                                  </td>
                                </tr>
                              </table>
                            </td>
                          </tr>

                          <tr>
                            <th>Status:</th>
                            <td>
                              <?php
                                if (${"accStatus" . $accountNumber} == "A") {
                                  echo '<span class="badge badge-success">Active</span>';
                                } elseif(${"accStatus" . $accountNumber} == "P") {
                                  echo '<span class="badge badge-primary">Processing</span>';
                                } elseif(${"accStatus" . $accountNumber} == "D") {
                                  echo '<span class="badge badge-danger">Deleted</span>';
                                } elseif(${"accStatus" . $accountNumber} == "S") {
                                  echo '<span class="badge badge-danger">Suspended</span>';
                                } else {
                                  echo '<span class="badge badge-default">Inactive</span>';
                                }
                              ?>
                            </td>
                          </tr>

                          <tr>
                              <th>Main Domain:</th>
                              <td>
                                  <?php
                                    if (${'accStatus' . $accountNumber} == "P") {
                                        echo "Available once processing is completed.";
                                    } else {
                                        echo ${"ultifreeID" . $accountNumber} . "." . ${'accApiDomain' . $accountNumber};
                                    }
                                  ?>
                              </td>
                          </tr>

                          <tr>
                              <th>IP Address:</th>
                              <td>
                                  <?php
                                    if (${'accStatus' . $accountNumber} == "P") {
                                      echo "Available once processing is completed.";
                                    } else {
                                        echo dns_get_record(${"ultifreeID" . $accountNumber} . "." . ${'accApiDomain' . $accountNumber}, DNS_A)[0]["ip"];
                                    }
                                  ?>
                              </td>
                          </tr>

                          <tr>
                              <th>Hosting Volume:</th>
                              <td>
                                  <?php
                                    if (${'accStatus' . $accountNumber} == "P") {
                                        echo "Available once processing is completed.";
                                    } else {
                                        echo ${'accHostingVolume' . $accountNumber};
                                    }
                                  ?>
                              </td>
                          </tr>

                          <tr>
                            <th>Date Created:</th>
                            <td>
                              <?php echo substr(${"accCreationDate" . $accountNumber}, 0, 2) . "/" . substr(${"accCreationDate" . $accountNumber}, 2, 2) . "/" . substr(${"accCreationDate" . $accountNumber}, 4, 4); ?>
                            </td>
                          </tr>
                        </tbody>
                        </table>
                      </div>
                    </div>

                    <div class="card ">
                          <div class="card-body ">
                              <table class="table table-responsive-sm">
                                  <thead class="text-primary">
                                  <th colspan="2" class="text-center">
                                      FTP Details - Details for Connecting with your FTP Client
                                  </th>
                                  </thead>
                                  <tbody>
                                  <tr>
                                      <th>Username:</th>
                                      <td>
                                          <?php echo ${"accUsername" . $accountNumber}; ?>
                                      </td>
                                  </tr>

                                  <tr>
                                      <th>Password:</th>
                                      <td>
                                          <table class="table-borderless table-responsive-stack">
                                              <tr>
                                                  <td class="pl-0">
                                                      <input disabled id="ftpPass" type="password" value="<?php echo ${"accPassword" . $accountNumber}; ?>" class="displayPasswordInput" />
                                                  </td>
                                                  <td class="pl-0">
                                                      <a href="#" for="ftpPass" class="btn btn-warning btn-sm showHidePass">Show/Hide</a>
                                                  </td>
                                              </tr>
                                          </table>
                                      </td>
                                  </tr>

                                  <tr>
                                      <th>Hostname:</th>
                                      <td>
                                          ftpupload.net
                                      </td>
                                  </tr>

                                  <tr>
                                      <th>Port:</th>
                                      <td>
                                          21
                                      </td>
                                  </tr>
                                  </tbody>
                              </table>

                              <div class="alert alert-with-icon alert-info">
                                  <span data-notify="icon" class="nc-icon nc-alert-circle-i"></span>
                                  <span>Please open the control panel before connecting to FTP for the first time!</span>
                              </div>
                          </div>
                      </div>

                      <div class="card ">
                          <div class="card-body ">
                              <table class="table table-responsive-sm display-table">
                                  <thead class="text-primary">
                                  <th colspan="2" class="text-center">
                                      Domains and Subdomains
                                  </th>
                                  </thead>
                                  <tbody>
                                  <?php
                                  if (sizeof(${'listOfDomainsAccount' . $accountNumber}) > 0) {
                                      foreach (${'listOfDomainsAccount' . $accountNumber} as $domain) {
                                          echo "<tr>";
                                          echo "<td colspan='2' class='text-center'>";
                                          echo '<a target="_blank" href="http://www.' . $domain .'">' . $domain . '</a>';
                                          echo "</td>";
                                          echo "</tr>";
                                      }
                                  } else {
                                      echo "<tr>";
                                      echo "<td colspan='2' class='text-center'>";
                                      echo "No (Sub-) Domains :(";
                                      echo "</td>";
                                      echo "</tr>";
                                  }
                                  ?>
                                  </tbody>
                              </table>
                          </div>
                      </div>
                  </div>

                  <div class="col-xl-6">
                    <div class="card ">
                      <div class="card-body ">
                      <table class="table table-responsive-sm">
                        <thead class="text-primary">
                          <th colspan="2" class="text-center">
                            MySQL Details - Details for Connecting to your Database
                          </th>
                        </thead>
                        <tbody>
                          <tr>
                            <th>Username:</th>
                            <td>
                              <?php echo ${"accUsername" . $accountNumber}; ?>
                            </td>
                          </tr>

                          <tr>
                            <th>Password:</th>
                            <td>
                              <table class="table-borderless table-responsive-stack">
                                <tr>
                                  <td class="pl-0">
                                    <input disabled id="mysqlPass" type="password" value="<?php echo ${"accPassword" . $accountNumber}; ?>" class="displayPasswordInput" />
                                  </td>
                                  <td class="pl-0">
                                    <a href="#" for="mysqlPass" class="btn btn-warning btn-sm showHidePass">Show/Hide</a>
                                  </td>
                                </tr>
                              </table>
                            </td>
                          </tr>

                          <tr>
                            <th>Hostname:</th>
                            <td>
                            <?php
                                if (${'accStatus' . $accountNumber} == "P") {
                                    echo "Available once processing is completed.";
                                } else {
                                    echo ${'accSQLServer' . $accountNumber} . ".ultihost.net";
                                }
                            ?>
                            </td>
                          </tr>

                          <tr>
                            <th>Port:</th>
                            <td>
                            3306
                            </td>
                          </tr>

                          <tr>
                            <th>Database Name:</th>
                            <td>
                              <?php echo ${"accUsername" . $accountNumber}; ?>_XXX <i>(Create this in the control panel)</i>
                            </td>
                          </tr>
                        </tbody>
                        </table>
                      </div>
                    </div>

                      <div class="card ">
                          <div class="card-body ">
                              <table class="table table-responsive-sm">
                                  <thead class="text-primary">
                                  <th colspan="2" class="text-center">
                                      Website Builder
                                  </th>
                                  </thead>
                                  <tbody>
                                  <?php
                                  if (${'accStatus' . $accountNumber} == "P") {
                                      echo "<tr>";
                                      echo "<td colspan='2' class='text-center'>";
                                      echo "Website builder unavailable yet, please wait until processing is completed.";
                                      echo "</td>";
                                      echo "</tr>";
                                  } else if (${'accStatus' . $accountNumber} == "S" || ${'accStatus' . $accountNumber} == "D") {
                                      echo "<tr>";
                                      echo "<td colspan='2' class='text-center'>";
                                      echo "Website builder unavailable on this hosting account.";
                                      echo "</td>";
                                      echo "</tr>";
                                  } else if (sizeof(${'listOfDomainsAccount' . $accountNumber}) > 0) {
                                      foreach (${'listOfDomainsAccount' . $accountNumber} as $domain) {
                                          echo "<tr>";
                                              echo "<td class='text-center'>";
                                                echo '<a target="_blank" href="http://www.' . $domain .'">' . $domain . '</a>';
                                              echo "</td>";
                                              echo "<td class='text-center'>";
                                                echo "<form method='post' target='_blank' action='./api/generateSiteProLink.php'>";
                                                    echo '<div class="form-group d-none">';
                                                        echo '<input type="text" class="invisible" name="domainToEdit" value="' . $domain . '">';
                                                        echo '<input type="text" class="invisible" name="ftpUser" value="' . ${'accUsername' . $accountNumber} . '">';
                                                        echo '<input type="text" class="invisible" name="ftpPass" value="' . ${'accPassword' . $accountNumber} . '">';
                                                    echo '</div>';
                                                    echo '<input type="submit" class="btn btn-primary" value="Edit Website" />';
                                                echo "</form>";
                                              echo "</td>";
                                          echo "</tr>";
                                      }
                                  } else {
                                      echo "<tr>";
                                      echo "<td colspan='2' class='text-center'>";
                                      echo "No (Sub-) Domains :(";
                                      echo "</td>";
                                      echo "</tr>";
                                  }
                                  ?>
                                  </tbody>
                              </table>
                              <div class="alert alert-with-icon alert-info">
                                  <span data-notify="icon" class="nc-icon nc-alert-circle-i"></span>
                                  <span>Make sure to open the control panel at least once before using this feature!</span>
                              </div>
                              <div class="alert alert-with-icon alert-warning">
                                  <span data-notify="icon" class="nc-icon nc-alert-circle-i"></span>
                                  <span class="text-dark">Warning: Using this may modify your FTP files!</span>
                              </div>
                          </div>
                      </div>

                <div class="card">
                        <!-- Loading Screen -->
                        <div class="loading_overlay text-center text-primary">
                            <div class="spinner-grow" style="width: 5rem; height: 5rem;" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                        <!-- Loading Screen End -->
                      <div class="card-body">
                          <table class="table-responsive-sm w-100">
                              <thead>
                              <th colspan="2" class="text-center">
                                  Account Controls:
                              </th>
                              </thead>
                              <tbody>
                              <tr>
                                  <td class="text-center">
                                      <?php
                                      // Create the control panel login form
                                      if (${'accStatus' . $accountNumber} == "A" || ${'accStatus' . $accountNumber} == "S") {
                                          echo '<form target="_blank" action="https://cpanel.ultihost.net/login.php" method="post" name="login">';
                                      } else {
                                          echo '<form action="#disabled" name="login">';
                                      }
                                      echo '<label for="mod_login_username" class="collapse input"><span>Username</span><input name="uname" id="mod_login_username" type="text" class="inputbox" alt="username" value="' . ${'accUsername' . $accountNumber} . '"/></label>';
                                      echo '<label for="mod_login_password" class="collapse input"><span>Password</span><input type="password" id="mod_login_password" name="passwd" class="inputbox" alt="password" value="' . ${'accPassword' . $accountNumber} . '"/></label>';
                                      if (${'accStatus' . $accountNumber} == "A" || ${'accStatus' . $accountNumber} == "S") {
                                          echo '<a onclick="this.closest(\'form\').submit(); return false;" class="btn btn-round btn-success btn-block text-white"><i class="nc-icon nc-badge"></i> Control Panel</a>';
                                      } else {
                                          echo '<a href="#" class="btn btn-round btn-success btn-block disabled"><i class="nc-icon nc-badge"></i> Control Panel</a>';
                                      }
                                      echo '</form>';
                                      ?>
                                  </td>
                                  <td class="text-center pl-2">
                                      <?php
                                      // Create the FTP button -> Generate URL
                                      if (${'accStatus' . $accountNumber} == "A") {
                                          $encoded_ftp_url = base64_encode('{"t":"ftp","c":{"p":"' . ${'accPassword' . $accountNumber} . '","i":"\/"}}');
                                          $ftp_url = "https://filemanager.ai/new/#/c/185.27.134.11/" . ${'accUsername' .  $accountNumber} . "/" . $encoded_ftp_url;
                                          echo '<a href="' . $ftp_url . '" target="_BLANK" class="btn btn-round btn-warning btn-block"><i class="nc-icon nc-image"></i> File Manager</a>';
                                      } else {
                                          echo '<a href="#" class="btn btn-round btn-warning btn-block disabled"><i class="nc-icon nc-image"></i> File Manager</a>';
                                      }
                                      ?>
                                  </td>
                              </tr>
                              <tr>
                                  <td class="text-center">
                                      <?php
                                      if (${'accStatus' . $accountNumber} == "A") {
                                          echo '<a href="#" class="btn btn-round btn-default btn-block" data-toggle="modal" data-target="#changePasswordModal"><i class="nc-icon nc-key-25"></i> Change Password</a>';
                                      } else {
                                          echo '<a href="#" class="btn btn-round btn-default btn-block disabled"><i class="nc-icon nc-key-25"></i> Change Password</a>';
                                      }
                                      ?>


                                      <!-- Modal for Changing Password -->
                                      <div class="modal fade" id="changePasswordModal" tabindex="-1" role="dialog" aria-labelledby="Change Password" aria-hidden="true">
                                          <div class="modal-dialog modal-dialog-centered" role="document">
                                              <div class="modal-content">
                                                  <!-- Loading Screen -->
                                                  <div class="loading_overlay_modal text-center text-primary">
                                                      <div class="spinner-grow" style="width: 5rem; height: 5rem;" role="status">
                                                          <span class="sr-only">Loading...</span>
                                                      </div>
                                                  </div>
                                                  <!-- Loading Screen End -->
                                                  <div class="modal-header">
                                                      <h5 class="modal-title" id="changePasswordModalTitle">Change Password</h5>
                                                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                          <span aria-hidden="true">&times;</span>
                                                      </button>
                                                  </div>
                                                  <div class="modal-body">
                                                      <form action="./api/changePassword.php" method="post" id="changePasswordForm">
                                                          <div class="form-group">
                                                              <label for="changePasswordInput">New Password:</label>
                                                              <input type="text" class="form-control" id="changePasswordInput" name="newPassword" required value="<?php echo ${'accPassword' . $accountNumber}; ?>">
                                                              <input type="text" class="invisible" id="changePasswordAccount" name="ultifreeID" value="<?php echo ${'ultifreeID' . $accountNumber}; ?>">
                                                              <input type="text" class="invisible" id="changePasswordReturnDomain" name="returnDomain" value="?acc=<?php echo $accountNumber; ?>&accDisp=<?php echo $displayAccountNumber;?>">
                                                          </div>
                                                      </form>
                                                      <div id="changePasswordInfoError" class="alert alert-danger">
                                                          <span>Sorry, this password is invalid!</span>
                                                      </div>
                                                      <div id="changePasswordInfoSuccess" class="alert alert-success">
                                                          <span>Password is valid, you can change it now!</span>
                                                      </div>
                                                  </div>
                                                  <div class="modal-footer">
                                                      <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                      <button type="button" id="changePasswordFormSubmitButton" class="btn btn-primary" onclick="$('.loading_overlay_modal').css('visibility', 'visible'); $('#changePasswordForm').submit(); return false;">Change Password</button>
                                                  </div>
                                              </div>
                                          </div>
                                      </div>
                                  </td>
                                  <td class="text-center  pl-2">
                                      <a href="#" class="btn btn-round btn-default btn-block" data-toggle="modal" data-target="#changeLabelModal"><i class="nc-icon nc-paper"></i> Change Label</a>

                                      <!-- Modal for Changing Label -->
                                      <div class="modal fade" id="changeLabelModal" tabindex="-1" role="dialog" aria-labelledby="Change Label" aria-hidden="true">
                                          <div class="modal-dialog modal-dialog-centered" role="document">
                                              <div class="modal-content">
                                                  <!-- Loading Screen -->
                                                  <div class="loading_overlay_modal text-center text-primary">
                                                      <div class="spinner-grow" style="width: 5rem; height: 5rem;" role="status">
                                                          <span class="sr-only">Loading...</span>
                                                      </div>
                                                  </div>
                                                  <!-- Loading Screen End -->
                                                  <div class="modal-header">
                                                      <h5 class="modal-title" id="changeLabelModalTitle">Change Label</h5>
                                                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                          <span aria-hidden="true">&times;</span>
                                                      </button>
                                                  </div>
                                                  <div class="modal-body">
                                                      <form action="./api/changeLabel.php" method="post" id="changeLabelForm">
                                                          <div class="form-group">
                                                              <label for="changeLabelInput">New Label:</label>
                                                              <input type="text" minlength="1" class="form-control" id="changeLabelInput" name="newLabel" value="<?php echo ${'accLabel' . $accountNumber}; ?>">
                                                              <input type="text" class="invisible" id="changeLabelAccount" name="accountID" value="<?php echo ${'accountID' . $accountNumber}; ?>">
                                                              <input type="text" class="invisible" id="changeLabelReturnDomain" name="returnDomain" value="?acc=<?php echo $accountNumber; ?>&accDisp=<?php echo $displayAccountNumber;?>">
                                                          </div>
                                                      </form>
                                                  </div>
                                                  <div class="modal-footer">
                                                      <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                      <button type="button" class="btn btn-primary" onclick="$('.loading_overlay_modal').css('visibility', 'visible'); $('#changeLabelForm').submit(); return false;">Change Label</button>
                                                  </div>
                                              </div>
                                          </div>
                                      </div>

                                  </td>
                              </tr>
                              <tr>
                                  <td class="text-center">
                                      <a href="deactivations.php?acc=<?php echo $accountNumber; ?>" class="btn btn-round btn-default btn-block" data-toggle="tooltip" data-placement="left" title="Find out why your account got suspended"><i class="nc-icon nc-lock-circle-open"></i> Deactivation History</a>
                                  </td>
                                  <td class="text-center  pl-2">
                                      <?php
                                      if (${'accStatus' . $accountNumber} == "A") {
                                          echo '<a href="#" class="btn btn-round btn-danger btn-block" data-toggle="modal" data-target="#removeHostingAccountModal"><i class="nc-icon nc-simple-remove"></i> Remove Hosting Account</a>';
                                      } else {
                                          echo '<a href="#" class="btn btn-round btn-danger btn-block disabled"><i class="nc-icon nc-simple-remove"></i> Remove Hosting Account</a>';
                                      }
                                      ?>

                                      <!-- Modal for Removing Hosting Account -->
                                      <div class="modal fade" id="removeHostingAccountModal" tabindex="-1" role="dialog" aria-labelledby="Remove Hosting Account" aria-hidden="true">
                                          <div class="modal-dialog modal-dialog-centered" role="document">
                                              <div class="modal-content">
                                                  <!-- Loading Screen -->
                                                  <div class="loading_overlay_modal text-center text-primary">
                                                      <div class="spinner-grow" style="width: 5rem; height: 5rem;" role="status">
                                                          <span class="sr-only">Loading...</span>
                                                      </div>
                                                  </div>
                                                  <!-- Loading Screen End -->
                                                  <div class="modal-header">
                                                      <h5 class="modal-title" id="removeHostingAccountModalTitle">Delete Hosting Account</h5>
                                                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                          <span aria-hidden="true">&times;</span>
                                                      </button>
                                                  </div>
                                                  <div class="modal-body text-left">
                                                      <p>
                                                          This will disable your website and control panel. The contents of your website will be kept for 60 days, and will then be automatically deleted. You can reactivate your account at any time during these 60 days.
                                                      </p>
                                                      <p class="text-danger">
                                                          <?php
                                                          if (sizeof(${'listOfDomainsAccount' . $accountNumber}) > 0) {
                                                              echo "Before being able to delete your account please remove the following domains from your account: <br>";
                                                              foreach (${'listOfDomainsAccount' . $accountNumber} as $domain) {
                                                                  echo "<br> - " . $domain . "<br>";
                                                              }
                                                              echo "<br> To remove the domains open your control panel, click on 'Addon Domains', remove all domains there, then try clicking this button again!";
                                                          } else {
                                                              echo "<br> Would you like to continue?";
                                                          }
                                                          ?>
                                                      </p>
                                                      <form class="d-none" action="./api/removeAccount.php" method="post" id="removeAccountForm">
                                                          <div class="form-group invisible">
                                                              <input type="text" class="invisible" id="removeAccountID" name="accountID" value="<?php echo ${'ultifreeID' . $accountNumber}; ?>">
                                                              <input type="text" class="invisible" id="removeAccountReturnDomain" name="returnDomain" value="?acc=<?php echo $accountNumber; ?>&accDisp=<?php echo $displayAccountNumber;?>">
                                                          </div>
                                                      </form>
                                                  </div>
                                                  <div class="modal-footer">
                                                      <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                      <?php
                                                      if (sizeof(${'listOfDomainsAccount' . $accountNumber}) == 0) {
                                                          echo '<button type="button" class="btn btn-danger" onclick="$(\'.loading_overlay_modal\').css(\'visibility\', \'visible\'); $(\'#removeAccountForm\').submit(); return false;">Remove Hosting Account</button>';
                                                      }
                                                      ?>
                                                  </div>
                                              </div>
                                          </div>
                                      </div>
                                  </td>
                              </tr>
                              <?php
                                if (${'accStatus' . $accountNumber} == "D") {
                                     echo "<tr>";
                                        echo '<td class="text-center" colspan="2">';
                                            echo '<form class="d-none" action="./api/reactivateAccount.php" method="post" id="reactivateAccountForm">';
                                                echo '<div class="form-group invisible">';
                                                  echo '<input type="text" class="invisible" id="reactivateAccountID" name="accountID" value="' . ${'ultifreeID' . $accountNumber} . '">';
                                                  echo '<input type="text" class="invisible" id="reactivateAccountReturnDomain" name="returnDomain" value="?acc=' . $accountNumber . '&accDisp=' . $displayAccountNumber . '">';
                                                echo '</div>';
                                            echo '</form>';
                                          echo '<button class="btn btn-round btn-success btn-block" onclick="$(\'.loading_overlay\').css(\'visibility\', \'visible\'); $(\'#reactivateAccountForm\').submit();"><i class="nc-icon nc-check"></i> Reactivate Hosting Account</a>';
                                      echo '</td>';
                                    echo '</tr>';
                                }
                              ?>
                              <tr>
                                  <td class="text-center" colspan="2">
                                      <a href="upgrade.php" class="btn btn-round btn-premium btn-block"  onclick="PremiumEvent('Dashboard Host Account', 'Premium CTA');"><i class="nc-icon nc-diamond"></i> Upgrade to Premium</a>
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
                      <?php echo "<br />"; insertAds('dash06', false);?>
                  </div>
              </div>

          </div>
        </div>
      </div>

      <?php include 'footer.php'; ?>

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
