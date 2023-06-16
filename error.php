<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <link rel="apple-touch-icon" sizes="76x76" href="./dashboard/assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="./dashboard/assets/img/favicon.png">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <title>
    Something went wrong - Ultifree Hosting
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
        require_once 'dashboard/api/settings.php';
        require_once 'ad_inserter.php';
      ?>

    <div class="main-panel bg-muted">
      <div class="content">

          <div class="row">
              <div class="col-12">
                  <?php echo "<br />"; insertAds('dash15', false);?>
              </div>
          </div>

        <div class="row">
          <div class="col-md-12">

            <div class="row">
                <div class="col-xl-4 col-md-8 offset-xl-4 offset-md-2">
                    <a href="index.php" class="text-center">
                        <img id="logo" class="mx-auto d-block p-2" src="./dashboard/assets/img/logo.png" alt="Ultifree Hosting Logo" />
                    </a>

                    <div class="card">
                        <div class="card-body ">
                            <h4 class="text-center text-danger">Oops, something went wrong!</h4>
                            <p class="text-center">
                                Please try again.
                            </p>
                            <ul>
                                <li>Error Code: <?php echo $_GET['errorCode']; ?></li>
                                <li>Your Hosting Account Username (eg. ultif_12345678, if you have one)</li>
                                <li>Any other relevant errors you saw</li>
                                <li>An explanation of what you did before the error happened</li>
                            </ul>
                            <p class="text-center"><a href="login.php">Return to Dashboard</a></p>
                        </div>
                    </div>
                </div>
            </div>

          </div>
        </div>

          <div class="row">
              <div class="col-12">
                  <?php echo "<br />"; insertAds('dash16', false);?>
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
  <script src="./dashboard/assets/js/loggedOut.js" <?php echo 'onload="function dashboardErrorEventWhenReady() { if (typeof DashboardErrorEvent != \'undefined\') { DashboardErrorEvent(\'' . $_GET['errorCode'] . '\'); } else { setTimeout(dashboardErrorEventWhenReady, 100); } } dashboardErrorEventWhenReady();"'; ?>></script>
  <!--  Notifications Plugin    -->
  <script src="./dashboard/assets/js/plugins/bootstrap-notify.js"></script>
  <!-- Control Center for Now Ui Dashboard: parallax effects etc -->
  <script src="./dashboard/assets/js/paper-dashboard.js?v=2.0.1" type="text/javascript"></script>
</body>

</html>
