<?php
    /**
     * @var mysqli $conn the DB Connection
     * @var InfinityFree\MofhClient\Client $mofhClient the iFastNet API connection
     * @var string $user the clientID
     */
    require_once('./securityCheck.php');
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    $domain = test_input($_SESSION['newDomain']);
    $username = unique_id($domain);
    $label = test_input($_SESSION['newLabel']);
    $password = test_input($_SESSION['newPassword']);
    $email = test_input($_SESSION['email']);

    // Get Current Date from DB
    $creationDate = $conn->real_escape_string(date("dmY"));

    if (strlen($password) <= 0) {
        try {
            $password = random_str(8);
        } catch (\Exception $e) {
            // Random Password String Error
            $errorMessage = $conn->real_escape_string($e->getMessage());
            $errorLocation = "Create Account - Random String Exception";

            // Insert into DB (if possible)
            $insertDatabaseErrorSQL = "INSERT INTO `errors` (`errorText`, `errorLocation`, `errorTime`) VALUES ('$errorMessage', '$errorLocation', now())";
            $conn->query($insertDatabaseErrorSQL);

            //Redirect to error page
            header("Location: ../../error.php?errorCode=" . $conn->insert_id); die();
        }
    }

    $createAccountRequest = $mofhClient->createAccount([
        'username' => $username,
        'password' => $password,
        'domain' => $domain,
        'email' => $email
    ]);

    $createAccountResponse = $createAccountRequest->send();

    if ($createAccountResponse->isSuccessful()) {
        $panelUsername = $createAccountResponse->getVpUsername();

        $sqlInsertAccount = "INSERT INTO `accounts` (`ultifreeID`,`clientID`, `username`, `password`, `domain`, `label`, `status`, `creationDate`) VALUES ('$username', '$user', '$panelUsername', '$password', '$domain', '$label', 'P', '$creationDate')";
        $conn->query($sqlInsertAccount);

        $sqlGetAccountID = "SELECT `accountID` FROM `accounts` WHERE `ultifreeID` = '$username'";
        $sqlGetAccountIDResult = $conn->query($sqlGetAccountID)->fetch_assoc();
        $accountID = $conn->real_escape_string($sqlGetAccountIDResult['accountID']);

        $sqlGetAllAccountIdsForClient = "SELECT `account1`, `account2`, `account3` FROM `clients` WHERE `clientID` = '$user'";
        $sqlGetAllAccountIdsForClientResult = $conn->query($sqlGetAllAccountIdsForClient)->fetch_assoc();

        if ($sqlGetAllAccountIdsForClientResult['account1'] == NULL) {
            $sqlSetAccount = "UPDATE `clients` SET `account1` = '$accountID' WHERE `clientID` = '$user'";
            $conn->query($sqlSetAccount);
        } else if ($sqlGetAllAccountIdsForClientResult['account2'] == NULL) {
            $sqlSetAccount = "UPDATE `clients` SET `account2` = '$accountID' WHERE `clientID` = '$user'";
            $conn->query($sqlSetAccount);
        } else {
            $sqlSetAccount = "UPDATE `clients` SET `account3` = '$accountID' WHERE `clientID` = '$user'";
            $conn->query($sqlSetAccount);
        }

        try {
            sendMail($panelUsername, $email, $domain);
        } catch (Exception $e) {
            // PHPMailer Exception
            $errorMessage = $conn->real_escape_string($e->getMessage());
            $errorLocation = "Create Account - PHPMailer Exception";

            // Insert into DB (if possible)
            $insertDatabaseErrorSQL = "INSERT INTO `errors` (`errorText`, `errorLocation`, `errorTime`) VALUES ('$errorMessage', '$errorLocation', now())";
            $conn->query($insertDatabaseErrorSQL);

            //Redirect to error page
            header("Location: ../../error.php?errorCode=" . $conn->insert_id); die();
        }

        //Redirect
        header("Location: ../home.php"); die();
    } else {
        // API Error
        $errorMessage = $conn->real_escape_string($createAccountResponse->getMessage());
        $errorLocation = "Create Account - API Error";

        if (str_contains($errorMessage, "allready added to a hosting account")) {
            //Redirect to custom domain creation page
            header("Location: ../createAccountCustomDomain.php?error=taken"); die();
        } else if (str_contains($errorMessage, "not set to valid name servers")) {
            //Redirect to custom domain creation page
            header("Location: ../createAccountCustomDomain.php?error=ns"); die();
        }

        // Insert into DB (if possible)
        $insertDatabaseErrorSQL = "INSERT INTO `errors` (`errorText`, `errorLocation`, `errorTime`) VALUES ('$errorMessage', '$errorLocation', now())";
        $conn->query($insertDatabaseErrorSQL);

        //Redirect to error page
        header("Location: ../../error.php?errorCode=" . $conn->insert_id); die();
    }

/**
 * Send email using SendPulse
 * @throws Exception
 */
    function sendMail($vpUsername, $email, $domain) {
    date_default_timezone_set('Etc/UTC');

    $mail = new PHPMailer(true);

    //Server settings
    $mail->isSMTP();
    $mail->Host       = 'smtp-pulse.com'; // Can include backup
    $mail->SMTPAuth   = true;
    $mail->Username   = 'ultifreehosting@gmail.com';
    $mail->Password   = '4eTSe65JDPap';
    $mail->SMTPSecure = 'ssl'; // tls or ssl
    $mail->Port       = 465;

    //Recipients
    $mail->setFrom('no-reply@ultifreehosting.com', 'Ultifree Hosting');
    $mail->addAddress($email);

    // BCC Automatic Review Thingy from TrustPilot
    $mail->addBcc("ultifreehosting.com+7da092b136@invite.trustpilot.com");

    //Fix encoding issues
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Your new Ultifree Hosting account has been created';
    $mail->Body    = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional //EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

  <html xmlns="http://www.w3.org/1999/xhtml" lang="en">
  <head>
  <!--[if gte mso 9]><xml><o:OfficeDocumentSettings><o:AllowPNG/><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml><![endif]-->
  <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
  <meta content="width=device-width" name="viewport"/>
  <!--[if !mso]><!-->
  <meta content="IE=edge" http-equiv="X-UA-Compatible"/>
  <!--<![endif]-->
  <title></title>
  <!--[if !mso]><!-->
  <!--<![endif]-->
  <style type="text/css">
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
  <style id="media-query" type="text/css">
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
  				min-height: 0;
  				max-height: 0;
  				max-width: 0;
  				display: none;
  				overflow: hidden;
  				font-size: 0;
  			}

  			.desktop_hide {
  				display: block !important;
  				max-height: none !important;
  			}
  		}
  	</style>
  </head>
  <body class="clean-body" style="margin: 0; padding: 0; -webkit-text-size-adjust: 100%; background-color: #a6ffd4;">
  <script type="application/json+trustpilot">
      {
          "recipientName": "Customer",
          "referenceId": "' . $vpUsername . '",
          "recipientEmail": "' . $email . '"
      }
  </script>
  <!--[if IE]><div class="ie-browser"><![endif]-->
  <table bgcolor="#a6ffd4" cellpadding="0" cellspacing="0" class="nl-container" style="table-layout: fixed; vertical-align: top; min-width: 320px; Margin: 0 auto; border-spacing: 0; border-collapse: collapse; mso-table-lspace: 0; mso-table-rspace: 0; background-color: #a6ffd4; width: 100%;"  width="100%">
  <tbody>
  <tr style="vertical-align: top;" valign="top">
  <td style="word-break: break-word; vertical-align: top;" valign="top">
  <!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td align="center" style="background-color:#a6ffd4"><![endif]-->
  <div style="background-color:transparent;">
  <div class="block-grid" style="Margin: 0 auto; min-width: 320px; max-width: 600px; overflow-wrap: break-word; word-wrap: break-word; word-break: break-word; background-color: transparent;">
  <div style="border-collapse: collapse;display: table;width: 100%;background-color:transparent;">
  <!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:transparent;"><tr><td align="center"><table cellpadding="0" cellspacing="0" border="0" style="width:600px"><tr class="layout-full-width" style="background-color:transparent"><![endif]-->
  <!--[if (mso)|(IE)]><td align="center" width="600" style="background-color:transparent;width:600px; border-top: 0px solid transparent; border-left: 0px solid transparent; border-bottom: 0px solid transparent; border-right: 0px solid transparent;" valign="top"><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 0px; padding-left: 0px; padding-top:0px; padding-bottom:0px;"><![endif]-->
  <div class="col num12" style="min-width: 320px; max-width: 600px; display: table-cell; vertical-align: top; width: 600px;">
  <div style="width:100% !important;">
  <!--[if (!mso)&(!IE)]><!-->
  <div style="border-top:0 solid transparent; border-left:0 solid transparent; border-bottom:0 solid transparent; border-right:0 solid transparent; padding: 0;">
  <!--<![endif]-->
  <table border="0" cellpadding="0" cellspacing="0" class="divider" role="presentation" style="table-layout: fixed; vertical-align: top; border-spacing: 0; border-collapse: collapse; mso-table-lspace: 0; mso-table-rspace: 0; min-width: 100%; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;"  width="100%">
  <tbody>
  <tr style="vertical-align: top;" valign="top">
  <td class="divider_inner" style="word-break: break-word; vertical-align: top; min-width: 100%; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; padding: 25px;" valign="top">
  <table align="center" border="0" cellpadding="0" cellspacing="0" class="divider_content" role="presentation" style="table-layout: fixed; vertical-align: top; border-spacing: 0; border-collapse: collapse; mso-table-lspace: 0; mso-table-rspace: 0; border-top: 0 solid transparent; width: 100%;"  width="100%">
  <tbody>
  <tr style="vertical-align: top;" valign="top">
  <td style="word-break: break-word; vertical-align: top; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;" valign="top"><span></span></td>
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
  <div style="background-color:transparent;">
  <div class="block-grid" style="Margin: 0 auto; min-width: 320px; max-width: 600px; overflow-wrap: break-word; word-wrap: break-word; word-break: break-word; background-color: #FFFFFF;">
  <div style="border-collapse: collapse;display: table;width: 100%;background-color:#FFFFFF;">
  <!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:transparent;"><tr><td align="center"><table cellpadding="0" cellspacing="0" border="0" style="width:600px"><tr class="layout-full-width" style="background-color:#FFFFFF"><![endif]-->
  <!--[if (mso)|(IE)]><td align="center" width="600" style="background-color:#FFFFFF;width:600px; border-top: 0px solid transparent; border-left: 0px solid transparent; border-bottom: 0px solid transparent; border-right: 0px solid transparent;" valign="top"><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 0px; padding-left: 0px; padding-top:0px; padding-bottom:0px;"><![endif]-->
  <div class="col num12" style="min-width: 320px; max-width: 600px; display: table-cell; vertical-align: top; width: 600px;">
  <div style="width:100% !important;">
  <!--[if (!mso)&(!IE)]><!-->
  <div style="border-top:0 solid transparent; border-left:0 solid transparent; border-bottom:0 solid transparent; border-right:0 solid transparent; padding: 0;">
  <!--<![endif]-->
  <div align="center" class="img-container center fixedwidth" style="padding-right: 0;padding-left: 0;">
  <!--[if mso]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr style="line-height:0px"><td style="padding-right: 0px;padding-left: 0px;" align="center"><![endif]-->
  <div style="font-size:1px;line-height:20px"> </div><img align="center" alt="Image" border="0" class="center fixedwidth" src="https://ultifreehosting.com/images/logo.png" style="text-decoration: none; -ms-interpolation-mode: bicubic; border: 0; height: auto; width: 100%; max-width: 210px; display: block;" title="Image" width="210"/>
  <!--[if mso]></td></tr></table><![endif]-->
  </div>
  <table border="0" cellpadding="0" cellspacing="0" class="divider" role="presentation" style="table-layout: fixed; vertical-align: top; border-spacing: 0; border-collapse: collapse; mso-table-lspace: 0; mso-table-rspace: 0; min-width: 100%; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;"  width="100%">
  <tbody>
  <tr style="vertical-align: top;" valign="top">
  <td class="divider_inner" style="word-break: break-word; vertical-align: top; min-width: 100%; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; padding: 15px;" valign="top">
  <table align="center" border="0" cellpadding="0" cellspacing="0" class="divider_content" role="presentation" style="table-layout: fixed; vertical-align: top; border-spacing: 0; border-collapse: collapse; mso-table-lspace: 0; mso-table-rspace: 0; border-top: 0 solid transparent; width: 100%;"  width="100%">
  <tbody>
  <tr style="vertical-align: top;" valign="top">
  <td style="word-break: break-word; vertical-align: top; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;" valign="top"><span></span></td>
  </tr>
  </tbody>
  </table>
  </td>
  </tr>
  </tbody>
  </table>
  <!--[if mso]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 10px; padding-left: 30px; padding-top: 10px; padding-bottom: 5px; font-family: Arial, sans-serif"><![endif]-->
  <div style="color:#555555;font-family:Helvetica Neue, Helvetica, Arial, sans-serif;line-height:1.2;padding: 10px 10px 5px 30px;">
  <div style="font-size: 12px; line-height: 1.2; color: #555555; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; mso-line-height-alt: 14px;">
  <p style="font-size: 14px; line-height: 1.2; word-break: break-word; mso-line-height-alt: 17px; margin: 0;"><strong><span style="font-size: 24px;">Hello there,</span></strong></p>
  <p style="font-size: 14px; line-height: 1.2; word-break: break-word; mso-line-height-alt: 17px; margin: 0;">Welcome to Ultifree Hosting! Your hosting account will be setup over the next few minutes. This email contains all the information you will need about your account.</p>
  </div>
  </div>
  <!--[if mso]></td></tr></table><![endif]-->
  <!--[if mso]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 10px; padding-left: 30px; padding-top: 10px; padding-bottom: 10px; font-family: Arial, sans-serif"><![endif]-->
  <div style="color:#555555;font-family:Helvetica Neue, Helvetica, Arial, sans-serif;line-height:1.2;padding: 10px 10px 10px 30px;">
  <div style="font-size: 14px; line-height: 1.2; color: #555555; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; mso-line-height-alt: 17px;">
  <p style="line-height: 1.2; word-break: break-word; mso-line-height-alt: NaNpx; margin: 0;"><strong>Account Information:</strong></p>
  <p style="line-height: 1.2; word-break: break-word; mso-line-height-alt: NaNpx; margin: 0;"> </p>
  <p style="line-height: 1.2; word-break: break-word; mso-line-height-alt: NaNpx; margin: 0;">Here are the details you will need to log into your control panel, where you can begin to create your website.</p>
  <p style="line-height: 1.2; word-break: break-word; mso-line-height-alt: NaNpx; margin: 0;"> </p>
  <p style="line-height: 1.2; word-break: break-word; mso-line-height-alt: NaNpx; margin: 0;">Domain: ' . $domain . '</p>
  <p style="line-height: 1.2; word-break: break-word; mso-line-height-alt: NaNpx; margin: 0;">Username: ' . $vpUsername . '</p>
  <p style="line-height: 1.2; word-break: break-word; mso-line-height-alt: NaNpx; margin: 0;">Password: (Can be found in your Client Area)</p>
  <p style="line-height: 1.2; word-break: break-word; mso-line-height-alt: NaNpx; margin: 0;"> </p>
  <p style="line-height: 1.2; word-break: break-word; mso-line-height-alt: NaNpx; margin: 0;"><strong>Domain Information:</strong></p>
  <p style="line-height: 1.2; word-break: break-word; mso-line-height-alt: NaNpx; margin: 0;"> </p>
  <p style="line-height: 1.2; word-break: break-word; mso-line-height-alt: NaNpx; margin: 0;">It can take up to 48 hours for the website to become visible due to a process called DNS propagation. The actual time depends on many factors, your Internet connection being the most important factor. Therefore, don\'t worry if you cannot access your website immediately. </p>
  <p style="line-height: 1.2; word-break: break-word; mso-line-height-alt: NaNpx; margin: 0;"> </p>
  <p style="line-height: 1.2; word-break: break-word; mso-line-height-alt: NaNpx; margin: 0;">Furthermore, if you have a custom domain (example.com), and would like to use our hosting service, you can do so by changing your nameservers to ours: </p>
  <p style="line-height: 1.2; word-break: break-word; mso-line-height-alt: NaNpx; margin: 0;"> </p>
  <p style="line-height: 1.2; word-break: break-word; mso-line-height-alt: NaNpx; margin: 0;">- ns1.ultihost.net</p>
  <p style="line-height: 1.2; word-break: break-word; mso-line-height-alt: NaNpx; margin: 0;">- ns2.ultihost.net</p>
  <p style="line-height: 1.2; word-break: break-word; mso-line-height-alt: NaNpx; margin: 0;"> </p>
  <p style="line-height: 1.2; word-break: break-word; mso-line-height-alt: NaNpx; margin: 0;">Then going to the Addon Domains section in your control panel and adding your domain there.</p>
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
  <div style="background-color:transparent;">
  <div class="block-grid" style="Margin: 0 auto; min-width: 320px; max-width: 600px; overflow-wrap: break-word; word-wrap: break-word; word-break: break-word; background-color: #EDEDED;">
  <div style="border-collapse: collapse;display: table;width: 100%;background-color:#EDEDED;">
  <!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:transparent;"><tr><td align="center"><table cellpadding="0" cellspacing="0" border="0" style="width:600px"><tr class="layout-full-width" style="background-color:#EDEDED"><![endif]-->
  <!--[if (mso)|(IE)]><td align="center" width="600" style="background-color:#EDEDED;width:600px; border-top: 0px solid transparent; border-left: 0px solid transparent; border-bottom: 0px solid transparent; border-right: 0px solid transparent;" valign="top"><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 5px; padding-left: 5px; padding-top:5px; padding-bottom:5px;"><![endif]-->
  <div class="col num12" style="min-width: 320px; max-width: 600px; display: table-cell; vertical-align: top; width: 600px;">
  <div style="width:100% !important;">
  <!--[if (!mso)&(!IE)]><!-->
  <div style="border-top:0 solid transparent; border-left:0 solid transparent; border-bottom:0 solid transparent; border-right:0 solid transparent; padding-top:5px; padding-bottom:5px; padding-right: 5px; padding-left: 5px;">
  <!--<![endif]-->
  <!--[if mso]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 30px; padding-left: 30px; padding-top: 20px; padding-bottom: 25px; font-family: Arial, sans-serif"><![endif]-->
  <div style="color:#555555;font-family:Helvetica Neue, Helvetica, Arial, sans-serif;line-height:1.8;padding-top:20px;padding-right:30px;padding-bottom:25px;padding-left:30px;">
  <div style="font-size: 12px; line-height: 1.8; color: #555555; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; mso-line-height-alt: 22px;">
  <p style="font-size: 14px; line-height: 1.8; word-break: break-word; mso-line-height-alt: 25px; margin: 0;"><span style="font-size: 14px;">Cheers,</span></p>
  <p style="font-size: 12px; line-height: 1.8; word-break: break-word; mso-line-height-alt: 22px; margin: 0;"><em><strong><span style="font-size: 14px;">Ultifree Hosting Team</span></strong></em></p>
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
  <div style="background-color:transparent;">
  <div class="block-grid" style="Margin: 0 auto; min-width: 320px; max-width: 600px; overflow-wrap: break-word; word-wrap: break-word; word-break: break-word; background-color: transparent;">
  <div style="border-collapse: collapse;display: table;width: 100%;background-color:transparent;">
  <!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:transparent;"><tr><td align="center"><table cellpadding="0" cellspacing="0" border="0" style="width:600px"><tr class="layout-full-width" style="background-color:transparent"><![endif]-->
  <!--[if (mso)|(IE)]><td align="center" width="600" style="background-color:transparent;width:600px; border-top: 0px solid transparent; border-left: 0px solid transparent; border-bottom: 0px solid transparent; border-right: 0px solid transparent;" valign="top"><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 0px; padding-left: 0px; padding-top:5px; padding-bottom:5px;"><![endif]-->
  <div class="col num12" style="min-width: 320px; max-width: 600px; display: table-cell; vertical-align: top; width: 600px;">
  <div style="width:100% !important;">
  <!--[if (!mso)&(!IE)]><!-->
  <div style="border-top:0 solid transparent; border-left:0 solid transparent; border-bottom:0 solid transparent; border-right:0 solid transparent; padding-top:5px; padding-bottom:5px; padding-right: 0; padding-left: 0;">
  <!--<![endif]-->
  <!--[if mso]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 10px; padding-left: 10px; padding-top: 10px; padding-bottom: 10px; font-family: Arial, sans-serif"><![endif]-->
  <div style="color:#555555;font-family:Helvetica Neue, Helvetica, Arial, sans-serif;line-height:1.2;padding-top:10px;padding-right:10px;padding-bottom:10px;padding-left:10px;">
  <div style="font-size: 14px; line-height: 1.2; color: #555555; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; mso-line-height-alt: 17px;">
  <p style="font-size: 11px; line-height: 1.2; word-break: break-word; text-align: center; mso-line-height-alt: 13px; margin: 0;"><span style="font-size: 11px;">© Ultifree Hosting. All rights reserved.</span></p>
  </div>
  </div>
  <!--[if mso]></td></tr></table><![endif]-->
  <table border="0" cellpadding="0" cellspacing="0" class="divider" role="presentation" style="table-layout: fixed; vertical-align: top; border-spacing: 0; border-collapse: collapse; mso-table-lspace: 0; mso-table-rspace: 0; min-width: 100%; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;" width="100%">
  <tbody>
  <tr style="vertical-align: top;" valign="top">
  <td class="divider_inner" style="word-break: break-word; vertical-align: top; min-width: 100%; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; padding-top: 25px; padding-right: 25px; padding-bottom: 25px; padding-left: 25px;" valign="top">
  <table align="center" border="0" cellpadding="0" cellspacing="0" class="divider_content" role="presentation" style="table-layout: fixed; vertical-align: top; border-spacing: 0; border-collapse: collapse; mso-table-lspace: 0; mso-table-rspace: 0; border-top: 0 solid transparent; width: 100%;" width="100%">
  <tbody>
  <tr style="vertical-align: top;" valign="top">
  <td style="word-break: break-word; vertical-align: top; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;" valign="top"><span></span></td>
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
  </html>';

    $mail->AltBody = 'Hello there,

  Welcome to Ultifree Hosting! Your hosting account will be setup over the next few minutes. This email contains all the information you will need about your account.

  Account Information:

  Here are the details you will need to login to your control panel, where you can begin to create your website.

  Domain: ' . $domain . '
  Username: ' . $vpUsername . '
  Password: (Can be found in your Client Area)

  Domain Information:

  It can take up to 48 hours for the website to become visible due to a process called DNS propagation. The actual time depends on many factors, your Internet connection being the most important factor. Therefore, don\'t worry if you cannot access your website immediately.

  Furthermore, if you have a custom domain (example.com), and would like to use our hosting service, you can do so by changing your nameservers to ours:

  - ns1.ultihost.net

  - ns2.ultihost.net

  Then going to the Addon Domains section in your control panel and adding your domain there.

  Cheers,
  Ultifree Hosting Team';

    $mail->send();
}