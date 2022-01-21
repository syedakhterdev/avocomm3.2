<?php
session_start();
require( '../includes/pdo.php' );
require( '../includes/check_login.php' );
require( '../includes/AdminManager.php' );
$Admin = new AdminManager($conn);
$msg = '';

if (!(int) $_SESSION['admin_sa'])
    header('Location: /manager/menu.php');
$insert = isset($_POST['insert']) ? (int) $_POST['insert'] : 0;

if ($insert) {
    $token = $_SESSION['add_token'];
    unset($_SESSION['add_token']);

    if (!$msg) {
        if ($token != '' && $token == $_POST['add_token']) {
            if (!$id = $Admin->add($_POST['username'], $_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['password'], $_POST['photo'], $_POST['permission_news'], $_POST['permission_events'], $_POST['permission_reports'], $_POST['permission_trade'], $_POST['permission_marketing_activities'], $_POST['permission_shopper_hub'], $_POST['permission_fs_hub'], $_POST['permission_periods'], $_POST['permission_users'], $_POST['sa'], $_POST['active'])) {
                $msg = "Sorry, an error has occurred, please contact your administrator.<br>Error: " . $Admin->error() . ";";
            } else {
                header("Location: index.php");
            }
        } else {
            $msg = 'Sorry, an error has occurred, please go back and try again.';
        }
    }
}

// create a token for secure deletion (from this page only and not remote)
$_SESSION['add_token'] = md5(uniqid());
session_write_close();
?>
<!DOCTYPE html>
<html>

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Add an <?php echo ENTITY; ?></title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
        <link href="/manager/font-awesome/css/font-awesome.min.css" rel="stylesheet">
        <link href="/manager/css/imagine.css" rel="stylesheet">
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300' rel='stylesheet' type='text/css'>
        <link rel="icon" href="/assets/cropped-favicon-150x150.png" sizes="32x32">
        <script>$(document).ready(function () {
                $('form:first *:input[type!=hidden]:first').focus();
            });</script>
        <script type="text/javascript" src="../includes/tinymce/tinymce.min.js"></script>
        <script type="text/javascript">
            $().ready(function () {
                updateCountdown('#username', 25, '#username_lbl');
                updateCountdown('#first_name', 35, '#first_name_lbl');
                updateCountdown('#last_name', 35, '#last_name_lbl');
                updateCountdown('#email', 120, '#email_lbl');
                updateCountdown('#password', 40, '#password_lbl');
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
                            <h3>Add an <?php echo ENTITY; ?></h3>
                        </div>
                    </div>

                    <ol class="breadcrumb bc-3">
                        <li><a href="/manager/menu.php">Dashboard</a></li>
                        <li>&nbsp;/&nbsp;</li>
                        <li><a href="/manager/Admins/">Admins</a></li>
                        <li>&nbsp;/&nbsp;</li>
                        <li><strong>Add an Admin</strong></li>
                    </ol>

                    <div class="row my-4 mgr_body program_edit admin_add">
                        <div class="col-lg-10 col-md-8">

                            <form action="add.php" role="form" method="POST" onSubmit="return validateForm();">
                                <input type="hidden" name="insert" value="1">
                                <input type="hidden" name="add_token" value="<?php echo $_SESSION['add_token']; ?>">

                                <?php if ($msg) echo "<div class=\"alert alert-danger\">$msg</div>"; ?>

                                <div class="form-group row">
                                    <div class="col-sm-12">
                                        <button type="button" id="cancel" name="cancel" class="btn btn-default back_btn float-right" onclick="window.location.href = 'index.php';">Back</button>
                                    </div>
                                </div>

                                <table style="width: 100%;" class="add_edit">
                                    <tr>
                                        <td>
                                            <h3>Username *</h3>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="col-sm-12">
                                                <input type="text" class="form-control" id="username" name="username" onKeyUp="updateCountdown('#username', 25, '#username_lbl');" placeholder="" required onKeyDown="updateCountdown('#username', 25, '#username_lbl');" value="<?php echo ( $msg ) ? $_POST['username'] : ''; ?>" maxlength="25" autocomplete="off">
                                                <span id="username_lbl" class="small"></span>
                                            </div>
                                        </td>
                                    </tr>
                                </table>

                                <!--<div class="form-group row">
                                    <label for="username" class="col-sm-4 col-form-label">Username <span class="required_sign">*</span></label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="username" name="username" onKeyUp="updateCountdown('#username', 25, '#username_lbl');" placeholder="" required onKeyDown="updateCountdown('#username', 25, '#username_lbl');" value="<?php echo ( $msg ) ? $_POST['username'] : ''; ?>" maxlength="25">
                                        <span id="username_lbl" class="small"></span>
                                    </div>
                                </div>-->

                                <table style="width: 100%;" class="add_edit">
                                    <tr>
                                        <td>
                                            <h3>First Name</h3>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="col-sm-12">
                                                <input type="text" class="form-control" id="first_name" name="first_name" onKeyUp="updateCountdown('#first_name', 35, '#first_name_lbl');" placeholder=""  onKeyDown="updateCountdown('#first_name', 35, '#first_name_lbl');" value="<?php echo ( $msg ) ? $_POST['first_name'] : ''; ?>" maxlength="35">
                                                <span id="first_name_lbl" class="small"></span>
                                            </div>
                                        </td>
                                    </tr>
                                </table>

                                <!--<div class="form-group row">
                                    <label for="first_name" class="col-sm-4 col-form-label">First Name</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="first_name" name="first_name" onKeyUp="updateCountdown('#first_name', 35, '#first_name_lbl');" placeholder=""  onKeyDown="updateCountdown('#first_name', 35, '#first_name_lbl');" value="<?php echo ( $msg ) ? $_POST['first_name'] : ''; ?>" maxlength="35">
                                        <span id="first_name_lbl" class="small"></span>
                                    </div>
                                </div>-->

                                <table style="width: 100%;" class="add_edit">
                                    <tr>
                                        <td>
                                            <h3>Last Name</h3>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="col-sm-12">
                                                <input type="text" class="form-control" id="last_name" name="last_name" onKeyUp="updateCountdown('#last_name', 35, '#last_name_lbl');" placeholder=""  onKeyDown="updateCountdown('#last_name', 35, '#last_name_lbl');" value="<?php echo ( $msg ) ? $_POST['last_name'] : ''; ?>" maxlength="35">
                                                <span id="last_name_lbl" class="small"></span>
                                            </div>
                                        </td>
                                    </tr>
                                </table>

                                <!--<div class="form-group row">
                                    <label for="last_name" class="col-sm-4 col-form-label">Last Name</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="last_name" name="last_name" onKeyUp="updateCountdown('#last_name', 35, '#last_name_lbl');" placeholder=""  onKeyDown="updateCountdown('#last_name', 35, '#last_name_lbl');" value="<?php echo ( $msg ) ? $_POST['last_name'] : ''; ?>" maxlength="35">
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
                                                <input type="email" class="form-control" id="email" name="email" onKeyUp="updateCountdown('#email', 120, '#email_lbl');" placeholder="" required onKeyDown="updateCountdown('#email', 120, '#email_lbl');" value="<?php echo ( $msg ) ? $_POST['email'] : ''; ?>" maxlength="120">
                                                <span id="email_lbl" class="small"></span>
                                            </div>
                                        </td>
                                    </tr>
                                </table>

                                <!--<div class="form-group row">
                                    <label for="email" class="col-sm-4 col-form-label">Email <span class="required_sign">*</span></label>
                                    <div class="col-sm-8">
                                        <input type="email" class="form-control" id="email" name="email" onKeyUp="updateCountdown('#email', 120, '#email_lbl');" placeholder="" required onKeyDown="updateCountdown('#email', 120, '#email_lbl');" value="<?php echo ( $msg ) ? $_POST['email'] : ''; ?>" maxlength="120">
                                        <span id="email_lbl" class="small"></span>
                                    </div>
                                </div>-->

                                <table style="width: 100%;" class="add_edit">
                                    <tr>
                                        <td>
                                            <h3>Password *</h3>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="col-sm-12">
                                                <input type="password" class="form-control" id="password" name="password" onKeyUp="updateCountdown('#password', 40, '#password_lbl');" placeholder="" required onKeyDown="updateCountdown('#password', 40, '#password_lbl');" maxlength="40" autocomplete="off">
                                                <span id="password_lbl" class="small"></span>
                                            </div>
                                        </td>
                                    </tr>
                                </table>

                                <!--<div class="form-group row">
                                    <label for="password" class="col-sm-4 col-form-label">Password <span class="required_sign">*</span></label>
                                    <div class="col-sm-8">
                                        <input type="password" class="form-control" id="password" name="password" onKeyUp="updateCountdown('#password', 40, '#password_lbl');" placeholder="" required onKeyDown="updateCountdown('#password', 40, '#password_lbl');" maxlength="40">
                                        <span id="password_lbl" class="small"></span>
                                    </div>
                                </div>-->

                                <table style="width: 100%;" class="add_edit">
                                    <tr>
                                        <td>
                                            <h3>Photo</h3>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="col-sm-12">
                                                <input type="text" class="form-control" id="photo" name="photo" placeholder="Click to upload" onfocus="this.blur();" onclick="window.open('../includes/tinymce/plugins/filemanager/dialog.php?type=1&fldr=admins&field_id=photo&popup=1', '<?php echo time(); ?>', 'width=900,height=550,toolbar=0,menubar=0,location=0,status=1,scrollbars=1,resizable=1,left=0,top=0');return false;" >
                                            </div>
                                        </td>
                                    </tr>
                                </table>

                                <!--<div class="form-group row">
                                    <label for="photo" class="col-sm-4 col-form-label">Photo</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="photo" name="photo" placeholder="Click to upload" onfocus="this.blur();" onclick="window.open('../includes/tinymce/plugins/filemanager/dialog.php?type=1&fldr=admins&field_id=photo&popup=1', '<?php echo time(); ?>', 'width=900,height=550,toolbar=0,menubar=0,location=0,status=1,scrollbars=1,resizable=1,left=0,top=0');return false;" >
                                    </div>
                                </div>-->

                                <script>
                                    function responsive_filemanager_callback(field_id) {
                                        var url = jQuery('#' + field_id).val();
                                        url = url.replace("https://<?php echo $_SERVER['HTTP_HOST']; ?>/assets/admins/", '');
                                        jQuery('#' + field_id).val(url);
                                    }
                                </script>

                                <table style="width: 100%;" class="add_edit">
                                    <tr>
                                        <td>
                                            <h3>Create/Modify</h3>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="col-sm-4 float-left">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="permission_news" name="permission_news" value="1"  <?php if (isset($_POST['permission_news']) && (int) $_POST['permission_news']) echo "CHECKED"; ?>><span>News</span>
                                                </div>
                                            </div>
                                            <div class="col-sm-4 float-left">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="permission_events" name="permission_events" value="1"  <?php if (isset($_POST['permission_events']) && (int) $_POST['permission_events']) echo "CHECKED"; ?>><span>Events</span>
                                                </div>
                                            </div>
                                            <div class="col-sm-4 float-left">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="permission_reports" name="permission_reports" value="1"  <?php if (isset($_POST['permission_reports']) && (int) $_POST['permission_reports']) echo "CHECKED"; ?>><span>Reports</span>
                                                </div>
                                            </div>
                                            <div class="col-sm-4 float-left">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="permission_trade" name="permission_trade" value="1"  <?php if (isset($_POST['permission_trade']) && (int) $_POST['permission_trade']) echo "CHECKED"; ?>><span>Trade</span>
                                                </div>
                                            </div>
                                            <div class="col-sm-4 float-left">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="permission_marketing_activities" name="permission_marketing_activities" value="1"  <?php if (isset($_POST['permission_marketing_activities']) && (int) $_POST['permission_marketing_activities']) echo "CHECKED"; ?>><span>Marketing Activities</span>
                                                </div>
                                            </div>
                                            <div class="col-sm-4 float-left">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="permission_shopper_hub" name="permission_shopper_hub" value="1"  <?php if (isset($_POST['permission_shopper_hub']) && (int) $_POST['permission_shopper_hub']) echo "CHECKED"; ?>><span>Shopper Hub</span>
                                                </div>
                                            </div>
                                            <div class="col-sm-4 float-left">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="permission_fs_hub" name="permission_fs_hub" value="1"  <?php if (isset($_POST['permission_fs_hub']) && (int) $_POST['permission_fs_hub']) echo "CHECKED"; ?>><span>Foodservice Hub</span>
                                                </div>
                                            </div>
                                            <div class="col-sm-4 float-left">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="permission_periods" name="permission_periods" value="1"  <?php if (isset($_POST['permission_periods']) && (int) $_POST['permission_periods']) echo "CHECKED"; ?>><span>Periods</span>
                                                </div>
                                            </div>
                                            <div class="col-sm-4 float-left">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="permission_users" name="permission_users" value="1"  <?php if (isset($_POST['permission_users']) && (int) $_POST['permission_users']) echo "CHECKED"; ?>><span>Users</span>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </table>

                                <!--<div class="form-group row">
                                    <div class="col-sm-4"><label for="permission_news">Create/Modify News</label></div>
                                    <div class="col-sm-8">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="permission_news" name="permission_news" value="1"  <?php if (isset($_POST['permission_news']) && (int) $_POST['permission_news']) echo "CHECKED"; ?>>
                                        </div>
                                    </div>
                                </div>-->

                                <!--<div class="form-group row">
                                    <div class="col-sm-4"><label for="permission_events">Create/Modify Events</label></div>
                                    <div class="col-sm-8">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="permission_events" name="permission_events" value="1"  <?php if (isset($_POST['permission_events']) && (int) $_POST['permission_events']) echo "CHECKED"; ?>>
                                        </div>
                                    </div>
                                </div>-->

                                <!--<div class="form-group row">
                                    <div class="col-sm-4"><label for="permission_reports">Create/Modify Reports</label></div>
                                    <div class="col-sm-8">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="permission_reports" name="permission_reports" value="1"  <?php if (isset($_POST['permission_reports']) && (int) $_POST['permission_reports']) echo "CHECKED"; ?>>
                                        </div>
                                    </div>
                                </div>-->

                                <!--<div class="form-group row">
                                    <div class="col-sm-4"><label for="permission_trade">Create/Modify Trade</label></div>
                                    <div class="col-sm-8">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="permission_trade" name="permission_trade" value="1"  <?php if (isset($_POST['permission_trade']) && (int) $_POST['permission_trade']) echo "CHECKED"; ?>>
                                        </div>
                                    </div>
                                </div>-->

                                <!--<div class="form-group row">
                                    <div class="col-sm-4"><label for="permission_marketing_activities">Create/Modify Marketing Activities</label></div>
                                    <div class="col-sm-8">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="permission_marketing_activities" name="permission_marketing_activities" value="1"  <?php if (isset($_POST['permission_marketing_activities']) && (int) $_POST['permission_marketing_activities']) echo "CHECKED"; ?>>
                                        </div>
                                    </div>
                                </div>-->

                                <!--<div class="form-group row">
                                    <div class="col-sm-4"><label for="permission_shopper_hub">Create/Modify Shopper Hub</label></div>
                                    <div class="col-sm-8">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="permission_shopper_hub" name="permission_shopper_hub" value="1"  <?php if (isset($_POST['permission_shopper_hub']) && (int) $_POST['permission_shopper_hub']) echo "CHECKED"; ?>>
                                        </div>
                                    </div>
                                </div>-->

                                <!--<div class="form-group row">
                                    <div class="col-sm-4"><label for="permission_fs_hub">Create/Modify Foodservice Hub</label></div>
                                    <div class="col-sm-8">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="permission_fs_hub" name="permission_fs_hub" value="1"  <?php if (isset($_POST['permission_fs_hub']) && (int) $_POST['permission_fs_hub']) echo "CHECKED"; ?>>
                                        </div>
                                    </div>
                                </div>-->

                                <!--<div class="form-group row">
                                    <div class="col-sm-4"><label for="permission_periods">Create/Modify Periods</label></div>
                                    <div class="col-sm-8">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="permission_periods" name="permission_periods" value="1"  <?php if (isset($_POST['permission_periods']) && (int) $_POST['permission_periods']) echo "CHECKED"; ?>>
                                        </div>
                                    </div>
                                </div>-->

                                <!--<div class="form-group row">
                                    <div class="col-sm-4"><label for="permission_users">Create/Modify Users</label></div>
                                    <div class="col-sm-8">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="permission_users" name="permission_users" value="1"  <?php if (isset($_POST['permission_users']) && (int) $_POST['permission_users']) echo "CHECKED"; ?>>
                                        </div>
                                    </div>
                                </div>-->

                                <table style="width: 100%;" class="add_edit">
                                    <tr>
                                        <td>
                                            <h3>SA</h3>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="col-sm-12">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="sa" name="sa" value="1"  <?php if (isset($_POST['sa']) && (int) $_POST['sa']) echo "CHECKED"; ?>><span>SA</span>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </table>

                                <!--<div class="form-group row">
                                    <div class="col-sm-4"><label for="sa">SA</label></div>
                                    <div class="col-sm-8">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="sa" name="sa" value="1"  <?php if (isset($_POST['sa']) && (int) $_POST['sa']) echo "CHECKED"; ?>>
                                        </div>
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
                                                    <input class="form-check-input" type="checkbox" id="active" name="active" value="1"  <?php if (isset($_POST['active']) && (int) $_POST['active'] || true) echo "CHECKED"; ?>><span>Active</span>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </table>

                                <!--<div class="form-group row">
                                    <div class="col-sm-4"><label for="active">Active</label></div>
                                    <div class="col-sm-8">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="active" name="active" value="1"  <?php if (isset($_POST['active']) && (int) $_POST['active'] || true) echo "CHECKED"; ?>>
                                        </div>
                                    </div>
                                </div>-->


                                <div class="form-group row">
                                    <div class="col-sm-12">
                                        <button type="submit" class="btn action_btn save float-right">Create</button>
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
                                                if ($('#username').val() == '')
                                                    return createError('username', 'Please enter a valid username');
                                                if ($('#email').val() == '')
                                                    return createError('email', 'Please enter a valid email');
                                                if ($('#password').val() == '')
                                                    return createError('password', 'Please enter a valid password');
                                                return true;
                                            }

                                            function hasHtml5Validation() {
                                                return typeof document.createElement('input').checkValidity === 'function';
                                            }
        </script>

    </body>

</html>
<?php $conn->close(); ?>