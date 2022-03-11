<?php
session_start();
$session_id = session_id();
require( 'config.php' );
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
        include_once 'header-login-new.php';
     ?>
        <section class="forget-pass-banner banner">
            <div class="container">
                <div class="banner-inner">
                    <h2 style="padding-bottom: 40px;">Set Your <span>PASSWORD</span></h2>
                    <p class="error"></p>
                    <form action="<?php echo SITE_URL?>/set_password.php" method="POST" onsubmit="return validate_pass();">
                        <input type="hidden" name="code" value="<?php echo $_GET['code']?>">

                        <div class="form-group">
                            <input type="email" readonly required name="email" value="<?php echo $_GET['email']?>" placeholder="Enter Your Email">
                        </div>
                        <div class="form-group">
                            <input type="password" required id="password" name="password" value="" placeholder="Enter Your Password">
                        </div>

                        <div class="form-group">
                            <input type="password" id="password_confirm" required name="password_confirm" value="" placeholder="Confirm Password">
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

        <?php
        include_once 'footer-login-new.php';
    }


}
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script language='javascript' type='text/javascript'>
    function validate_pass() {
        $('.error').text('');
       if($('#password').val()!=$('#password_confirm').val()){
           $('.error').text('Please match both password');
           return false;
       }else{
           return true;
       }
    }
</script>
