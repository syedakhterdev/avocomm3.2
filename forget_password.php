<?php

session_start();
require( 'config.php' );
require( 'manager/includes/pdo.php' );


function getRandomString( $length = 8 ) {
    $characters = '0123456789';
    $charactersLength = strlen( $characters );
    $randomString = '';
    for ( $i = 0; $i < $length; $i++ ) {
        $randomString .= $characters[rand( 0, $charactersLength - 1 )];
    }
    return $randomString;
}
$msg = '';
$err = '';
if(isset($_POST['submit_form'])){

    $sql = 'SELECT * FROM users WHERE email = ? LIMIT 1;';
    $users = $conn->query( $sql, array( $_POST['email']) );
    if ( $conn->num_rows()>0 ) {
        $code = getRandomString(8);
        $user = $conn->fetch( $users );
        require_once('manager/includes/EmailManager.php');
        $mail = new EmailManager(
            $conn,
            '',
            3, // use template # 1
            $user['email'],
            stripslashes( $user['first_name'] . ' ' . $user['last_name'] ),
            '',
            '',
            true,
            [
                'NAME' => stripslashes( $user['first_name'] . ' ' . $user['last_name'] ),
                'HOST' => $_SERVER['HTTP_HOST'],
                'VERIFY_LINK' => '<a href="https://' . SITE_URL . '/set_password.php?code=' . $code . '&email=' . $user['email'] . '">Reset Your Password</a>'
            ]
        );
        $mail->send();
        $sql = 'UPDATE users SET set_password = ? WHERE email = ? LIMIT 1;';
        $conn->exec( $sql, array( $code, $user['email'] ) );
        $_SESSION['msg']    =   'Please check your email to reset your password';
        header('Location: '.SITE_URL.'/forget_password.php');
        exit;

    }else{
        $_SESSION['err']    =   'This email address is not exist';
        header('Location: '.SITE_URL.'/forget_password.php');
        exit;
    }

}
include_once 'header-login-new.php';
     ?>

    <!-- banner sec start -->
    <section class="forget-pass-banner banner">
        <div class="container">
            <div class="banner-inner">
                <h2>FORGOT YOUR <span>PASSWORD?</span></h2>
                <p>That’s okay, it happens! Enter your email below:</p>
                <?php if(isset($_SESSION['msg'])){?> <p class="success-msg"><?php  echo $_SESSION['msg']; unset($_SESSION['msg']);?></p>  <?php }?>
                <?php if(isset($_SESSION['err'])){?> <p class="error"><?php  echo $_SESSION['err']; unset($_SESSION['err']);?></p>  <?php }?>
                <form action="<?php echo SITE_URL?>/forget_password.php" method="POST">
                    <div class="form-group">
                        <input type="email" required name="email" placeholder="Enter Your Email">
                    </div>
                    <button type="submit" name="submit_form" value="Submit">
                        <img
                                src="<?php echo SITE_URL?>/images/forget-submit-btn.png"
                                onmouseover="this.src='<?php echo SITE_URL?>/images/forget-submit-hvr-btn.png'"
                                onmouseout="this.src='<?php echo SITE_URL?>/images/forget-submit-btn.png'"
                                alt="login-submit-btn"
                        />
                    </button>
                </form>
            </div>
        </div>
    </section>
    <!-- banner sec end -->


    <?php
include_once 'footer-login-new.php';
?>