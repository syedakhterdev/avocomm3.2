<?php

session_start();
$session_id = session_id();
require( 'manager/includes/pdo.php' );

$msg = '';
$err = '';

if(isset($_POST['email']) && isset($_POST['password'])){

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


}


if(isset($_GET['code']) && isset($_GET['email'])){
// destroy the entire session
    session_unset();
    session_destroy();
    
    $sql = 'SELECT * FROM users WHERE email = ? AND set_password = ? LIMIT 1;';
    $users = $conn->query( $sql, array( $_GET['email'], $_GET['code'] ) );
    if ( $conn->num_rows()==0 ) {
        $_SESSION['err']    =   'You have clicked on an invalid link, please try again.';
        header( 'Location: index.php' );
        exit;
    }else{
      include_once 'header-login.php';
     ?>
        <div class="avo_comm_login">
            <div class="container">
                <img src="images/login_pg_logo.png" alt="" class="login_avo_comm_img"/>
                <h2>Set Your Password</h2>
                <form action="set_password.php" method="POST">
                    <input type="text" name="email" readonly placeholder="LOGIN" value="<?php echo $_GET['email']?>" />
                    <input type="password" id="password" name="password" placeholder="PASSWORD" autocomplete="new-password" />
                    <input type="password" id="password_confirm" oninput="check(this)" name="password" placeholder="Confirm Password" autocomplete="new-password" />
                    <input type="hidden" name="code" value="<?php echo $_GET['code']?>">
                    <script language='javascript' type='text/javascript'>
                        function check(input) {
                            if (input.value != document.getElementById('password').value) {
                                input.setCustomValidity('Password Must be Matching.');
                            } else {
                                // input is valid -- reset the error message
                                input.setCustomValidity('');
                            }
                        }
                    </script>
                    <input type="submit" name="submit_form" value="Submit" />
                </form>
                <img src="images/login_footer_img.png" alt="" class="login_ftr_img" />
            </div>
        </div>


    <?php
        include_once 'footer-login.php';
    }


}
?>