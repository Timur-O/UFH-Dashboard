<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <link rel="apple-touch-icon" sizes="76x76" href="./dashboard/assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="./dashboard/assets/img/favicon.png">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <title>
    Verify Your Email - Ultifree Hosting
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
        //Import PHPMailer
        use \PHPMailer\PHPMailer\PHPMailer;
        use \PHPMailer\PHPMailer\Exception;

        session_start();

        // Placeholder for DB issues
        $email = "your email address";
        $clientID = null;
        if (isset($_SESSION['clientIDtoVerify'])) {
            $clientID = $conn->real_escape_string($_SESSION['clientIDtoVerify']);
        } else {
            //Redirect
            header("Location: login.php"); die();
        }

        $sql = "SELECT `email` FROM `clients` WHERE `clientID` = '$clientID'";
        $result = $conn->query($sql)->fetch_assoc();
        $email = $result['email'];

        $verificationHash = $conn->real_escape_string(md5(rand(0,1000)));
        $sql = "UPDATE `clients` SET `verification` = '$verificationHash' WHERE `clientID` = '$clientID'";
        $result = $conn->query($sql);

        $verificationURL = "https://app.ultifreehosting.com/verified.php?client=" . $clientID . "&verificationCode=" . $verificationHash;

        sendVerificationMail($email, $verificationURL);

        $fetchAnnouncements = "SELECT * FROM `announcements`";

        // Collect Information
        $allAnnouncements = $conn->query($fetchAnnouncements)->fetch_all(MYSQLI_ASSOC);
    ?>

    <div class="main-panel bg-muted">
      <div class="content">
        <div class="row">
          <div class="col-md-12">

            <?php
              // Get Announcements and convert to HTML
              foreach($allAnnouncements as $row) {
                if ($row["location"] == "verify") {
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
                    <?php insertAds('veri01', false);?>
                </div>

                <div class="col-xl-4 col-md-8 offset-xl-1 offset-md-2">
                    <a href="index.php" class="text-center">
                        <img id="logo" class="mx-auto d-block p-2" src="./dashboard/assets/img/logo.png" alt="Ultifree Hosting Logo" />
                    </a>

                    <div class="card">
                        <div class="card-body ">
                            <p class="text-center">
                                We've sent you a link by email. Please press the button in the email to verify your account!
                            </p>

                            <p class="text-center"><a href="login.php">Login to your account!</a></p>
                        </div>
                    </div>
                </div>

                <div class="col-xl-2 offset-xl-1 col-md-12">
                    <?php insertAds('veri02', false);?>
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
  <!-- Google Analytics Verification Event -->
  <script>BeginVerificationEvent();</script>
</body>

</html>

<?php
function sendVerificationMail($email, $verificationURL) {
    date_default_timezone_set('Etc/UTC');

    $mail = new PHPMailer(true);

//Server settings
    $mail->isSMTP();
    $mail->Host       = 'smtp-pulse.com'; // Can include backup a;b
    $mail->SMTPAuth   = true;
    $mail->Username   = 'ultifreehosting@gmail.com';
    $mail->Password   = '4eTSe65JDPap';
    $mail->SMTPSecure = 'ssl'; // tls or ssl
    $mail->Port       = 465;

//Sender & Recipients
    $mail->setFrom('no-reply@ultifreehosting.com', 'Ultifree Hosting');
    $mail->addAddress($email);

//Fix encoding issues
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

// Content
    $mail->isHTML(true);
    $mail->Subject = 'Verify your email';
    $mail->Body    = "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional //EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>

<html xmlns='http://www.w3.org/1999/xhtml' xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:v='urn:schemas-microsoft-com:vml'>
<head>
<!--[if gte mso 9]><xml><o:OfficeDocumentSettings><o:AllowPNG/><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml><![endif]-->
<meta content='text/html; charset=utf-8' http-equiv='Content-Type'/>
<meta content='width=device-width' name='viewport'/>
<!--[if !mso]><!-->
<meta content='IE=edge' http-equiv='X-UA-Compatible'/>
<!--<![endif]-->
<title></title>
<!--[if !mso]><!-->
<!--<![endif]-->
<style type='text/css'>
		body {
			margin: 0;
			padding: 0;
		}

		table,
		td,
		tr {
			vertical-align: top;
			border-collapse: collapse;
		}

		* {
			line-height: inherit;
		}

		a[x-apple-data-detectors=true] {
			color: inherit !important;
			text-decoration: none !important;
		}
	</style>
<style id='media-query' type='text/css'>
		@media (max-width: 620px) {

			.block-grid,
			.col {
				min-width: 320px !important;
				max-width: 100% !important;
				display: block !important;
			}

			.block-grid {
				width: 100% !important;
			}

			.col {
				width: 100% !important;
			}

			.col>div {
				margin: 0 auto;
			}

			img.fullwidth,
			img.fullwidthOnMobile {
				max-width: 100% !important;
			}

			.no-stack .col {
				min-width: 0 !important;
				display: table-cell !important;
			}

			.no-stack.two-up .col {
				width: 50% !important;
			}

			.no-stack .col.num4 {
				width: 33% !important;
			}

			.no-stack .col.num8 {
				width: 66% !important;
			}

			.no-stack .col.num4 {
				width: 33% !important;
			}

			.no-stack .col.num3 {
				width: 25% !important;
			}

			.no-stack .col.num6 {
				width: 50% !important;
			}

			.no-stack .col.num9 {
				width: 75% !important;
			}

			.video-block {
				max-width: none !important;
			}

			.mobile_hide {
				min-height: 0px;
				max-height: 0px;
				max-width: 0px;
				display: none;
				overflow: hidden;
				font-size: 0px;
			}

			.desktop_hide {
				display: block !important;
				max-height: none !important;
			}
		}
	</style>
</head>
<body class='clean-body' style='margin: 0; padding: 0; -webkit-text-size-adjust: 100%; background-color: #a6ffd4;'>
<script type='application/json+trustpilot'>
    {
        'recipientName': 'Customer',
        'referenceId': '" . $email . "',
        'recipientEmail': '" . $email . "'
    }
</script>
<!--[if IE]><div class='ie-browser'><![endif]-->
<table bgcolor='#a6ffd4' cellpadding='0' cellspacing='0' class='nl-container' role='presentation' style='table-layout: fixed; vertical-align: top; min-width: 320px; Margin: 0 auto; border-spacing: 0; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #a6ffd4; width: 100%;' valign='top' width='100%'>
<tbody>
<tr style='vertical-align: top;' valign='top'>
<td style='word-break: break-word; vertical-align: top;' valign='top'>
<!--[if (mso)|(IE)]><table width='100%' cellpadding='0' cellspacing='0' border='0'><tr><td align='center' style='background-color:#a6ffd4'><![endif]-->
<div style='background-color:transparent;'>
<div class='block-grid' style='Margin: 0 auto; min-width: 320px; max-width: 600px; overflow-wrap: break-word; word-wrap: break-word; word-break: break-word; background-color: transparent;'>
<div style='border-collapse: collapse;display: table;width: 100%;background-color:transparent;'>
<!--[if (mso)|(IE)]><table width='100%' cellpadding='0' cellspacing='0' border='0' style='background-color:transparent;'><tr><td align='center'><table cellpadding='0' cellspacing='0' border='0' style='width:600px'><tr class='layout-full-width' style='background-color:transparent'><![endif]-->
<!--[if (mso)|(IE)]><td align='center' width='600' style='background-color:transparent;width:600px; border-top: 0px solid transparent; border-left: 0px solid transparent; border-bottom: 0px solid transparent; border-right: 0px solid transparent;' valign='top'><table width='100%' cellpadding='0' cellspacing='0' border='0'><tr><td style='padding-right: 0px; padding-left: 0px; padding-top:0px; padding-bottom:0px;'><![endif]-->
<div class='col num12' style='min-width: 320px; max-width: 600px; display: table-cell; vertical-align: top; width: 600px;'>
<div style='width:100% !important;'>
<!--[if (!mso)&(!IE)]><!-->
<div style='border-top:0px solid transparent; border-left:0px solid transparent; border-bottom:0px solid transparent; border-right:0px solid transparent; padding-top:0px; padding-bottom:0px; padding-right: 0px; padding-left: 0px;'>
<!--<![endif]-->
<table border='0' cellpadding='0' cellspacing='0' class='divider' role='presentation' style='table-layout: fixed; vertical-align: top; border-spacing: 0; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; min-width: 100%; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;' valign='top' width='100%'>
<tbody>
<tr style='vertical-align: top;' valign='top'>
<td class='divider_inner' style='word-break: break-word; vertical-align: top; min-width: 100%; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; padding-top: 25px; padding-right: 25px; padding-bottom: 25px; padding-left: 25px;' valign='top'>
<table align='center' border='0' cellpadding='0' cellspacing='0' class='divider_content' role='presentation' style='table-layout: fixed; vertical-align: top; border-spacing: 0; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; border-top: 0px solid transparent; width: 100%;' valign='top' width='100%'>
<tbody>
<tr style='vertical-align: top;' valign='top'>
<td style='word-break: break-word; vertical-align: top; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;' valign='top'><span></span></td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>
<!--[if (!mso)&(!IE)]><!-->
</div>
<!--<![endif]-->
</div>
</div>
<!--[if (mso)|(IE)]></td></tr></table><![endif]-->
<!--[if (mso)|(IE)]></td></tr></table></td></tr></table><![endif]-->
</div>
</div>
</div>
<div style='background-color:transparent;'>
<div class='block-grid' style='Margin: 0 auto; min-width: 320px; max-width: 600px; overflow-wrap: break-word; word-wrap: break-word; word-break: break-word; background-color: #FFFFFF;'>
<div style='border-collapse: collapse;display: table;width: 100%;background-color:#FFFFFF;'>
<!--[if (mso)|(IE)]><table width='100%' cellpadding='0' cellspacing='0' border='0' style='background-color:transparent;'><tr><td align='center'><table cellpadding='0' cellspacing='0' border='0' style='width:600px'><tr class='layout-full-width' style='background-color:#FFFFFF'><![endif]-->
<!--[if (mso)|(IE)]><td align='center' width='600' style='background-color:#FFFFFF;width:600px; border-top: 0px solid transparent; border-left: 0px solid transparent; border-bottom: 0px solid transparent; border-right: 0px solid transparent;' valign='top'><table width='100%' cellpadding='0' cellspacing='0' border='0'><tr><td style='padding-right: 0px; padding-left: 0px; padding-top:0px; padding-bottom:0px;'><![endif]-->
<div class='col num12' style='min-width: 320px; max-width: 600px; display: table-cell; vertical-align: top; width: 600px;'>
<div style='width:100% !important;'>
<!--[if (!mso)&(!IE)]><!-->
<div style='border-top:0px solid transparent; border-left:0px solid transparent; border-bottom:0px solid transparent; border-right:0px solid transparent; padding-top:0px; padding-bottom:0px; padding-right: 0px; padding-left: 0px;'>
<!--<![endif]-->
<table border='0' cellpadding='0' cellspacing='0' class='divider' role='presentation' style='table-layout: fixed; vertical-align: top; border-spacing: 0; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; min-width: 100%; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;' valign='top' width='100%'>
<tbody>
<tr style='vertical-align: top;' valign='top'>
<td class='divider_inner' style='word-break: break-word; vertical-align: top; min-width: 100%; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; padding-top: 15px; padding-right: 15px; padding-bottom: 15px; padding-left: 15px;' valign='top'>
<table align='center' border='0' cellpadding='0' cellspacing='0' class='divider_content' role='presentation' style='table-layout: fixed; vertical-align: top; border-spacing: 0; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; border-top: 0px solid transparent; width: 100%;' valign='top' width='100%'>
<tbody>
<tr style='vertical-align: top;' valign='top'>
<td style='word-break: break-word; vertical-align: top; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;' valign='top'><span></span></td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>
<div align='center' class='img-container center fixedwidth' style='padding-right: 0px;padding-left: 0px;'>
<!--[if mso]><table width='100%' cellpadding='0' cellspacing='0' border='0'><tr style='line-height:0px'><td style='padding-right: 0px;padding-left: 0px;' align='center'><![endif]--><img align='center' alt='Image' border='0' class='center fixedwidth' src='https://ultifreehosting.com/images/logo.png' style='text-decoration: none; -ms-interpolation-mode: bicubic; border: 0; height: auto; width: 100%; max-width: 210px; display: block;' title='Image' width='210'/>
<!--[if mso]></td></tr></table><![endif]-->
</div>
<table border='0' cellpadding='0' cellspacing='0' class='divider' role='presentation' style='table-layout: fixed; vertical-align: top; border-spacing: 0; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; min-width: 100%; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;' valign='top' width='100%'>
<tbody>
<tr style='vertical-align: top;' valign='top'>
<td class='divider_inner' style='word-break: break-word; vertical-align: top; min-width: 100%; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; padding-top: 15px; padding-right: 15px; padding-bottom: 15px; padding-left: 15px;' valign='top'>
<table align='center' border='0' cellpadding='0' cellspacing='0' class='divider_content' role='presentation' style='table-layout: fixed; vertical-align: top; border-spacing: 0; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; border-top: 0px solid transparent; width: 100%;' valign='top' width='100%'>
<tbody>
<tr style='vertical-align: top;' valign='top'>
<td style='word-break: break-word; vertical-align: top; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;' valign='top'><span></span></td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>
<!--[if mso]><table width='100%' cellpadding='0' cellspacing='0' border='0'><tr><td style='padding-right: 10px; padding-left: 30px; padding-top: 10px; padding-bottom: 5px; font-family: Arial, sans-serif'><![endif]-->
<div style='color:#555555;font-family:Helvetica Neue, Helvetica, Arial, sans-serif;line-height:1.2;padding-top:10px;padding-right:10px;padding-bottom:5px;padding-left:30px;'>
<div style='font-size: 12px; line-height: 1.2; color: #555555; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; mso-line-height-alt: 14px;'>
<p style='font-size: 14px; line-height: 1.2; word-break: break-word; mso-line-height-alt: 17px; margin: 0;'><strong><span style='font-size: 24px;'>Hello there,</span></strong></p>
<p style='font-size: 14px; line-height: 1.2; word-break: break-word; mso-line-height-alt: 17px; margin: 0;'>Welcome to Ultifree Hosting! To complete the verification process for " . $email . ", please click the button.</p>
</div>
</div>
<!--[if mso]></td></tr></table><![endif]-->
<div align='left' class='button-container' style='padding-top:10px;padding-right:10px;padding-bottom:10px;padding-left:30px;'>
<!--[if mso]><table width='100%' cellpadding='0' cellspacing='0' border='0' style='border-spacing: 0; border-collapse: collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;'><tr><td style='padding-top: 10px; padding-right: 10px; padding-bottom: 10px; padding-left: 30px' align='left'><v:roundrect xmlns:v='urn:schemas-microsoft-com:vml' xmlns:w='urn:schemas-microsoft-com:office:word' href='INSERT URL' style='height:25.5pt; width:196.5pt; v-text-anchor:middle;' arcsize='9%' stroke='false' fillcolor='#95f8d2'><w:anchorlock/><v:textbox inset='0,0,0,0'><center style='color:#000000; font-family:Arial, sans-serif; font-size:12px'><![endif]--><a href='" . $verificationURL . "' style='-webkit-text-size-adjust: none; text-decoration: none; display: inline-block; color: #000000; background-color: #95f8d2; border-radius: 3px; -webkit-border-radius: 3px; -moz-border-radius: 3px; width: auto; width: auto; border-top: 1px solid #95f8d2; border-right: 1px solid #95f8d2; border-bottom: 1px solid #95f8d2; border-left: 1px solid #95f8d2; padding-top: 5px; padding-bottom: 5px; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; text-align: center; mso-border-alt: none; word-break: keep-all;' target='_blank'><span style='padding-left:20px;padding-right:20px;font-size:12px;display:inline-block;'><span style='font-size: 12px; line-height: 2; word-break: break-word; mso-line-height-alt: 24px;'>VERIFY YOUR EMAIL ADDRESS</span></span></a>
<!--[if mso]></center></v:textbox></v:roundrect></td></tr></table><![endif]-->
</div>
<table border='0' cellpadding='0' cellspacing='0' class='divider' role='presentation' style='table-layout: fixed; vertical-align: top; border-spacing: 0; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; min-width: 100%; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;' valign='top' width='100%'>
<tbody>
<tr style='vertical-align: top;' valign='top'>
<td class='divider_inner' style='word-break: break-word; vertical-align: top; min-width: 100%; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; padding-top: 15px; padding-right: 15px; padding-bottom: 15px; padding-left: 15px;' valign='top'>
<table align='center' border='0' cellpadding='0' cellspacing='0' class='divider_content' role='presentation' style='table-layout: fixed; vertical-align: top; border-spacing: 0; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; border-top: 0px solid transparent; width: 100%;' valign='top' width='100%'>
<tbody>
<tr style='vertical-align: top;' valign='top'>
<td style='word-break: break-word; vertical-align: top; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;' valign='top'><span></span></td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>
<!--[if (!mso)&(!IE)]><!-->
</div>
<!--<![endif]-->
</div>
</div>
<!--[if (mso)|(IE)]></td></tr></table><![endif]-->
<!--[if (mso)|(IE)]></td></tr></table></td></tr></table><![endif]-->
</div>
</div>
</div>
<div style='background-color:transparent;'>
<div class='block-grid' style='Margin: 0 auto; min-width: 320px; max-width: 600px; overflow-wrap: break-word; word-wrap: break-word; word-break: break-word; background-color: #EDEDED;'>
<div style='border-collapse: collapse;display: table;width: 100%;background-color:#EDEDED;'>
<!--[if (mso)|(IE)]><table width='100%' cellpadding='0' cellspacing='0' border='0' style='background-color:transparent;'><tr><td align='center'><table cellpadding='0' cellspacing='0' border='0' style='width:600px'><tr class='layout-full-width' style='background-color:#EDEDED'><![endif]-->
<!--[if (mso)|(IE)]><td align='center' width='600' style='background-color:#EDEDED;width:600px; border-top: 0px solid transparent; border-left: 0px solid transparent; border-bottom: 0px solid transparent; border-right: 0px solid transparent;' valign='top'><table width='100%' cellpadding='0' cellspacing='0' border='0'><tr><td style='padding-right: 0px; padding-left: 0px; padding-top:5px; padding-bottom:5px;'><![endif]-->
<div class='col num12' style='min-width: 320px; max-width: 600px; display: table-cell; vertical-align: top; width: 600px;'>
<div style='width:100% !important;'>
<!--[if (!mso)&(!IE)]><!-->
<div style='border-top:0px solid transparent; border-left:0px solid transparent; border-bottom:0px solid transparent; border-right:0px solid transparent; padding-top:5px; padding-bottom:5px; padding-right: 0px; padding-left: 0px;'>
<!--<![endif]-->
<!--[if mso]><table width='100%' cellpadding='0' cellspacing='0' border='0'><tr><td style='padding-right: 30px; padding-left: 30px; padding-top: 30px; padding-bottom: 30px; font-family: Arial, sans-serif'><![endif]-->
<div style='color:#555555;font-family:Helvetica Neue, Helvetica, Arial, sans-serif;line-height:1.8;padding-top:30px;padding-right:30px;padding-bottom:30px;padding-left:30px;'>
<div style='font-size: 12px; line-height: 1.8; color: #555555; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; mso-line-height-alt: 22px;'>
<p style='font-size: 14px; line-height: 1.8; word-break: break-word; mso-line-height-alt: 25px; margin: 0;'><span style='font-size: 14px;'>If you didn't attempt to verify your email address with our service, delete this email.</span></p>
<p style='font-size: 14px; line-height: 1.8; word-break: break-word; mso-line-height-alt: 25px; margin: 0;'><span style='font-size: 14px;'>Cheers,</span></p>
<p style='font-size: 12px; line-height: 1.8; word-break: break-word; mso-line-height-alt: 22px; margin: 0;'><em><strong><span style='font-size: 14px;'>Ultifree Hosting Team</span></strong></em></p>
</div>
</div>
<!--[if mso]></td></tr></table><![endif]-->
<!--[if (!mso)&(!IE)]><!-->
</div>
<!--<![endif]-->
</div>
</div>
<!--[if (mso)|(IE)]></td></tr></table><![endif]-->
<!--[if (mso)|(IE)]></td></tr></table></td></tr></table><![endif]-->
</div>
</div>
</div>
<div style='background-color:transparent;'>
<div class='block-grid' style='Margin: 0 auto; min-width: 320px; max-width: 600px; overflow-wrap: break-word; word-wrap: break-word; word-break: break-word; background-color: transparent;'>
<div style='border-collapse: collapse;display: table;width: 100%;background-color:transparent;'>
<!--[if (mso)|(IE)]><table width='100%' cellpadding='0' cellspacing='0' border='0' style='background-color:transparent;'><tr><td align='center'><table cellpadding='0' cellspacing='0' border='0' style='width:600px'><tr class='layout-full-width' style='background-color:transparent'><![endif]-->
<!--[if (mso)|(IE)]><td align='center' width='600' style='background-color:transparent;width:600px; border-top: 0px solid transparent; border-left: 0px solid transparent; border-bottom: 0px solid transparent; border-right: 0px solid transparent;' valign='top'><table width='100%' cellpadding='0' cellspacing='0' border='0'><tr><td style='padding-right: 0px; padding-left: 0px; padding-top:5px; padding-bottom:5px;'><![endif]-->
<div class='col num12' style='min-width: 320px; max-width: 600px; display: table-cell; vertical-align: top; width: 600px;'>
<div style='width:100% !important;'>
<!--[if (!mso)&(!IE)]><!-->
<div style='border-top:0px solid transparent; border-left:0px solid transparent; border-bottom:0px solid transparent; border-right:0px solid transparent; padding-top:5px; padding-bottom:5px; padding-right: 0px; padding-left: 0px;'>
<!--<![endif]-->
<!--[if mso]><table width='100%' cellpadding='0' cellspacing='0' border='0'><tr><td style='padding-right: 10px; padding-left: 10px; padding-top: 10px; padding-bottom: 10px; font-family: Arial, sans-serif'><![endif]-->
<div style='color:#555555;font-family:Helvetica Neue, Helvetica, Arial, sans-serif;line-height:1.2;padding-top:10px;padding-right:10px;padding-bottom:10px;padding-left:10px;'>
<div style='font-size: 14px; line-height: 1.2; color: #555555; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; mso-line-height-alt: 17px;'>
<p style='font-size: 11px; line-height: 1.2; word-break: break-word; text-align: center; mso-line-height-alt: 13px; margin: 0;'><span style='font-size: 11px;'>© Ultifree Hosting. All rights reserved.</span></p>
</div>
</div>
<!--[if mso]></td></tr></table><![endif]-->
<table border='0' cellpadding='0' cellspacing='0' class='divider' role='presentation' style='table-layout: fixed; vertical-align: top; border-spacing: 0; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; min-width: 100%; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;' valign='top' width='100%'>
<tbody>
<tr style='vertical-align: top;' valign='top'>
<td class='divider_inner' style='word-break: break-word; vertical-align: top; min-width: 100%; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; padding-top: 25px; padding-right: 25px; padding-bottom: 25px; padding-left: 25px;' valign='top'>
<table align='center' border='0' cellpadding='0' cellspacing='0' class='divider_content' role='presentation' style='table-layout: fixed; vertical-align: top; border-spacing: 0; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; border-top: 0px solid transparent; width: 100%;' valign='top' width='100%'>
<tbody>
<tr style='vertical-align: top;' valign='top'>
<td style='word-break: break-word; vertical-align: top; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;' valign='top'><span></span></td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>
<!--[if (!mso)&(!IE)]><!-->
</div>
<!--<![endif]-->
</div>
</div>
<!--[if (mso)|(IE)]></td></tr></table><![endif]-->
<!--[if (mso)|(IE)]></td></tr></table></td></tr></table><![endif]-->
</div>
</div>
</div>
<!--[if (mso)|(IE)]></td></tr></table><![endif]-->
</td>
</tr>
</tbody>
</table>
<!--[if (IE)]></div><![endif]-->
</body>
</html>";

    $mail->AltBody = 'Hello there,

Welcome to Ultifree Hosting! To complete the verification process for ' . $email . ', please click the button.

' . $verificationURL . ' 

If you did not attempt to verify your email address with our service, delete this email.

Cheers,

Ultifree Hosting Team';

    $mail->send();
}
?>
