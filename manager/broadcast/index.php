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

$html   =   '<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AvoComm</title>

    <style>
        body {
            margin: 0;
            padding: 0;
        }
        
        @media (max-width:680px) {
            .outer {
                width: 100% !important;
                padding: 0 15px !important;
            }
        }
        
        @media (max-width:630px) {
            .para {
                padding: 30px !important;
                font-size: 18px !important;
            }
        }
        
        @media (max-width:480px) {
            .para {
                padding: 30px 0 !important;
                font-size: 16px !important;
            }
        }
    </style>
</head>

<body style="margin: 0;">
    <table class="outer" cellpadding="0" cellspacing="0" border="0" align="center" width="650" bgcolor="#fff">
        <tbody>
            <tr>
                <td style="background: transparent linear-gradient(90deg, #83C885 0%, #83C884 0%, #A4D47F 4%, #C0DE7A 7%, #D7E676 12%, #E9ED73 17%, #F5F171 23%, #FCF470 31%, #FFF570 50%, #FCF470 69%, #F5F171 77%, #E9ED73 83%, #D7E676 88%, #C0DE7A 93%, #A4D47F 96%, #83C884 100%, #83C885 100%) 0% 0% no-repeat padding-box; max-width: 650px; padding: 0 15px;">
                    <table cellpadding="0" class="header" cellspacing="0" border="0" align="center" width="100%">
                        <tbody>
                            <tr>
                                <td style="height: 43px; max-width: 650px;"></td>
                            </tr>
                            <tr>
                                <td style="text-align: center; max-width: 650px;">
                                    <a href="#" style="display: inline-block;"><img width="100%" src="https://avocomm.avocadosfrommexico.com/images/email-logo.png" alt=""></a>
                                </td>
                            </tr>
                            <tr>
                                <td style="height: 43px; max-width: 650px;"></td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            </tr>
            <tr>
                <td style="max-width: 650px;">
                    <table cellpadding="0" class="banner" cellspacing="0" border="0" width="100%">
                        <tbody>
                            <tr>
                                <td class="para" style="padding: 31px 72px 40px; max-width: 650px; font-size: 21px; line-height: 1.5; font-family: sans-serif ;font-weight: 600;">
                                    Hola,
                                    <br><br>'.$description.'
                                    <br><br>
                                    <a style="color:#006bad;" href="https://avocomm.avocadosfrommexico.com">https://avocomm.avocadosfrommexico.com/</a>
                                    <br><br> Stay tuned for this reminder next month! -Avocados From Mexico Team<br><br>
                                </td>
                            </tr>
                      </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td style="max-width: 650px;">
                    <table cellpadding="0" class="footer" cellspacing="0" border="0" width="100%">
                        <tbody>
                            <tr>
                                <td style="max-width: 650px;"><img width="100%" src="https://avocomm.avocadosfrommexico.com/images/email-footer.png" alt="footer"></td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
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
            $html   =   '<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AvoComm</title>

    <style>
        body {
            margin: 0;
            padding: 0;
        }
        
        @media (max-width:680px) {
            .outer {
                width: 100% !important;
                padding: 0 15px !important;
            }
        }
        
        @media (max-width:630px) {
            .para {
                padding: 30px !important;
                font-size: 18px !important;
            }
        }
        
        @media (max-width:480px) {
            .para {
                padding: 30px 0 !important;
                font-size: 16px !important;
            }
        }
    </style>
</head>

<body style="margin: 0;">
    <table class="outer" cellpadding="0" cellspacing="0" border="0" align="center" width="650" bgcolor="#fff">
        <tbody>
            <tr>
                <td style="background: transparent linear-gradient(90deg, #83C885 0%, #83C884 0%, #A4D47F 4%, #C0DE7A 7%, #D7E676 12%, #E9ED73 17%, #F5F171 23%, #FCF470 31%, #FFF570 50%, #FCF470 69%, #F5F171 77%, #E9ED73 83%, #D7E676 88%, #C0DE7A 93%, #A4D47F 96%, #83C884 100%, #83C885 100%) 0% 0% no-repeat padding-box; max-width: 650px; padding: 0 15px;">
                    <table cellpadding="0" class="header" cellspacing="0" border="0" align="center" width="100%">
                        <tbody>
                            <tr>
                                <td style="height: 43px; max-width: 650px;"></td>
                            </tr>
                            <tr>
                                <td style="text-align: center; max-width: 650px;">
                                    <a href="#" style="display: inline-block;"><img width="100%" src="https://avocomm.avocadosfrommexico.com/images/email-logo.png" alt=""></a>
                                </td>
                            </tr>
                            <tr>
                                <td style="height: 43px; max-width: 650px;"></td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            </tr>
            <tr>
                <td style="max-width: 650px;">
                    <table cellpadding="0" class="banner" cellspacing="0" border="0" width="100%">
                        <tbody>
                            <tr>
                                <td class="para" style="padding: 31px 72px 40px; max-width: 650px; font-size: 21px; line-height: 1.5; font-family: sans-serif ;font-weight: 600;">
                                    Hola,
                                    <br><br>'.$description.'
                                    <br><br>
                                    <a style="color:#006bad;" href="https://avocomm.avocadosfrommexico.com">https://avocomm.avocadosfrommexico.com/</a>
                                    <br><br> Stay tuned for this reminder next month! -Avocados From Mexico Team<br><br>
                                </td>
                            </tr>
                      </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td style="max-width: 650px;">
                    <table cellpadding="0" class="footer" cellspacing="0" border="0" width="100%">
                        <tbody>
                            <tr>
                                <td style="max-width: 650px;"><img width="100%" src="https://avocomm.avocadosfrommexico.com/images/email-footer.png" alt="footer"></td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
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