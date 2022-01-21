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

   /* if ( (int)$user['difference'] > 60 || (int)$user['last_verify'] == '' ) {
      require_once('manager/includes/EmailManager.php');
      $mail = new EmailManager(
        $conn,
        '',
        1, // use template # 1
        $user['email'],
        stripslashes( $user['first_name'] . ' ' . $user['last_name'] ),
        '',
        '',
        true,
        [
            'NAME' => stripslashes( $user['first_name'] . ' ' . $user['last_name'] ),
            'HOST' => $_SERVER['HTTP_HOST'],
            'VERIFY_LINK' => '<a href="https://' . $_SERVER['HTTP_HOST'] . '/index.php?code=' . $code . '&email=' . $user['email'] . '">Verify my email</a>'
        ]
      );
      $mail->send();
      $sql = 'UPDATE users SET verify_code = ? WHERE id = ? LIMIT 1;';
      $conn->exec( $sql, array( $code, $user['id'] ) );
      $msg = 'Please check your email and click the link inside to verify your email address.';
    } */
   /* else {*/
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
          header( 'Location: terms.php' );
      }

      else{

          header( 'Location: main.php' );
      }


   /* }*/
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
      header( 'Location: terms.php' );
    else
      header( 'Location: main.php' );
  } else {
    $err = 'You have clicked on an invalid link, please log in with your email and password combination or reset your password.';
  }
}
include_once 'header-login.php';
?>

<div class="avo_comm_login">
    <div class="container">
        <img src="images/login_pg_logo.png" alt="" class="login_avo_comm_img"/>
        <h2>SIGN IN TO AVO COMMUNICATOR</h2>
        <?php if ( $err ) echo "<p>$err</p>"; ?>
        <?php if ( $msg ) echo "<p>$msg</p>"; ?>
        <?php if(isset($_SESSION['msg'])){?> <p><?php  echo $_SESSION['msg']; unset($_SESSION['msg']);?></p>  <?php }?>
        <?php if(isset($_SESSION['err'])){?> <p><?php  echo $_SESSION['err']; unset($_SESSION['err']);?></p>  <?php }?>
        <form action="index.php" method="POST">
            <input type="text" name="email" placeholder="LOGIN" />
            <input type="password" name="password" placeholder="PASSWORD" />
            <h3>Forgot Your Password? <a href="/forget_password.php">Click Here</a></h3>
            <h3><a href="assets/terms-conditions/Avo_Comm_Legal_Final_7-18-19.pdf" target="_blank">Terms & Conditions</a></h3>
            <input type="submit" name="submit" value="Submit" />
        </form>
        <img src="images/login_footer_img.png" alt="" class="login_ftr_img" />
    </div>
</div>

<?php
include_once 'footer-login.php';

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