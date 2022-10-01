<?php
    /**
     * @var mysqli $conn The DB Object
     * @var array $allAnnouncements The array of announcements
     * @var string $user the clientID
     */
    require_once('./api/securityCheck.php');

    $certificateID = $conn->real_escape_string($_GET['cert']);

    $fetchSslCertificate = "SELECT * FROM `certificates` WHERE `certificateID` LIKE '$certificateID'";
    $sslCertificateResult = $conn->query($fetchSslCertificate)->fetch_assoc();

    $certificateClient = $sslCertificateResult['clientID'];

    if ($certificateClient != $user) {
        // Certificate Doesn't Belong To User -> Redirect
        header("Location: ../login.php"); die();
    }

    $domain = $sslCertificateResult['domain'];
    $type = $sslCertificateResult['type'];
    $privateKey = $sslCertificateResult['privateKey'];

    $allCerts = $sslCertificateResult['cert'];
    $allCerts = explode("-----BEGIN CERTIFICATE-----", $allCerts);
    $certificate = "-----BEGIN CERTIFICATE-----" . $allCerts[1];

    $status = $sslCertificateResult['status'];
    $expireDate = Date('Y-m-d', strtotime($sslCertificateResult['expireDate']));
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
                    <a class="navbar-brand" href="#">SSL Certificate <span class="d-none d-md-inline">for <?php echo $domain; ?></span></a>
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
                            <?php echo "<br />"; insertAds('dash21', false);?>
                        </div>
                    </div>

                    <div class="row">

                        <div class="col-xl-8">
                            <div class="card ">
                                <div class="card-body">
                                    <table class="table table-responsive-sm">
                                        <thead class="text-primary">
                                        <th colspan="2" class="text-center">
                                            SSL Certificate Details - Information about your SSL Certificate
                                        </th>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <th>Domain:</th>
                                            <td>
                                                <?php echo $domain; ?>
                                            </td>
                                        </tr>

                                        <tr>
                                            <th>Type:</th>
                                            <td>
                                                <?php
                                                    switch($type) {
                                                        case "zero":
                                                            echo "Free";
                                                            break;
                                                        default:
                                                            echo "Self-Signed";
                                                            break;
                                                    }
                                                ?>
                                            </td>
                                        </tr>

                                        <tr>
                                            <th>Status:</th>
                                            <td>
                                                <?php
                                                    switch($status) {
                                                        case 0:
                                                            echo '<span class="badge badge-primary">Pending Verification</span>';
                                                            break;
                                                        case 1:
                                                            echo '<span class="badge badge-success">Issued</span>';
                                                            break;
                                                        case 2:
                                                            echo '<span class="badge badge-danger">Expired</span>';
                                                            break;
                                                        case 3:
                                                            echo '<span class="badge badge-danger">Revoked</span>';
                                                            break;
                                                    }
                                                ?>
                                            </td>
                                        </tr>

                                        <tr>
                                            <th>Expire Date:</th>
                                            <td><?php echo $expireDate; ?></td>
                                        </tr>
                                        </tbody>
                                    </table>

                                    <hr />

                                    <div id="accordion">
                                        <div>
                                            <div class="card-header text-center" id="viewPrivKeyAndCertHeading">
                                                <button class="btn btn-warning" data-toggle="collapse" data-target="#privKeyAndCertCollapse" aria-expanded="false" aria-controls="privKeyAndCertCollapse">
                                                    View Private Key and Certificate
                                                </button>
                                            </div>

                                            <div id="privKeyAndCertCollapse" class="collapse hide" aria-labelledby="viewPrivKeyAndCertHeading" data-parent="#accordion">
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-xl-5 offset-xl-1">
                                                            <h6 class="text-primary">Private Key</h6>
                                                            <pre><?php echo $privateKey; ?></pre>
                                                        </div>

                                                        <div class="col-xl-5">
                                                            <h6 class="text-primary">Certificate</h6>
                                                            <pre><?php echo $certificate;?></pre>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4">
                            <div class="card ">
                                <div class="card-header ">
                                    <h5 class="card-title">Installing Your Certificate</h5>
                                </div>
                                <div class="card-body">
                                    <p>
                                        Now that you've generated your certificate you need to install it! When using our hosting follow these steps:
                                    </p>
                                    <ol>
                                        <li>Open your Control Panel, click on the "SSL/TLS" section.</li>
                                        <li>Click on "Configure" next to the domain you'd like to add the certificate to.</li>
                                        <li>Paste your private key into the Private Key Field and click "Upload Key". (Including the first and last lines - eg. "---BEGIN PRIVATE KEY--")</li>
                                        <li>Paste your certificate into the Certificate Field and click "Upload Certificate". (Including the first and last lines - eg. "---BEGIN CERTIFICATE--")</li>
                                    </ol>
                                    <p>
                                        Now your certificate has been installed! Wait 1-2 minutes, then access your website using https:// and your website is secure!
                                    </p>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-12">
                            <?php echo "<br />"; insertAds('dash22', false);?>
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

