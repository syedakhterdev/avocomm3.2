<?php
$title =  'administrative';
$subtitle = 'broadcast';
require( '../config.php' );
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

<?php require( '../includes/header_new.php' );?>
    <script type="text/javascript" src="<?php echo ADMIN_URL?>/includes/tinymce/tinymce.min.js"></script>
    <div class="dashboard-sub-menu-sec">
        <div class="container">
            <div class="sub-menu-sec">
                <?php include('../includes/administrative_sub_nav.php')?>
            </div>
        </div>
    </div>

    <div class="latest_activities hd-grid">
        <div class="container">
            <div class="heading_sec">
                <h2><bold>BroadCast</bold></h2>
            </div>
        </div>
    </div>
    <div class="main-form">
        <div class="container">
            <?php if(isset($_SESSION['msg'])){?><div class="alert alert-success"> <?php  echo $_SESSION['msg']; unset($_SESSION['msg']);?> </div><?php }?>
            <form action="<?php echo ADMIN_URL?>/broadcast/index.php" role="form" method="POST">
                <input type="hidden" name="insert" value="1">
                <div class="form-group text-box">
                    <label for="fname">Email Subject *</label><br>
                    <input value="<?php if(isset($_POST['subject'])){ echo $_POST['subject'];}?>" type="text"  name="subject" required />
                </div>
                <div class="form-group text-box">
                    <label for="html">Email Description *</label><br>
                    <textarea id="description"  name="description" rows="20"><?php if(isset($_POST['description'])){ echo $_POST['description'];}?></textarea>
                </div>
                <div class="form-group text-box">
                    <label for="fname">Test Email</label><br>
                    <input type="email"  name="test_email" />
                </div>
                <button type="submit" name="test_button">
                    <img src="<?php echo ADMIN_URL?>/images/send-to-test.png" onmouseover="this.src='<?php echo ADMIN_URL?>/images/send-to-test-hvr.png'" onmouseout="this.src='<?php echo ADMIN_URL?>/images/send-to-test.png'" alt="login-submit-btn">
                </button>
                <button type="submit" name="submit">
                    <img src="<?php echo ADMIN_URL?>/images/send-to-all.png" onmouseover="this.src='<?php echo ADMIN_URL?>/images/send-to-all-hvr.png'" onmouseout="this.src='<?php echo ADMIN_URL?>/images/send-to-all.png'" alt="login-submit-btn">
                </button>
            </form>
        </div>
    </div>


    <script>$(document).ready(function () {
            $('form:first *:input[type!=hidden]:first').focus();
        });</script>

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
<?php $conn->close();  } ?>
<?php include('../includes/footer_new.php');?>