<?php
$msg = '';
$err = '';

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
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link href="font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="css/imagine.css" rel="stylesheet">
		<link rel="icon" href="/assets/cropped-favicon-150x150.png" sizes="32x32">
	<style>
  :root {
    --input-padding-x: 1.5rem;
    --input-padding-y: .75rem;
  }

  body {
    background-color: #2C3E50;
  }

  .card-signin {
    border: 0;
    border-radius: 1rem;
    box-shadow: 0 0.5rem 1rem 0 rgba(0, 0, 0, 0.1);
  }

  .card-signin .card-title {
    margin-bottom: 2rem;
    font-weight: 300;
    font-size: 1.5rem;
  }

  .card-signin .card-body {
    padding: 2rem;
  }

  .form-signin {
    width: 100%;
  }

  .form-signin .btn {
    font-size: 80%;
    border-radius: 3px;
    letter-spacing: .1rem;
    font-weight: bold;
    padding: 1rem;
    transition: all 0.2s;
  }

  .form-label-group input {
    border-radius: 3px;
  }

  .form-label-group>input,
  .form-label-group>label {
    padding: var(--input-padding-y) var(--input-padding-x);
  }

  .form-label-group input:not(:placeholder-shown) {
    padding-top: calc(var(--input-padding-y) + var(--input-padding-y) * (2 / 3));
    padding-bottom: calc(var(--input-padding-y) / 3);
  }

  .form-label-group input:not(:placeholder-shown)~label {
    padding-top: calc(var(--input-padding-y) / 3);
    padding-bottom: calc(var(--input-padding-y) / 3);
    font-size: 12px;
    color: #777;
  }
	</style>
</head>

<body class="manager_login">

    <div class="login">
        <header>
            <img src="images/afm_logo.png" alt="" />
        </header>
        <div class="login_container">
            <img src="images/avo_comm_logo.png" alt="" />
            <h2>ADMIN SIGN IN</h2>
            <?php if ($err) echo '<div class="alert alert-danger">' . $err . '</div>'; ?>
            <form class="form-signin" method="POST">
                <div class="form-group">
                    <input type="email" id="email" name="email" class="form-control" placeholder="Email address" required autofocus>
                </div>

                <div class="form-group">
                    <input type="password" id="password" name="password" class="form-control" placeholder="Password" required>
                </div>

                <div class="custom-control custom-checkbox mb-3 remember_pwd">
                    <input type="checkbox" class="custom-control-input" id="customCheck1">
                    <label class="custom-control-label" for="customCheck1">Remember password</label>
                </div>
                <button class="btn btn-lg btn-primary btn-block text-uppercase" type="submit" name="submit" id="submit">Sign in</button>
            </form>
        </div>
        <footer>
            <img src="images/login_ftr_img.png" alt="" />
        </footer>
    </div>

    <?php /* ?>
    <div class="container">
      <div class="row">
        <div class="col-sm-9 col-md-7 col-lg-5 mx-auto">
          <div class="card card-signin my-5">
            <div class="card-body">
              <h5 class="card-title text-center">Sign In</h5>
              <?php if ( $err ) echo '<div class="alert alert-danger">' . $err . '</div>'; ?>
              <form class="form-signin" method="POST">
                <div class="form-group">
                  <label for="email">Email address</label>
                  <input type="email" id="email" name="email" class="form-control" placeholder="Email address" required autofocus>
                </div>

                <div class="form-group">
                  <label for="password">Password</label>
                  <input type="password" id="password" name="password" class="form-control" placeholder="Password" required>
                </div>

                <div class="custom-control custom-checkbox mb-3">
                  <input type="checkbox" class="custom-control-input" id="customCheck1">
                  <label class="custom-control-label" for="customCheck1">Remember password</label>
                </div>
                <button class="btn btn-lg btn-primary btn-block text-uppercase" type="submit" name="submit" id="submit">Sign in</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?php */ ?>

  <!-- Core Scripts - Include with every page -->
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
  <script src="/manager/js/imagine.js"></script>
</body>

</html>
