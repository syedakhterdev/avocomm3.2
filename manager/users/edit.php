<?php
session_start();
require( '../includes/pdo.php' );
require( '../includes/check_login.php' );
require( '../includes/UserManager.php' );
$User = new UserManager($conn);
$msg = '';

// check variables passed in through querystring
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$update = isset($_POST['update']) ? (int) $_POST['update'] : 0;

// if an id is passed in then let's fetch it from the database
if ($id) {
    if (!( $row = $User->getByID($id) )) {
        $msg = "Sorry, a record with the specified ID could not be found!";
    }
} else if ($update) { // if the user submitted a form....
    $token = $_SESSION['upd_token'];
    unset($_SESSION['upd_token']);

    if (!$msg && $token != '' && $token == $_POST['upd_token']) {
        if (!$User->update($_POST['update'], $_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['password'], $_POST['company'], $_POST['active'])) {
            $msg = "Sorry, an error has occurred, please contact your administrator.<br>Error: " . $User->error() . ";";
        } else {
            header("Location: index.php");
        }
    }
}

// create a token for secure deletion (from this page only and not remote)
$_SESSION['upd_token'] = md5(uniqid());
session_write_close();
?>
<!DOCTYPE html>
<html>

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Edit a <?php echo ENTITY; ?></title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
        <link href="/manager/font-awesome/css/font-awesome.min.css" rel="stylesheet">
        <link href="/manager/css/imagine.css" rel="stylesheet">
        <link rel="icon" href="/assets/cropped-favicon-150x150.png" sizes="32x32">
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300' rel='stylesheet' type='text/css'>
        <script>$(document).ready(function () {
                $('form:first *:input[type!=hidden]:first').focus();
            });</script>
        <script type="text/javascript">
            $().ready(function () {
                updateCountdown('#first_name', 40, '#first_name_lbl');
                updateCountdown('#last_name', 40, '#last_name_lbl');
                updateCountdown('#email', 120, '#email_lbl');
                updateCountdown('#password', 40, '#password_lbl');
                updateCountdown('#company', 80, '#company_lbl');
            });

            function updateCountdown(input, limit, lbl) {
                var remaining = limit - $(input).val().length;
                $(lbl).text(remaining + ' characters remaining.');
            }
        </script>
    </head>

    <body>
        <?php include( '../includes/header.php' ); ?>

        <div class="container-fluid" id="main">
            <div class="row row-offcanvas row-offcanvas-left">

                <?php include( '../includes/nav.php' ); ?>

                <div class="col main pt-5 mt-3">
                    <div class="row mgr_heading">
                        <div class="col-lg-10">
                            <h3>Edit an <?php echo ENTITY; ?></h3>
                        </div>
                    </div>

                    <ol class="breadcrumb bc-3">
                        <li><a href="/manager/menu.php">Dashboard</a></li>
                        <li>&nbsp;/&nbsp;</li>
                        <li><a href="/manager/users/">Users</a></li>
                        <li>&nbsp;/&nbsp;</li>
                        <li><strong>Edit an User</strong></li>
                    </ol>

                    <div class="row my-4 mgr_body users user_edit program_edit">
                        <div class="col-lg-10 col-md-8">

                            <form action="edit.php" role="form" method="POST" onSubmit="return validateForm();">
                                <input type="hidden" name="update" value="<?php echo $row['id']; ?>">
                                <input type="hidden" name="upd_token" value="<?php echo $_SESSION['upd_token']; ?>">

                                <?php if ($msg) echo "<div class=\"alert alert-danger\">$msg</div>"; ?>

                                <div class="form-group row">
                                    <div class="col-sm-12">
                                        <button type="button" id="cancel" name="cancel" class="btn btn-default back_btn float-right" onclick="window.location.href = 'index.php';">Back</button>
                                    </div>
                                </div>

                                <table style="width: 100%;" class="add_edit">
                                    <tr>
                                        <td>
                                            <h3>First Name *</h3>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="col-sm-12">
                                                <input type="text" class="form-control" id="first_name" name="first_name" onKeyUp="updateCountdown('#first_name', 40, '#first_name_lbl');" placeholder="" required onKeyDown="updateCountdown('#first_name', 40, '#first_name_lbl');" value="<?php echo ( $msg ) ? $_POST['first_name'] : $row['first_name']; ?>" maxlength="40">
                                                <span id="first_name_lbl" class="small"></span>
                                            </div>
                                        </td>
                                    </tr>
                                </table>

                                <!--<div class="form-group row">
                                    <label for="first_name" class="col-sm-4 col-form-label">First Name <span class="required_sign">*</span></label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="first_name" name="first_name" onKeyUp="updateCountdown('#first_name', 40, '#first_name_lbl');" placeholder="" required onKeyDown="updateCountdown('#first_name', 40, '#first_name_lbl');" value="<?php echo ( $msg ) ? $_POST['first_name'] : $row['first_name']; ?>" maxlength="40">
                                        <span id="first_name_lbl" class="small"></span>
                                    </div>
                                </div>-->

                                <table style="width: 100%;" class="add_edit">
                                    <tr>
                                        <td>
                                            <h3>Last Name *</h3>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="col-sm-12">
                                                <input type="text" class="form-control" id="last_name" name="last_name" onKeyUp="updateCountdown('#last_name', 40, '#last_name_lbl');" placeholder="" required onKeyDown="updateCountdown('#last_name', 40, '#last_name_lbl');" value="<?php echo ( $msg ) ? $_POST['last_name'] : $row['last_name']; ?>" maxlength="40">
                                                <span id="last_name_lbl" class="small"></span>
                                            </div>
                                        </td>
                                    </tr>
                                </table>

                                <!--<div class="form-group row">
                                    <label for="last_name" class="col-sm-4 col-form-label">Last Name <span class="required_sign">*</span></label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="last_name" name="last_name" onKeyUp="updateCountdown('#last_name', 40, '#last_name_lbl');" placeholder="" required onKeyDown="updateCountdown('#last_name', 40, '#last_name_lbl');" value="<?php echo ( $msg ) ? $_POST['last_name'] : $row['last_name']; ?>" maxlength="40">
                                        <span id="last_name_lbl" class="small"></span>
                                    </div>
                                </div>-->

                                <table style="width: 100%;" class="add_edit">
                                    <tr>
                                        <td>
                                            <h3>Email *</h3>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="col-sm-12">
                                                <input type="email" class="form-control" id="email" name="email" onKeyUp="updateCountdown('#email', 120, '#email_lbl');" placeholder="" required onKeyDown="updateCountdown('#email', 120, '#email_lbl');" value="<?php echo ( $msg ) ? $_POST['email'] : $row['email']; ?>" maxlength="120">
                                                <span id="email_lbl" class="small"></span>
                                            </div>
                                        </td>
                                    </tr>
                                </table>

                                <!--<div class="form-group row">
                                    <label for="email" class="col-sm-4 col-form-label">Email <span class="required_sign">*</span></label>
                                    <div class="col-sm-8">
                                        <input type="email" class="form-control" id="email" name="email" onKeyUp="updateCountdown('#email', 120, '#email_lbl');" placeholder="" required onKeyDown="updateCountdown('#email', 120, '#email_lbl');" value="<?php echo ( $msg ) ? $_POST['email'] : $row['email']; ?>" maxlength="120">
                                        <span id="email_lbl" class="small"></span>
                                    </div>
                                </div>-->

                                <table style="width: 100%;" class="add_edit">
                                    <tr>
                                        <td>
                                            <h3>Password</h3>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="col-sm-12">
                                                <input type="password" class="form-control" id="password" name="password" onKeyUp="updateCountdown('#password', 40, '#password_lbl');" placeholder=""  onKeyDown="updateCountdown('#password', 40, '#password_lbl');" value="<?php echo ( $msg ) ? $_POST['password'] : ''; ?>" maxlength="40" autocomplete="new-password">
                                                <span id="password_lbl" class="small"></span>
                                            </div>
                                        </td>
                                    </tr>
                                </table>

                                <!--<div class="form-group row">
                                    <label for="password" class="col-sm-4 col-form-label">Password </label>
                                    <div class="col-sm-8">
                                        <input type="password" class="form-control" id="password" name="password" onKeyUp="updateCountdown('#password', 40, '#password_lbl');" placeholder=""  onKeyDown="updateCountdown('#password', 40, '#password_lbl');" value="<?php echo ( $msg ) ? $_POST['password'] : ''; ?>" maxlength="40">
                                        <span id="password_lbl" class="small"></span>
                                    </div>
                                </div>-->

                                <table style="width: 100%;" class="add_edit">
                                    <tr>
                                        <td>
                                            <h3>Company *</h3>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="col-sm-12">
                                                <input type="text" class="form-control" id="company" name="company" onKeyUp="updateCountdown('#company', 80, '#company_lbl');" placeholder="" required onKeyDown="updateCountdown('#company', 80, '#company_lbl');" value="<?php echo ( $msg ) ? $_POST['company'] : $row['company']; ?>" maxlength="80">
                                                <span id="company_lbl" class="small"></span>
                                            </div>
                                        </td>
                                    </tr>
                                </table>

                                <!--<div class="form-group row">
                                    <label for="company" class="col-sm-4 col-form-label">Company <span class="required_sign">*</span></label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="company" name="company" onKeyUp="updateCountdown('#company', 80, '#company_lbl');" placeholder="" required onKeyDown="updateCountdown('#company', 80, '#company_lbl');" value="<?php echo ( $msg ) ? $_POST['company'] : $row['company']; ?>" maxlength="80">
                                        <span id="company_lbl" class="small"></span>
                                    </div>
                                </div>-->

                                <table style="width: 100%;" class="add_edit">
                                    <tr>
                                        <td>
                                            <h3>Status</h3>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="col-sm-12">
                                                <div class="form-check">
                                                    <input name="active" id="active" type="checkbox" value="1" <?php if (isset($_POST['active']) || (int) $row['active']) echo "CHECKED"; ?> class="form-check-input"><span>Active</span>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </table>

                                <!--<div class="form-group row">
                                    <div class="col-sm-4"><label for="active">Active</label></div>
                                    <div class="col-sm-8">
                                        <div class="form-check">
                                            <input name="active" id="active" type="checkbox" value="1" <?php if (isset($_POST['active']) || (int) $row['active']) echo "CHECKED"; ?> class="form-check-input">
                                        </div>
                                    </div>
                                </div>-->
                                <div class="form-group row">
                                    <div class="col-sm-12">
                                      <small>Last login: <?php echo date( 'm/d/Y h:i a', strtotime( $row['last_login'] ) ); ?> &nbsp;&nbsp;&nbsp; Last verification: <?php echo date( 'm/d/Y h:i a', strtotime( $row['last_verify'] ) ); ?></small>
                                    </div>
                                </div>


                                <div class="form-group row">
                                    <div class="col-sm-12">
                                        <button type="submit" class="btn action_btn save float-right">Update</button>
                                        <button type="button" id="cancel" name="cancel" class="btn action_btn cancel float-right" onClick="window.location.href = 'index.php';">Cancel</button>
                                    </div>
                                </div>

                            </form>
                        </div>



                    </div>
                    <!--/row-->

                    <footer class="container-fluid">
                        <p class="text-right small">©2019 All rights reserved.</p>
                    </footer>

                </div>
                <!--/main col-->

            </div>

        </div>
        <!--/.container-->
        <!-- Core Scripts - Include with every page -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
        <script src="/manager/js/imagine.js"></script>
        <script>
                                            $(document).ready(function () {

                                                $('#submit').click(function () {
                                                    if (!hasHtml5Validation())
                                                        return validateForm();
                                                });
                                            });
                                            function validateForm() {

                                                if ($('#first_name').val() == '')
                                                    return createError('first_name', 'Please enter a valid first name');
                                                if ($('#last_name').val() == '')
                                                    return createError('last_name', 'Please enter a valid last name');
                                                if ($('#email').val() == '')
                                                    return createError('email', 'Please enter a valid email');
                                                if ($('#company').val() == '')
                                                    return createError('company', 'Please enter a valid company');
                                            }
                                            function hasHtml5Validation() {
                                                return typeof document.createElement('input').checkValidity === 'function';
                                            }
        </script>

    </body>

</html>
<?php $conn->close(); ?>