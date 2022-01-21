<?php
session_start();
if ((int) $_SESSION['admin_sa']) {
ini_set('display_errors', 1);
require( '../includes/pdo.php' );
require( '../includes/check_login.php' );

if (isset($_POST['submit']) && $_POST['subject']!='' && $_POST['description']) {
$description    =   $_POST['description'];
$sql = 'SELECT * FROM users WHERE agree_to_terms =?';
$users = $conn->query( $sql,array( 1) );
$email_array    =   array();

if ( $conn->num_rows()>0 ) {
while ($row = $conn->fetch($users)) {
array_push($email_array,$row['email']);
}

}
if(count($email_array)>0){
error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_STRICT);

require( '../includes/PHPMailer.php' );
require( '../includes/SMTP.php' );
require( '../includes/Exception.php' );

$mail = new PHPMailer\PHPMailer\PHPMailer();
$mail->IsSMTP();
$mail->CharSet = 'UTF-8';

$mail->Host       = "us-smtp-outbound-1.mimecast.com";
$mail->SMTPDebug  = 0;                     // enables SMTP debug information (for testing)
$mail->SMTPAuth   = true;                  // enable SMTP authentication
$mail->Port       = 587;                    // set the SMTP port for the GMAIL server
$mail->Username   = "ambo@avocadosfrommexico.com";
$mail->Password   = "Ww3BR*nn663OivMYfY8NbaWxuf3!";
$mail->setFrom('avocomm@avocadosfrommexico.com', 'AvoComm');

while (list ($key, $val) = each ($email_array)) {
    $mail->addBCC($val);
    /*$mail->AddAddress($val);*/
}
$mail->addReplyTo('info@avocadosfrommexico.com', 'avocomm');

$html   =   '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html data-editor-version="2" class="sg-campaigns" xmlns="http://www.w3.org/1999/xhtml">

<head>
    <title>Avocado</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="x-apple-disable-message-reformatting" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="x-apple-disable-message-reformatting">
    <!--[if !mso]>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <![endif]-->
    <style type="text/css">
        #outlook a {
            padding: 0;
        }

        .ReadMsgBody {
            width: 100%;
        }

        .ExternalClass {
            width: 100%;
        }

        .ExternalClass * {
            line-height: 100%;
        }

        body {
            margin: 0;
            padding: 0;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }

        table,
        td {
            border-collapse: collapse;
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
            font-size: 0em;
            font-family: \'Lato\', \'Roboto\', \'RobotoDraft\', -apple-system, BlinkMacSystemFont, Tahoma, Helvetica, Arial, sans-serif !important;
        }

        img {
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
            -ms-interpolation-mode: bicubic;
        }

        p {
            display: block !important;
            margin: 0 !important;
        }

        table {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
            font-size: 0em;
        }



        @media only screen and (max-width:480px) {
            table {
                width: 100% !important;
            }
            table tr td img.logo-img {
                width: 100% !important;
            }
            td.td-outer {
                padding: 15px !important;
            }
        }

    </style>

</head>

<body style="margin: 0 !important; padding: 0 !important;">
<table align="center" width="600" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td class="td-outer" style="border: 1px solid #CCC; border-radius: 10px; padding: 30px;">
            <table width="100%" align="center"  border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td class="header" align="center" style="padding:30px 10px; background: #017dc0;">
                        <a target="_blank" href="#">
                            <img class="logo-img" style="vertical-align: middle;" alt="" width="400" src="https://avocomm.avocadosfrommexico.com/images/avo_comm_img.png">
                        </a>
                    </td>
                </tr>
             
                <tr>
                    <td style="font-size: 14px; line-height: 20px; color: #666; font-weight: 400; font-family: arial; padding-top: 15px; padding-bottom: 5px;">'
                        .$description.
                        '</td>
                </tr>

            </table>
        </td>
    </tr>
</table>
</body>
</html>';

$mail->isHTML(true);                                  // Set email format to HTML
$mail->Subject = $_POST['subject'];
$mail->Body    = $html;
$mail->AltBody = '';
$mail->send();

}
$_SESSION['msg']    =   'Successfully sent email to all users.';
header("Location: index.php");
exit;

}
if (isset($_POST['test_button']) && $_POST['subject']!='' && $_POST['description']) {
        $description    =   $_POST['description'];

            require( '../includes/PHPMailer.php' );
            require( '../includes/SMTP.php' );
            require( '../includes/Exception.php' );

            $mail = new PHPMailer\PHPMailer\PHPMailer();
            $mail->IsSMTP();
            $mail->CharSet = 'UTF-8';

            $mail->Host       = "us-smtp-outbound-1.mimecast.com";
            $mail->SMTPDebug  = 0;                     // enables SMTP debug information (for testing)
            $mail->SMTPAuth   = true;                  // enable SMTP authentication
            $mail->Port       = 587;                    // set the SMTP port for the GMAIL server
            $mail->Username   = "ambo@avocadosfrommexico.com";
            $mail->Password   = "Ww3BR*nn663OivMYfY8NbaWxuf3!";
            $mail->setFrom('avocomm@avocadosfrommexico.com', 'AvoComm');
            $mail->AddAddress($_POST['test_email']);
            $mail->addReplyTo('info@avocadosfrommexico.com', 'avocomm');
            $html   =   '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html data-editor-version="2" class="sg-campaigns" xmlns="http://www.w3.org/1999/xhtml">

<head>
    <title>Avocado</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="x-apple-disable-message-reformatting" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="x-apple-disable-message-reformatting">
    <!--[if !mso]>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <![endif]-->
    <style type="text/css">
        #outlook a {
            padding: 0;
        }

        .ReadMsgBody {
            width: 100%;
        }

        .ExternalClass {
            width: 100%;
        }

        .ExternalClass * {
            line-height: 100%;
        }

        body {
            margin: 0;
            padding: 0;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }

        table,
        td {
            border-collapse: collapse;
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
            font-size: 0em;
            font-family: \'Lato\', \'Roboto\', \'RobotoDraft\', -apple-system, BlinkMacSystemFont, Tahoma, Helvetica, Arial, sans-serif !important;
        }

        img {
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
            -ms-interpolation-mode: bicubic;
        }

        p {
            display: block !important;
            margin: 0 !important;
        }

        table {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
            font-size: 0em;
        }



        @media only screen and (max-width:480px) {
            table {
                width: 100% !important;
            }
            table tr td img.logo-img {
                width: 100% !important;
            }
            td.td-outer {
                padding: 15px !important;
            }
        }

    </style>

</head>

<body style="margin: 0 !important; padding: 0 !important;">
<table align="center" width="600" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td class="td-outer" style="border: 1px solid #CCC; border-radius: 10px; padding: 30px;">
            <table width="100%" align="center"  border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td class="header" align="center" style="padding:30px 10px; background: #017dc0;">
                        <a target="_blank" href="#">
                            <img class="logo-img" style="vertical-align: middle;" alt="" width="400" src="https://avocomm.avocadosfrommexico.com/images/avo_comm_img.png">
                        </a>
                    </td>
                </tr>         
                <tr>
                    <td style="font-size: 14px; line-height: 20px; color: #666; font-weight: 400; font-family: arial; padding-top: 15px; padding-bottom: 5px;">'
                .$description.
                '</td>
                </tr>

            </table>
        </td>
    </tr>
</table>
</body>
</html>';

            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = $_POST['subject'];
            $mail->Body    = $html;
            $mail->AltBody = '';
            $mail->send();
            $_SESSION['msg']    =   'Test Email successfully sent.';
    }
    ?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Broadcast</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link href="/manager/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <link href="/manager/css/imagine.css" rel="stylesheet">
    <link rel="icon" href="/assets/cropped-favicon-150x150.png" sizes="32x32">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300' rel='stylesheet' type='text/css'>
    <script>$(document).ready(function () {
            $('form:first *:input[type!=hidden]:first').focus();
        });</script>
    <script type="text/javascript" src="../includes/tinymce/tinymce.min.js"></script>

    <script type="text/javascript">tinymce.init({
            selector: "textarea#description",
            plugins: ["link image hr fullscreen media table textcolor code paste lists advlist","anchor"],
            toolbar: "anchor | undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link code responsivefilemanager table | forecolor backcolor",
            external_filemanager_path: "/manager/includes/tinymce/plugins/filemanager/",
            filemanager_title: "File manager", relative_urls: false, image_advtab: true,
            external_plugins: {"filemanager": "/manager/includes/tinymce/plugins/filemanager/plugin.min.js"},
            paste_as_text: true
        });
    </script>

    <script type="text/javascript">tinymce.init({
            selector: "textarea#updates",
            plugins: ["link image hr fullscreen media table textcolor code paste lists advlist","anchor"],
            toolbar: "anchor | undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link code responsivefilemanager table | forecolor backcolor",
            external_filemanager_path: "/manager/includes/tinymce/plugins/filemanager/",
            filemanager_title: "File manager", relative_urls: false, image_advtab: true,
            external_plugins: {"filemanager": "/manager/includes/tinymce/plugins/filemanager/plugin.min.js"},
            paste_as_text: true
        });
    </script>

</head>

<body>

<?php include( '../includes/header.php' ); ?>

<div class="container-fluid" id="main">
    <div class="row row-offcanvas row-offcanvas-left">

        <?php include( '../includes/nav.php' ); ?>

        <div class="col main pt-5 mt-3">
            <div class="row mgr_heading">
                <div class="col-lg-10">
                    <h3>Broadcast</h3>
                </div>
            </div>

            <ol class="breadcrumb bc-3">
                <li><a href="/manager/menu.php">Dashboard</a></li>
                <li>&nbsp;/&nbsp;</li>
                <li><strong>BroadCast</strong></li>
            </ol>

            <div class="row my-4 mgr_body  program_edit program_updates_add shopper">
                <div class="col-lg-10 col-md-8">

                    <form action="index.php" role="form" method="POST">
                        <input type="hidden" name="insert" value="1">
                        <?php if(isset($_SESSION['msg'])){?><div class="alert alert-danger"> <?php  echo $_SESSION['msg']; unset($_SESSION['msg']);?> </div><?php }?>
                        <table style="width: 100%;">

                            <tr>
                                <td>
                                    <div class="form-group row">
                                        <div class="col-sm-12">
                                            <h4>Email Subject *</h4>
                                            <input value="<?php if(isset($_POST['subject'])){ echo $_POST['subject'];}?>" type="text"  name="subject" class="form-control" required />
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <div class="form-group row">
                                        <div class="col-sm-12">
                                            <h4>Email Description *</h4>
                                            <textarea id="description"  name="description" class="form-control" rows="20"><?php if(isset($_POST['description'])){ echo $_POST['description'];}?></textarea>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <h4>Test Email</h4>
                                    <div class="form-group row">
                                        <div class="col-sm-6">
                                            <input type="email"  name="test_email" class="form-control" />

                                        </div>
                                        <div class="col-sm-6">

                                            <button type="submit" name="test_button" class="btn action_btn">Sent To Test User</button>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                        </table>

                        <div class="form-group row">
                            <div class="col-sm-12">
                                <button type="submit" name="submit" class="btn action_btn float-right">Sent to all users</button>
                            </div>
                        </div>

                    </form>
                </div>

            </div>
            <!--/row-->

            <footer class="container-fluid">
                <p class="text-right small">©2019 All rights reserved.</p>
            </footer>

        </div>
        <!--/main col-->

    </div>

</div>

</body>

</html>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
<?php $conn->close();  } ?>
