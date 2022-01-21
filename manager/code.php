<?php
$msg = '';

if ( isset( $_POST['submission'] ) ) {
	if ( isset( $_POST['code'] ) ) {
		require( 'includes/pdo.php' );

		// check to see if th euser is in the db
		$sql = "SELECT id FROM Admins WHERE reset_code = '" . $_POST['code'] . "' AND active = 1;";
		$result = $conn->query( $sql );

		if ( $conn->num_rows() == 0 ) {
			$err = "The code you entered could not be verified, please try again.";
		} else if ( $conn->num_rows() > 0 ) {
			// close the database connection
			$conn->close();
			header( 'Location: reset.php?code=' . $_POST['code'] );
		}
	} else {
		$err = 'Please enter a valid code to continue.';
	}
}
?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Code Verification</title>
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="font-awesome/css/font-awesome.css" rel="stylesheet">
  <link href="css/imagine.css" rel="stylesheet">
	<style>
	.form-group {
		padding-top: 2px;
	}
	</style>
</head>

<body>

    <div class="container">
        <div class="row">
            <div class="col-md-5 col-md-offset-3">

                <div class="login-panel panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Code Verification</h3>
                    </div>

										<?php if ( $err ) echo "<div class=\"alert alert-danger\">$err</div>"; ?>

                    <div class="panel-body">
												<?php if ( !$err ) { ?>
												<p>Please enter the verification code you received below.</p>
												<?php } ?>

                        <form role="form" action="" method="POST">
                        <input type="hidden" name="submission" id="submission" value="1">
                            <fieldset>
                                <div class="form-group">
                                	<label for="code">Code</label>
                                  <input class="form-control" placeholder="Verification Code" name="code" type="text" required autofocus>
                                </div>
                                <button class="btn btn-lg btn-success btn-block">Verify</button>
                                <!-- Change this to a button or input when using this as a form -->
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Core Scripts - Include with every page -->
    <script src="js/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/imagine.js"></script>
</body>

</html>