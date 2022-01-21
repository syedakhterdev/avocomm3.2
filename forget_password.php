<?php

session_start();
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
                'VERIFY_LINK' => '<a href="https://' . $_SERVER['HTTP_HOST'] . '/set_password.php?code=' . $code . '&email=' . $user['email'] . '">Reset Your Password</a>'
            ]
        );
        $mail->send();
        $sql = 'UPDATE users SET set_password = ? WHERE email = ? LIMIT 1;';
        $conn->exec( $sql, array( $code, $user['email'] ) );
        $_SESSION['err']    =   'Please check your email to reset your password';
        header('Location: forget_password.php');
        exit;

    }else{
        $_SESSION['err']    =   'This email address is not exist';
        header('Location: forget_password.php');
        exit;
    }

}

/*if(isset($_POST['email']) && isset($_POST['password'])){

    $sql = 'SELECT * FROM users WHERE email = ? AND set_password = ? LIMIT 1;';
    $users = $conn->query( $sql, array( $_POST['email'], $_POST['code'] ) );
    if ( $conn->num_rows()>0 ) {
        $sql = "UPDATE users SET password = md5( ? ), set_password = NULL,last_verify = NOW(), verify_code = NULL WHERE email = ?";
        $conn->exec($sql, array($_POST['password'], $_POST['email']));
        $_SESSION['msg'] = 'You have successfully set your password.';
        header('Location: index.php');
    }else{
        $_SESSION['msg'] = 'You have entered invalid code please contact admin.';
        header('Location: index.php');
    }


}*/
include_once 'header-login.php';
     ?>
        <div class="avo_comm_login">
            <div class="container">
                <img src="images/login_pg_logo.png" alt="" class="login_avo_comm_img"/>
                <h2>Forget Password</h2>
                <?php if(isset($_SESSION['msg'])){?> <p><?php  echo $_SESSION['msg']; unset($_SESSION['msg']);?></p>  <?php }?>
                <?php if(isset($_SESSION['err'])){?> <p><?php  echo $_SESSION['err']; unset($_SESSION['err']);?></p>  <?php }?>
                <form action="forget_password.php" method="POST">
                    <input type="email" required name="email" placeholder="Email" value="" />
                    <input type="submit" name="submit_form" value="Submit" />
                </form>
                <img src="images/login_footer_img.png" alt="" class="login_ftr_img" />
            </div>
        </div>


    <?php
        include_once 'footer-login.php';
?>