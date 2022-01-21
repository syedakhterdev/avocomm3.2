<?php
$msg = '';
$code = $_GET['code'];

if ( isset( $_POST['submission'] ) ) {
	if ( isset( $_POST['password'] ) && isset( $_POST['password2'] ) ) {
		if ( $_POST['password'] == $_POST['password2'] ) {
			require( 'includes/pdo.php' );
			$code = $conn->prepString( $_POST['code'], 6 );

			// check to see if th euser is in the db
			$sql = "UPDATE Admins SET password = MD5( ? ), reset_code = '' WHERE reset_code = ? AND active = 1;";
			if ( $conn->exec( $sql, array( $_POST['password'], $code ) ) ) {
				$msg = "Your password has been reset successfully! Click here to <a href=\"index.php\">log in</a>.";
			} else {
				$err = 'An error has occurred and your password coult not be reset. Please contact your administrator.';
			}
			// close the database connection
			$conn->close();
		} else {
			$err = 'Your passwords do not match.';
		}
	} else {
		$err = 'Please both passwords to continue.';
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

<body>

  <div class="container">
    <div class="row">
      <div class="col-sm-9 col-md-7 col-lg-5 mx-auto">
        <div class="card card-signin my-5">
          <div class="card-body">
            <h5 class="card-title text-center">Forgot My Password</h5>
            <?php if ( $err ) echo '<div class="alert alert-danger">' . $err . '</div>'; ?>
						<?php if ( $msg ) echo "<div class=\"alert alert-danger\">$msg</div>"; ?>

						<?php if ( !isset( $_POST['submit'] ) ) { ?>
            <form class="form-signin" method="POST">
							<div class="form-group">
								<label for="password">Password</label>
								<input class="form-control" placeholder="Password" name="password" type="password" required autofocus>
							</div>

							<div class="form-group">
								<label for="password">Repeat Password</label>
								<input class="form-control" placeholder="Repeat Password" name="password2" type="password" required>
							</div>

              <button class="btn btn-lg btn-primary btn-block text-uppercase" type="submit" name="submit" id="submit">Send Reset</button>
            </form>
						<?php } ?>

          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Core Scripts - Include with every page -->
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
  <script src="/manager/js/imagine.js"></script>
</body>

</html>