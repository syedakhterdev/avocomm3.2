<?php
session_start();
require( '../includes/pdo.php' );
require( '../includes/check_login.php' );
require( '../includes/VendorUpdateManager.php' );
$VendorUpdate = new VendorUpdateManager($conn);
$msg = '';

$insert = isset($_POST['insert']) ? (int) $_POST['insert'] : 0;
$vid = (int) $_GET['sid'];


if ($insert) {
    $token = $_SESSION['add_token'];
    unset($_SESSION['add_token']);

    if (!$msg) {
        if ($token != '' && $token == $_POST['add_token']) {
            if (!$id = $VendorUpdate->add($_POST['vendor_id'], $_POST['period_id'], $_POST['current_marketing_activities'], $_POST['upcoming_marketing_activities'], $_POST['current_shopper_marketing_activities'], $_POST['upcoming_shopper_marketing_activiites'])) {
                $msg = "Sorry, an error has occurred, please contact your administrator.<br>Error: " . $VendorUpdate->error() . ";";
            } else {
                if ($vid)
                    header("Location: ../trade_vendor_entries/edit.php?add=1&id=$vid");
                else
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
        <title>Add a <?php echo ENTITY; ?></title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
        <link href="/manager/font-awesome/css/font-awesome.min.css" rel="stylesheet">
        <link href="/manager/css/imagine.css" rel="stylesheet">
        <link rel="icon" href="/assets/cropped-favicon-150x150.png" sizes="32x32">
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300' rel='stylesheet' type='text/css'>
        <script>$(document).ready(function () {
                $('form:first *:input[type!=hidden]:first').focus();
            });</script>
        <script type="text/javascript" src="../includes/tinymce/tinymce.min.js"></script>

        <script type="text/javascript">tinymce.init({
                selector: "textarea#current_marketing_activities",
                plugins: ["link image hr fullscreen media table textcolor code paste lists advlist"],
                toolbar: "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link code responsivefilemanager table | forecolor backcolor",
                external_filemanager_path: "/manager/includes/tinymce/plugins/filemanager/",
                filemanager_title: "File manager", relative_urls: false, image_advtab: true,
                external_plugins: {"filemanager": "/manager/includes/tinymce/plugins/filemanager/plugin.min.js"},
                paste_as_text: true
            });
        </script>

        <script type="text/javascript">tinymce.init({
                selector: "textarea#upcoming_marketing_activities",
                plugins: ["link image hr fullscreen media table textcolor code paste lists advlist"],
                toolbar: "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link code responsivefilemanager table | forecolor backcolor",
                external_filemanager_path: "/manager/includes/tinymce/plugins/filemanager/",
                filemanager_title: "File manager", relative_urls: false, image_advtab: true,
                external_plugins: {"filemanager": "/manager/includes/tinymce/plugins/filemanager/plugin.min.js"},
                paste_as_text: true
            });
        </script>

        <script type="text/javascript">tinymce.init({
                selector: "textarea#current_shopper_marketing_activities",
                plugins: ["link image hr fullscreen media table textcolor code paste lists advlist"],
                toolbar: "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link code responsivefilemanager table | forecolor backcolor",
                external_filemanager_path: "/manager/includes/tinymce/plugins/filemanager/",
                filemanager_title: "File manager", relative_urls: false, image_advtab: true,
                external_plugins: {"filemanager": "/manager/includes/tinymce/plugins/filemanager/plugin.min.js"},
                paste_as_text: true
            });
        </script>

        <script type="text/javascript">tinymce.init({
                selector: "textarea#upcoming_shopper_marketing_activiites",
                plugins: ["link image hr fullscreen media table textcolor code paste lists advlist"],
                toolbar: "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link code responsivefilemanager table | forecolor backcolor",
                external_filemanager_path: "/manager/includes/tinymce/plugins/filemanager/",
                filemanager_title: "File manager", relative_urls: false, image_advtab: true,
                external_plugins: {"filemanager": "/manager/includes/tinymce/plugins/filemanager/plugin.min.js"},
                paste_as_text: true
            });
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
                            <h3>Add a <?php echo ENTITY; ?></h3>
                        </div>
                    </div>

                    <ol class="breadcrumb bc-3">
                        <li><a href="/manager/menu.php">Dashboard</a></li>
                        <li>&nbsp;/&nbsp;</li>
                        <li><strong>Add a <?php echo ENTITY; ?></strong></li>
                    </ol>

                    <div class="row my-4 mgr_body vendor vendot_add program_edit">
                        <div class="col-lg-10 col-md-8">

                            <form action="add.php?sid=<?php echo $vid; ?>" role="form" method="POST" onSubmit="return validateForm();">
                                <input type="hidden" name="insert" value="1">
                                <input type="hidden" name="add_token" value="<?php echo $_SESSION['add_token']; ?>">

                                <?php if ($msg) echo "<div class=\"alert alert-danger\">$msg</div>"; ?>



                                <input type="hidden" name="vendor_id" id="vendor_id" value="<?php echo $vid; ?>">

                                <input type="hidden" name="period_id" id="period_id" value="<?php echo $_SESSION['admin_period_id']; ?>">

                                <table style="width: 100%;" class="add_edit">
                                    <tr>
                                        <td>
                                            <h3>Current Marketing Activities</h3>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <textarea id="current_marketing_activities" name="current_marketing_activities" class="form-control" rows="8"><?php echo ( $msg ) ? $_POST['current_marketing_activities'] : ''; ?></textarea>
                                        </td>
                                    </tr>
                                </table>

                                <!--<div class="form-group row">
                                    <label for="current_marketing_activities" class="col-sm-4 col-form-label">Current Marketing Activities</label>
                                    <div class="col-sm-8">
                                        <textarea id="current_marketing_activities" name="current_marketing_activities" class="form-control" rows="8"><?php echo ( $msg ) ? $_POST['current_marketing_activities'] : ''; ?></textarea>
                                    </div>
                                </div>-->

                                <table style="width: 100%;" class="add_edit">
                                    <tr>
                                        <td>
                                            <h3>Upcoming Marketing Activities</h3>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <textarea id="upcoming_marketing_activities" name="upcoming_marketing_activities" class="form-control" rows="8"><?php echo ( $msg ) ? $_POST['upcoming_marketing_activities'] : ''; ?></textarea>
                                        </td>
                                    </tr>
                                </table>

                                <!--<div class="form-group row">
                                    <label for="upcoming_marketing_activities" class="col-sm-4 col-form-label">Upcoming Marketing Activities</label>
                                    <div class="col-sm-8">
                                        <textarea id="upcoming_marketing_activities" name="upcoming_marketing_activities" class="form-control" rows="8"><?php echo ( $msg ) ? $_POST['upcoming_marketing_activities'] : ''; ?></textarea>
                                    </div>
                                </div>-->

                                <table style="width: 100%;" class="add_edit">
                                    <tr>
                                        <td>
                                            <h3>Current Shopper Marketing Activities</h3>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <textarea id="current_shopper_marketing_activities" name="current_shopper_marketing_activities" class="form-control" rows="8"><?php echo ( $msg ) ? $_POST['current_shopper_marketing_activities'] : ''; ?></textarea>
                                        </td>
                                    </tr>
                                </table>

                                <!--<div class="form-group row">
                                    <label for="current_shopper_marketing_activities" class="col-sm-4 col-form-label">Current Shopper Marketing Activities</label>
                                    <div class="col-sm-8">
                                        <textarea id="current_shopper_marketing_activities" name="current_shopper_marketing_activities" class="form-control" rows="8"><?php echo ( $msg ) ? $_POST['current_shopper_marketing_activities'] : ''; ?></textarea>
                                    </div>
                                </div>-->

                                <table style="width: 100%;" class="add_edit">
                                    <tr>
                                        <td>
                                            <h3>Upcoming Shopper Marketing Activities</h3>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <textarea id="upcoming_shopper_marketing_activiites" name="upcoming_shopper_marketing_activiites" class="form-control" rows="8"><?php echo ( $msg ) ? $_POST['upcoming_shopper_marketing_activiites'] : ''; ?></textarea>
                                        </td>
                                    </tr>
                                </table>

                                <!--<div class="form-group row">
                                    <label for="upcoming_shopper_marketing_activiites" class="col-sm-4 col-form-label">Upcoming Shopper Marketing Activities</label>
                                    <div class="col-sm-8">
                                        <textarea id="upcoming_shopper_marketing_activiites" name="upcoming_shopper_marketing_activiites" class="form-control" rows="8"><?php echo ( $msg ) ? $_POST['upcoming_shopper_marketing_activiites'] : ''; ?></textarea>
                                    </div>
                                </div>-->

                                <div class="form-group row">
                                    <div class="col-sm-12">
                                        <button type="submit" class="btn action_btn save float-right">Create</button>
                                        <button type="button" id="cancel" name="cancel" class="btn action_btn cancel float-right" onClick="window.location.href = '../trade_vendor_entries/edit.php?id=<? echo $vid; ?>';">Cancel</button>
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

                                                return true;
                                            }

                                            function hasHtml5Validation() {
                                                return typeof document.createElement('input').checkValidity === 'function';
                                            }
        </script>

    </body>

</html>
<?php $conn->close(); ?>
