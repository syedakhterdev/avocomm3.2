<?php
session_start();
require( '../includes/pdo.php' );
require( '../includes/check_login.php' );
require( '../includes/FSProgramUpdateManager.php' );
$FSProgramUpdate = new FSProgramUpdateManager($conn);
$msg = '';

// check variables passed in through querystring
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$update = isset($_POST['update']) ? (int) $_POST['update'] : 0;
$sid = (int) $_GET['sid'];

// if an id is passed in then let's fetch it from the database
if ($id) {
    if (!( $row = $FSProgramUpdate->getByID($sid, $_SESSION['admin_period_id'], $id) )) {
        $msg = "Sorry, a record with the specified ID could not be found!";
    }
} else if ($update) { // if the user submitted a form....
    $token = $_SESSION['upd_token'];
    unset($_SESSION['upd_token']);

    if (!$msg && $token != '' && $token == $_POST['upd_token']) {
        if (!$FSProgramUpdate->update($_POST['update'], $sid, $_SESSION['admin_period_id'], $_POST['description'], $_POST['updates'])) {
            $msg = "Sorry, an error has occurred, please contact your administrator.<br>Error: " . $FSProgramUpdate->error() . ";";
        } else {
            if ($sid)
                header("Location: ../fs_program_entries/edit.php?upd=1&id=$sid");
            else
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
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300' rel='stylesheet' type='text/css'>
        <link rel="icon" href="/assets/cropped-favicon-150x150.png" sizes="32x32">
        <script>$(document).ready(function () {
                $('form:first *:input[type!=hidden]:first').focus();
            });</script>
        <script type="text/javascript" src="../includes/tinymce/tinymce.min.js"></script>

        <script type="text/javascript">tinymce.init({
                selector: "textarea#description",
                plugins: ["link image hr fullscreen media table textcolor code paste lists advlist"],
                toolbar: "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link code responsivefilemanager table | forecolor backcolor",
                external_filemanager_path: "/manager/includes/tinymce/plugins/filemanager/",
                filemanager_title: "File manager", relative_urls: false, image_advtab: true,
                external_plugins: {"filemanager": "/manager/includes/tinymce/plugins/filemanager/plugin.min.js"},
                paste_as_text: true
            });
        </script>

        <script type="text/javascript">tinymce.init({
                selector: "textarea#updates",
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
                            <h3>Edit a <?php echo ENTITY; ?></h3>
                        </div>
                    </div>

                    <ol class="breadcrumb bc-3">
                        <li><a href="/manager/menu.php">Dashboard</a></li>
                        <li>&nbsp;/&nbsp;</li>
                        <li><strong>EDIT A FOODSERVICE PROGRAM UPDATE</strong></li>
                    </ol>

                    <div class="row my-4 mgr_body program_edit program_updates_edit foodservice">
                        <div class="col-lg-10 col-md-8">

                            <form action="edit.php?sid=<?php echo $sid; ?>" role="form" method="POST" onSubmit="return validateForm();">
                                <input type="hidden" name="update" value="<?php echo $row['id']; ?>">
                                <input type="hidden" name="upd_token" value="<?php echo $_SESSION['upd_token']; ?>">

                                <?php if ($msg) echo "<div class=\"alert alert-danger\">$msg</div>"; ?>

                                <table style="width: 100%;">
                                    <tr>
                                        <td>
                                            <h3>FOR CURRENT PERIOD</h3>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>

                                            <div class="form-group row">
                                                <div class="col-sm-12">
                                                    <h4>Description</h4>
                                                    <textarea id="description" name="description" class="form-control" rows="3"><?php echo ( $msg ) ? $_POST['description'] : $row['description']; ?></textarea>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="form-group row">
                                                <div class="col-sm-12">
                                                    <h4>Updates</h4>
                                                    <textarea id="updates" name="updates" class="form-control" rows="3"><?php echo ( $msg ) ? $_POST['updates'] : $row['updates']; ?></textarea>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </table>

                                <!--<div class="form-group row">
                                    <label for="description" class="col-sm-4 col-form-label">Description <span class="required_sign">*</span></label>
                                    <div class="col-sm-8">
                                        <textarea id="description" name="description" class="form-control" rows="3"><?php echo ( $msg ) ? $_POST['description'] : $row['description']; ?></textarea>
                                    </div>
                                </div>-->

                                <!--<div class="form-group row">
                                    <label for="updates" class="col-sm-4 col-form-label">Updates <span class="required_sign">*</span></label>
                                    <div class="col-sm-8">
                                        <textarea id="updates" name="updates" class="form-control" rows="3"><?php echo ( $msg ) ? $_POST['updates'] : $row['updates']; ?></textarea>
                                    </div>
                                </div>-->


                                <div class="form-group row">
                                    <div class="col-sm-12">
                                        <button type="submit" class="btn action_btn float-right save">Update</button>
                                        <button type="button" id="cancel" name="cancel" class="btn action_btn cancel float-right" onClick="window.location.href = '<?php echo $sid ? '../fs_program_entries/edit.php?id=' . $sid : 'index.php'; ?>';">Cancel</button>
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

                                            }
                                            function hasHtml5Validation() {
                                                return typeof document.createElement('input').checkValidity === 'function';
                                            }
        </script>

    </body>

</html>
<?php $conn->close(); ?>
