<?php
session_start();
require( '../includes/pdo.php' );
require( '../includes/check_login.php' );
require( '../includes/AdminManager.php' );
$Admin = new AdminManager($conn);
$msg = '';

if (!(int) $_SESSION['admin_sa'])
    header('Location: /manager/menu.php');

// check variables passed in through querystring
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$update = isset($_POST['update']) ? (int) $_POST['update'] : 0;

// if an id is passed in then let's fetch it from the database
if ($id) {
    if (!( $row = $Admin->getByID($id) )) {
        $msg = "Sorry, a record with the specified ID could not be found!";
    }

    $del_photo = ( isset($_GET['del_photo']) ) ? (int) $_GET['del_photo'] : 0;
    if ($del_photo) {
        $Admin->removePhoto($id);
        $row['photo'] = '';
    }
} else if ($update) { // if the user submitted a form....
    $token = $_SESSION['upd_token'];
    unset($_SESSION['upd_token']);

    if (!$msg && $token != '' && $token == $_POST['upd_token']) {
        if (!$Admin->update($_POST['update'], $_POST['username'], $_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['password'], $_POST['photo'], $_POST['permission_news'], $_POST['permission_events'], $_POST['permission_reports'], $_POST['permission_trade'], $_POST['permission_marketing_activities'], $_POST['permission_shopper_hub'], $_POST['permission_fs_hub'], $_POST['permission_periods'], $_POST['permission_users'], $_POST['sa'], $_POST['active'])) {
            $msg = "Sorry, an error has occurred, please contact your administrator.<br>Error: " . $Admin->error() . ";";
        } else {
            if ((int) $_POST['update'] == (int) $_SESSION['admin_id']) {
                $_SESSION['admin_permission_news'] = (int) $_POST['permission_news'];
                $_SESSION['admin_permission_events'] = (int) $_POST['permission_events'];
                $_SESSION['admin_permission_trade'] = (int) $_POST['permission_trade'];
                $_SESSION['admin_permission_reports'] = (int) $_POST['permission_reports'];
                $_SESSION['admin_permission_periods'] = (int) $_POST['permission_periods'];
                $_SESSION['admin_permission_shopper_hub'] = (int) $_POST['permission_shopper_hub'];
                $_SESSION['admin_permission_fs_hub'] = (int) $_POST['permission_fs_hub'];
                $_SESSION['admin_permission_marketing_activities'] = (int) $_POST['permission_marketing_activities'];
                $_SESSION['admin_permission_users'] = (int) $_POST['permission_users'];
            }
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
        <title>Edit an <?php echo ENTITY; ?></title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
        <link href="/manager/font-awesome/css/font-awesome.min.css" rel="stylesheet">
        <link href="/manager/css/imagine.css" rel="stylesheet">
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300' rel='stylesheet' type='text/css'>
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
                            <h3>Edit an <?php echo ENTITY; ?></h3>
                        </div>
                    </div>

                    <ol class="breadcrumb bc-3">
                        <li><a href="/manager/menu.php">Dashboard</a></li>
                        <li>&nbsp;/&nbsp;</li>
                        <li><a href="/manager/Admins/">Admins</a></li>
                        <li>&nbsp;/&nbsp;</li>
                        <li><strong>Edit an Admin</strong></li>
                    </ol>

                    <div class="row my-4 mgr_body  program_edit admin_edit">
                        <div class="col-lg-10 col-md-8">

                            <form action="edit.php" role="form" method="POST" onSubmit="return validateForm();">
                                <input type="hidden" name="update" value="<?php echo $row['id']; ?>">
                                <input type="hidden" name="upd_token" value="<?php echo $_SESSION['upd_token']; ?>">

                                <div class="form-group row">
                                    <div class="col-sm-12">
                                        <button type="button" id="cancel" name="cancel" class="btn btn-default back_btn float-right" onclick="window.location.href = 'index.php';">Back</button>
                                    </div>
                                </div>

                                <?php if ($msg) echo "<div class=\"alert alert-danger\">$msg</div>"; ?>

                                <table style="width: 100%;" class="add_edit">
                                    <tr>
                                        <td>
                                            <h3>Username *</h3>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="col-sm-12">
                                                <input type="text" class="form-control" id="username" name="username" onKeyUp="updateCountdown('#username', 25, '#username_lbl');" placeholder="" required onKeyDown="updateCountdown('#username', 25, '#username_lbl');" value="<?php echo ( $msg ) ? $_POST['username'] : $row['username']; ?>" maxlength="25">
                                                <span id="username_lbl" class="small"></span>
                                            </div>
                                        </td>
                                    </tr>
                                </table>

                                <!--<div class="form-group row">
                                    <label for="username" class="col-sm-4 col-form-label">Username <span class="required_sign">*</span></label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="username" name="username" onKeyUp="updateCountdown('#username', 25, '#username_lbl');" placeholder="" required onKeyDown="updateCountdown('#username', 25, '#username_lbl');" value="<?php echo ( $msg ) ? $_POST['username'] : $row['username']; ?>" maxlength="25">
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
                                                <input type="text" class="form-control" id="first_name" name="first_name" onKeyUp="updateCountdown('#first_name', 35, '#first_name_lbl');" placeholder=""  onKeyDown="updateCountdown('#first_name', 35, '#first_name_lbl');" value="<?php echo ( $msg ) ? $_POST['first_name'] : $row['first_name']; ?>" maxlength="35">
                                                <span id="first_name_lbl" class="small"></span>
                                            </div>
                                        </td>
                                    </tr>
                                </table>

                                <!--<div class="form-group row">
                                    <label for="first_name" class="col-sm-4 col-form-label">First Name</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="first_name" name="first_name" onKeyUp="updateCountdown('#first_name', 35, '#first_name_lbl');" placeholder=""  onKeyDown="updateCountdown('#first_name', 35, '#first_name_lbl');" value="<?php echo ( $msg ) ? $_POST['first_name'] : $row['first_name']; ?>" maxlength="35">
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
                                                <input type="text" class="form-control" id="last_name" name="last_name" onKeyUp="updateCountdown('#last_name', 35, '#last_name_lbl');" placeholder=""  onKeyDown="updateCountdown('#last_name', 35, '#last_name_lbl');" value="<?php echo ( $msg ) ? $_POST['last_name'] : $row['last_name']; ?>" maxlength="35">
                                                <span id="last_name_lbl" class="small"></span>
                                            </div>
                                        </td>
                                    </tr>
                                </table>

                                <!--<div class="form-group row">
                                    <label for="last_name" class="col-sm-4 col-form-label">Last Name</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="last_name" name="last_name" onKeyUp="updateCountdown('#last_name', 35, '#last_name_lbl');" placeholder=""  onKeyDown="updateCountdown('#last_name', 35, '#last_name_lbl');" value="<?php echo ( $msg ) ? $_POST['last_name'] : $row['last_name']; ?>" maxlength="35">
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
                                                <input type="password" class="form-control" id="password" name="password" onKeyUp="updateCountdown('#password', 40, '#password_lbl');" placeholder=""  onKeyDown="updateCountdown('#password', 40, '#password_lbl');" maxlength="40" autocomplete="off">
                                                <span id="password_lbl" class="small"></span>
                                            </div>
                                        </td>
                                    </tr>
                                </table>

                                <!--<div class="form-group row">
                                    <label for="password" class="col-sm-4 col-form-label">Password</label>
                                    <div class="col-sm-8">
                                        <input type="password" class="form-control" id="password" name="password" onKeyUp="updateCountdown('#password', 40, '#password_lbl');" placeholder=""  onKeyDown="updateCountdown('#password', 40, '#password_lbl');" maxlength="40">
                                        <span id="password_lbl" class="small"></span>
                                    </div>
                                </div>-->

                                <?php if ($row['photo'] != '') { ?>
                                    <table style="width: 100%;" class="add_edit">
                                        <tr>
                                            <td>
                                                <h3>Photo *</h3>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="col-sm-12">
                                                    <img src="/manager/timThumb.php?src=/assets/admins/<?php echo $row['photo']; ?>&w=200&h=80" style="border: 1px solid #CCCCCC;padding: 2px;margin: 4px;">
                                                    <br>
                                                    <a href="edit.php?id=<?php echo $id; ?>&del_photo=1" class="btn action_btn cancel">Remove Photo</a>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                <?php } else { ?>
                                    <table style="width: 100%;" class="add_edit">
                                        <tr>
                                            <td>
                                                <h3>Photo *</h3>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="col-sm-12">
                                                    <input type="text" class="form-control" id="photo" name="photo" placeholder="Click to upload" onfocus="this.blur();" onclick="window.open('../includes/tinymce/plugins/filemanager/dialog.php?type=1&fldr=admins&field_id=photo&popup=1', '<?php echo time(); ?>', 'width=900,height=550,toolbar=0,menubar=0,location=0,status=1,scrollbars=1,resizable=1,left=0,top=0');return false;" >
                                                </div>
                                                <script>
                                                    function responsive_filemanager_callback(field_id) {
                                                        var url = jQuery('#' + field_id).val();
                                                        url = url.replace("https://<?php echo $_SERVER['HTTP_HOST']; ?>/assets/admins/", '');
                                                        jQuery('#' + field_id).val(url);
                                                    }
                                                </script>
                                            </td>
                                        </tr>
                                    </table>
                                <?php } ?>

                                <?php /* if ($row['photo'] != '') { ?>
                                  <div class="form-group row">
                                  <label for="photo" class="col-sm-4 col-form-label">Photo <span class="required_sign">*</span></label>
                                  <div class="col-sm-8">
                                  <img src="/manager/timThumb.php?src=/assets/admins/<?php echo $row['photo']; ?>&w=200&h=80" style="border: 1px solid #CCCCCC;padding: 2px;margin: 4px;">
                                  <br>
                                  <a href="edit.php?id=<?php echo $id; ?>&del_photo=1">Remove Photo</a>
                                  </div>
                                  </div>
                                  <?php } else { ?>
                                  <div class="form-group row">
                                  <label for="photo" class="col-sm-4 col-form-label">Photo <span class="required_sign">*</span></label>
                                  <div class="col-sm-8">
                                  <input type="text" class="form-control" id="photo" name="photo" placeholder="Click to upload" onfocus="this.blur();" onclick="window.open('../includes/tinymce/plugins/filemanager/dialog.php?type=1&fldr=admins&field_id=photo&popup=1', '<?php echo time(); ?>', 'width=900,height=550,toolbar=0,menubar=0,location=0,status=1,scrollbars=1,resizable=1,left=0,top=0');return false;" >
                                  </div>
                                  </div>
                                  <script>
                                  function responsive_filemanager_callback(field_id) {
                                  var url = jQuery('#' + field_id).val();
                                  url = url.replace("https://<?php echo $_SERVER['HTTP_HOST']; ?>/assets/admins/", '');
                                  jQuery('#' + field_id).val(url);
                                  }
                                  </script>
                                  <?php } */ ?>

                                <table style="width: 100%;" class="add_edit">
                                    <tr>
                                        <td>
                                            <h3>Create/Modify</h3>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="col-sm-4 float-right">
                                                <div class="form-check">
                                                    <input name="permission_news" id="permission_news" type="checkbox" value="1" <?php if (isset($_POST['permission_news']) || (int) $row['permission_news']) echo "CHECKED"; ?> class="form-check-input"><span>News</span>
                                                </div>
                                            </div>
                                            <div class="col-sm-4 float-right">
                                                <div class="form-check">
                                                    <input name="permission_events" id="permission_events" type="checkbox" value="1" <?php if (isset($_POST['permission_events']) || (int) $row['permission_events']) echo "CHECKED"; ?> class="form-check-input"><span>Events</span>
                                                </div>
                                            </div>
                                            <div class="col-sm-4 float-right">
                                                <div class="form-check">
                                                    <input name="permission_reports" id="permission_reports" type="checkbox" value="1" <?php if (isset($_POST['permission_reports']) || (int) $row['permission_reports']) echo "CHECKED"; ?> class="form-check-input"><span>Reports</span>
                                                </div>
                                            </div>
                                            <div class="col-sm-4 float-right">
                                                <div class="form-check">
                                                    <input name="permission_trade" id="permission_trade" type="checkbox" value="1" <?php if (isset($_POST['permission_trade']) || (int) $row['permission_trade']) echo "CHECKED"; ?> class="form-check-input"><span>Trade</span>
                                                </div>
                                            </div>
                                            <div class="col-sm-4 float-right">
                                                <div class="form-check">
                                                    <input name="permission_marketing_activities" id="permission_marketing_activities" type="checkbox" value="1" <?php if (isset($_POST['permission_marketing_activities']) || (int) $row['permission_marketing_activities']) echo "CHECKED"; ?> class="form-check-input"><span>Marketing Activities</span>
                                                </div>
                                            </div>
                                            <div class="col-sm-4 float-right">
                                                <div class="form-check">
                                                    <input name="permission_shopper_hub" id="permission_shopper_hub" type="checkbox" value="1" <?php if (isset($_POST['permission_shopper_hub']) || (int) $row['permission_shopper_hub']) echo "CHECKED"; ?> class="form-check-input"><span>Shopper Hub</span>
                                                </div>
                                            </div>
                                            <div class="col-sm-4 float-right">
                                                <div class="form-check">
                                                    <input name="permission_fs_hub" id="permission_fs_hub" type="checkbox" value="1" <?php if (isset($_POST['permission_fs_hub']) || (int) $row['permission_fs_hub']) echo "CHECKED"; ?> class="form-check-input"><span>Foodservice Hub</span>
                                                </div>
                                            </div>
                                            <div class="col-sm-4 float-right">
                                                <div class="form-check">
                                                    <input name="permission_periods" id="permission_periods" type="checkbox" value="1" <?php if (isset($_POST['permission_periods']) || (int) $row['permission_periods']) echo "CHECKED"; ?> class="form-check-input"><span>Periods</span>
                                                </div>
                                            </div>
                                            <div class="col-sm-4 float-right">
                                                <div class="form-check">
                                                    <input name="permission_users" id="permission_users" type="checkbox" value="1" <?php if (isset($_POST['permission_users']) || (int) $row['permission_users']) echo "CHECKED"; ?> class="form-check-input"><span>Users</span>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </table>

                                <!--<div class="form-group row">
                                    <div class="col-sm-4"><label for="permission_news">Create/Modify News</label></div>
                                    <div class="col-sm-8">
                                        <div class="form-check">
                                            <input name="permission_news" id="permission_news" type="checkbox" value="1" <?php if (isset($_POST['permission_news']) || (int) $row['permission_news']) echo "CHECKED"; ?> class="form-check-input">
                                        </div>
                                    </div>
                                </div>-->

                                <!--<div class="form-group row">
                                    <div class="col-sm-4"><label for="permission_events">Create/Modify Events</label></div>
                                    <div class="col-sm-8">
                                        <div class="form-check">
                                            <input name="permission_events" id="permission_events" type="checkbox" value="1" <?php if (isset($_POST['permission_events']) || (int) $row['permission_events']) echo "CHECKED"; ?> class="form-check-input">
                                        </div>
                                    </div>
                                </div>-->

                                <!--<div class="form-group row">
                                    <div class="col-sm-4"><label for="permission_reports">Create/Modify Reports</label></div>
                                    <div class="col-sm-8">
                                        <div class="form-check">
                                            <input name="permission_reports" id="permission_reports" type="checkbox" value="1" <?php if (isset($_POST['permission_reports']) || (int) $row['permission_reports']) echo "CHECKED"; ?> class="form-check-input">
                                        </div>
                                    </div>
                                </div>-->

                                <!--<div class="form-group row">
                                    <div class="col-sm-4"><label for="permission_trade">Create/Modify Trade</label></div>
                                    <div class="col-sm-8">
                                        <div class="form-check">
                                            <input name="permission_trade" id="permission_trade" type="checkbox" value="1" <?php if (isset($_POST['permission_trade']) || (int) $row['permission_trade']) echo "CHECKED"; ?> class="form-check-input">
                                        </div>
                                    </div>
                                </div>-->

                                <!--<div class="form-group row">
                                    <div class="col-sm-4"><label for="permission_marketing_activities">Create/Modify Marketing Activities</label></div>
                                    <div class="col-sm-8">
                                        <div class="form-check">
                                            <input name="permission_marketing_activities" id="permission_marketing_activities" type="checkbox" value="1" <?php if (isset($_POST['permission_marketing_activities']) || (int) $row['permission_marketing_activities']) echo "CHECKED"; ?> class="form-check-input">
                                        </div>
                                    </div>
                                </div>-->

                                <!--<div class="form-group row">
                                    <div class="col-sm-4"><label for="permission_shopper_hub">Create/Modify Shopper Hub</label></div>
                                    <div class="col-sm-8">
                                        <div class="form-check">
                                            <input name="permission_shopper_hub" id="permission_shopper_hub" type="checkbox" value="1" <?php if (isset($_POST['permission_shopper_hub']) || (int) $row['permission_shopper_hub']) echo "CHECKED"; ?> class="form-check-input">
                                        </div>
                                    </div>
                                </div>-->

                                <!--<div class="form-group row">
                                    <div class="col-sm-4"><label for="permission_fs_hub">Create/Modify Foodservice Hub</label></div>
                                    <div class="col-sm-8">
                                        <div class="form-check">
                                            <input name="permission_fs_hub" id="permission_fs_hub" type="checkbox" value="1" <?php if (isset($_POST['permission_fs_hub']) || (int) $row['permission_fs_hub']) echo "CHECKED"; ?> class="form-check-input">
                                        </div>
                                    </div>
                                </div>-->

                                <!--<div class="form-group row">
                                    <div class="col-sm-4"><label for="permission_periods">Create/Modify Periods</label></div>
                                    <div class="col-sm-8">
                                        <div class="form-check">
                                            <input name="permission_periods" id="permission_periods" type="checkbox" value="1" <?php if (isset($_POST['permission_periods']) || (int) $row['permission_periods']) echo "CHECKED"; ?> class="form-check-input">
                                        </div>
                                    </div>
                                </div>-->

                                <!--<div class="form-group row">
                                    <div class="col-sm-4"><label for="permission_users">Create/Modify Users</label></div>
                                    <div class="col-sm-8">
                                        <div class="form-check">
                                            <input name="permission_users" id="permission_users" type="checkbox" value="1" <?php if (isset($_POST['permission_users']) || (int) $row['permission_users']) echo "CHECKED"; ?> class="form-check-input">
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
                                                    <input name="sa" id="sa" type="checkbox" value="1" <?php if (isset($_POST['sa']) || (int) $row['sa']) echo "CHECKED"; ?> class="form-check-input"><span>SA</span>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </table>

                                <!--<div class="form-group row">
                                    <div class="col-sm-4"><label for="sa">Sa</label></div>
                                    <div class="col-sm-8">
                                        <div class="form-check">
                                            <input name="sa" id="sa" type="checkbox" value="1" <?php if (isset($_POST['sa']) || (int) $row['sa']) echo "CHECKED"; ?> class="form-check-input">
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
                                                if ($('#username').val() == '')
                                                    return createError('username', 'Please enter a valid username');
                                                if ($('#email').val() == '')
                                                    return createError('email', 'Please enter a valid email');
                                            }
                                            function hasHtml5Validation() {
                                                return typeof document.createElement('input').checkValidity === 'function';
                                            }
        </script>

    </body>

</html>
<?php $conn->close(); ?>