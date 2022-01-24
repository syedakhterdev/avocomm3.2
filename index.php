<?php
session_start();
$session_id = session_id();
require( 'manager/includes/pdo.php' );

$msg = '';
$err = '';

if ( isset( $_POST['submit'] ) ) {

  $sql = 'SELECT *, DATEDIFF( CURDATE(), last_verify ) AS difference FROM users WHERE email = ? AND password = MD5( ? ) AND active = 1 LIMIT 1;';

  $users = $conn->query( $sql, array( $_POST['email'], $_POST['password'] ) );
  $code = getRandomString(8);

  if ( $conn->num_rows() > 0 ) {

    $user = $conn->fetch( $users );

      $id = $user['id'];
      $_SESSION['user_id'] = $id;
      $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
      $_SESSION['user_type'] = 'Normal';

      // LOG THEM IN
      // get the latest period
      $sql = 'SELECT id, title FROM periods WHERE publish = 1 ORDER BY year DESC, month DESC LIMIT 1';
      $periods = $conn->query( $sql, array() );
      if ( $conn->num_rows() > 0 ) {
        $period = $conn->fetch( $periods );
        $_SESSION['user_period_id'] = $period['id'];
        $_SESSION['user_period_title'] = stripslashes( $period['title'] );
      }
      $sql = "UPDATE users SET session_id = ?, last_activity = ?, login_count = login_count + 1, verify_code = NULL, last_login = NOW() WHERE id = ?";
      $conn->exec( $sql, array( $session_id, time(), $user['id'] ) );

      $sql = 'INSERT INTO activity_log SET date_created = NOW(), user_id = ?, activity_type_id = 1, ip_address = ?';
      $conn->exec( $sql, array( $id, $_SERVER['REMOTE_ADDR'] ) );

      if ( $user['last_login'] == '' || !(int)$user['agree_to_terms'] ){
          header( 'Location: '.SITE_URL.'/terms.php' );
      }

      else{

          header( 'Location: '.SITE_URL.'/main.php' );
          exit;
      }

  } else {
    $err = 'Sorry, the email and password combination you entered could not be found,<br>please check your information and try again.';
  }
} else if ( isset( $_GET['email'] ) && !empty( $_GET['email'] ) && isset( $_GET['code'] ) && !empty( $_GET['code'] ) ) {
  $email = $_GET['email'];
  $code = $_GET['code'];
  $sql = 'SELECT id, first_name, last_name FROM users WHERE email = ? AND verify_code = ?';
  $users = $conn->query( $sql, array( $email, $code ) );

  if ( $conn->num_rows() > 0 ) {
    $user = $conn->fetch( $users );
    $id = $user['id'];
    $_SESSION['user_id'] = $id;
    $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];

    // get the latest period
    $sql = 'SELECT id, title FROM periods WHERE active = 1 AND month = ? AND year = ? ORDER BY year DESC, month DESC LIMIT 1';
    $periods = $conn->query( $sql, array( date( 'n' ), date( 'Y' ) ) );
    if ( $conn->num_rows() > 0 ) {
      $period = $conn->fetch( $periods );
      $_SESSION['user_period_id'] = $period['id'];
      $_SESSION['user_period_title'] = stripslashes( $period['title'] );
    }
    $sql = "UPDATE users SET session_id = ?, last_activity = ?, login_count = login_count + 1, last_verify = NOW(), verify_code = NULL, last_login = NOW() WHERE id = ?";
    $conn->exec( $sql, array( $session_id, time(), $user['id'] ) );

    $sql = 'INSERT INTO activity_log SET date_created = NOW(), user_id = ?, activity_type_id = 1, ip_address = ?';
    $conn->exec( $sql, array( $id, $_SERVER['REMOTE_ADDR'] ) );

    if ( $user['last_login'] == '' || !(int)$user['agree_to_terms'] )
      header( 'Location: '.SITE_URL.'/terms.php' );
    else
      header( 'Location: '.SITE_URL.'/main.php' );
  } else {
    $err = 'You have clicked on an invalid link, please log in with your email and password combination or reset your password.';
  }
}
include_once 'header-login-new.php';
?>

    <section class="login-banner banner">
        <div class="container">
            <div class="banner-inner">
                <h2>LOGIN</h2>
                <p>Sign in to Avo Communicator</p>
                <?php if ( $err ) echo "<p>$err</p>"; ?>
                <?php if ( $msg ) echo "<p>$msg</p>"; ?>
                <?php if(isset($_SESSION['msg'])){?> <p><?php  echo $_SESSION['msg']; unset($_SESSION['msg']);?></p>  <?php }?>
                <?php if(isset($_SESSION['err'])){?> <p><?php  echo $_SESSION['err']; unset($_SESSION['err']);?></p>  <?php }?>
                <form action="<?php echo SITE_URL?>/index.php" method="POST">
                    <div class="form-group">
                        <input type="email" name="email" placeholder="Enter Your Email">
                    </div>
                    <div class="form-group">
                        <input type="password" name="password" placeholder="Enter Your Password">
                    </div>
                    <a href="<?php echo SITE_URL?>/forget_password.php" class="forget-pass">
                        <span>Forgot your password? <b>Click here.</b></span>
                    </a>
                    <div class="form-group2">
                        <input type="checkbox" id="html">
                        <label for="html"><a href="<?php echo SITE_URL?>/assets/terms-conditions/Avo_Comm_Legal_Final_7-18-19.pdf">I agree with terms and conditions</a></label>
                    </div>
                    <button name="submit" type="submit">
                        <img
                                src="<?php echo SITE_URL?>/images/login-submit-btn.png"
                                onmouseover="this.src='<?php echo SITE_URL?>/images/login-submit-hvr-btn.png'"
                                onmouseout="this.src='<?php echo SITE_URL?>/images/login-submit-btn.png'"
                                alt="login-submit-btn"
                        />
                    </button>
                </form>
                <img class="hand-mobile" src="<?php echo SITE_URL?>/images/hand-mobile.png" alt="">
                <img class="hand-mobile2" src="<?php echo SITE_URL?>/images/hand-mobile2.png" alt="">
            </div>
            <img class="hand" src="<?php echo SITE_URL?>/images/hand.png" alt="">
        </div>
    </section>

<?php
include_once 'footer-login-new.php';

function getRandomString( $length = 8 ) {
    $characters = '0123456789';
    $charactersLength = strlen( $characters );
    $randomString = '';
    for ( $i = 0; $i < $length; $i++ ) {
        $randomString .= $characters[rand( 0, $charactersLength - 1 )];
    }
    return $randomString;
}

?>