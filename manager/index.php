<?php
$msg = '';
$err = '';
require( 'config.php' );
if ( isset( $_POST['submit'] ) ) {
	require( 'includes/pdo.php' );

	// check to see if th euser is in the db
	$sql = "SELECT * FROM Admins WHERE email = ? AND password = MD5( ? ) AND active = 1;";
	$stmt = $conn->query( $sql, array( $_POST['email'], $_POST['password'] ) );

	if ( $stmt ) {
		if ( $stmt->rowCount() == 0 ) {
			$msg = "The username and password you provided are not valid.";
		} else if ( $stmt->rowCount() > 0 ) {
			$row = $stmt->fetch();
			// store the session if logged in successfully
			session_start();
			$session_id = session_id();

			$id = $row['id'];

			// set session variables
			$_SESSION['admin_id'] = $id;
			$_SESSION['admin_name'] = $row['first_name'] . " " . $row['last_name']; // store session data
			$_SESSION['admin_sa'] = $row['sa']; // store session data
			$_SESSION['admin_photo'] = $row['photo']; // store session data
			$_SESSION['admin_permission_news'] = $row['permission_news'];
			$_SESSION['admin_permission_events'] = $row['permission_events'];
			$_SESSION['admin_permission_trade'] = $row['permission_trade'];
			$_SESSION['admin_permission_reports'] = $row['permission_reports'];
			$_SESSION['admin_permission_periods'] = $row['permission_periods'];
			$_SESSION['admin_permission_users'] = $row['permission_users'];
			$_SESSION['admin_permission_shopper_hub'] = $row['permission_shopper_hub'];
			$_SESSION['admin_permission_fs_hub'] = $row['permission_fs_hub'];
			$_SESSION['admin_permission_marketing_activities'] = $row['permission_marketing_activities'];

			//$sql = 'SELECT id FROM periods WHERE active = 1 ORDER BY year DESC, month DESC LIMIT 1';
			$sql = 'SELECT id FROM periods WHERE active = 1 AND month = ? AND year = ? ORDER BY year DESC, month DESC LIMIT 1';
			$periods = $conn->query( $sql, array( date( 'n' ), date( 'Y' ) )  );
			if ( $conn->num_rows() > 0 ) {
				$period = $conn->fetch( $periods );
				$_SESSION['admin_period_id'] = $period['id'];
			}

			// set the last activity for the user, this is used to expire the session after X amount of time, see check_login.php
			$sql = "UPDATE Admins SET session_id = ?, last_activity = ? WHERE id = ?";
			$conn->exec( $sql, array( $session_id, time(), $id ) );

			// close the database connection
			$conn = null;
			// user is logged in, let's send them to the main screen
			header( "Location: menu.php" );
		}
	} else {
		$msg = 'A database error has occurred, please contact your administrator.';
	}
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="<?php echo ADMIN_URL?>/images/cropped-favicon-150x150.png" sizes="32x32">
    <title>Avocado</title>
    <link rel="stylesheet" href="<?php echo ADMIN_URL?>/css/admin_style_new.css">
    <link rel="stylesheet" href="<?php echo ADMIN_URL?>/css/responsive_new.css">

</head>

<body>
<!-- header sec start -->
<header class="login-page-header">
    <div class="container">
        <div class="header-inner login-header">
            <a class="logo" href="<?php echo ADMIN_URL?>"><img src="<?php echo ADMIN_URL?>/images/logo.png" alt="logo"></a>
            <img class="line3" src="<?php echo ADMIN_URL?>/images/line2.png" alt="line2">
            <a class="avo" href="<?php echo ADMIN_URL?>"><img src="<?php echo ADMIN_URL?>/images/avo.png" alt="avo"></a>
            <a class="avo-mobile" href="<?php echo ADMIN_URL?>"><img src="<?php echo ADMIN_URL?>/images/avo-mobile.png" alt="avo"></a>
        </div>
        <img class="line1" src="<?php echo ADMIN_URL?>/images/line1.png" alt="line1">
        <img class="line2" src="<?php echo ADMIN_URL?>/images/line1.png" alt="line1">
    </div>
</header>
<!-- header sec end -->

<!-- banner sec start -->
<section class="login-banner admin-login-banner banner login-page-banner">
    <div class="container">
        <div class="banner-inner">
            <h2>ADMIN LOGIN</h2>
            <p>Sign in to your ADMIN account</p>
            <?php if ($err) echo '<div class="alert alert-danger">' . $err . '</div>'; ?>
            <form method="POST" action="<?php echo ADMIN_URL?>/index.php">
                <div class="form-group">
                    <input type="email" id="email" name="email" required placeholder="Enter Your Email">
                </div>
                <div class="form-group">
                    <input type="password" id="password" name="password" placeholder="Enter Your Password">
                </div>
                <div class="form-group2 remember_pwd" >
                    <input type="checkbox" class="custom-control-input" id="html">
                    <label for="html"><a href="javascript:void(0)">Remember password</a></label>
                </div>
                <button type="submit" name="submit" id="submit">
                    <img
                            src="<?php echo ADMIN_URL?>/images/login-page-submit.svg"
                            onmouseover="this.src='<?php echo ADMIN_URL?>/images/login-page-submit-hvr.svg'"
                            onmouseout="this.src='<?php echo ADMIN_URL?>/images/login-page-submit.svg'"
                            alt="login-submit-btn"
                    />
                </button>
            </form>
        </div>
    </div>
</section>
<!-- banner sec end -->
</body>

</html>
