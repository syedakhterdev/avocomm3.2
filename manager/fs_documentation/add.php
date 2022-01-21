<?php
session_start();
require( '../includes/pdo.php' );
require( '../includes/check_login.php' );
require( '../includes/FSDocumentationManager.php' );
$FSDocumentation = new FSDocumentationManager($conn);
$msg = '';

$insert = isset($_POST['insert']) ? (int) $_POST['insert'] : 0;
$sid = (int) $_GET['sid'];
if ( !(int)$_SESSION['admin_permission_fs_hub'] ) header( 'Location: ../menu.php' );

if ($insert) {
    $token = $_SESSION['add_token'];
    unset($_SESSION['add_token']);

    if (!$msg) {
        if ($token != '' && $token == $_POST['add_token']) {
            if (!$id = $FSDocumentation->add($sid, $_SESSION['admin_period_id'], $_POST['title'], $_POST['description'], $_POST['image'], $_POST['document'], $_POST['document_type_id'], $_POST['active'])) {
                $msg = "Sorry, an error has occurred, please contact your administrator.<br>Error: " . $FSDocumentation->error() . ";";
            } else {
                if ( (int)$_POST['document_type_id'] == 1 && isset( $_POST['document'] ) ) { // if the document type is an image create a thumbnail
                  $ext = pathinfo( $_POST['document'], PATHINFO_EXTENSION );
                  if ( $_POST['image'] == '' && in_array( $ext, array( 'jpg', 'jpeg', 'png', 'gif' ) ) ) {
                    $FSDocumentation->makeThumbnail( $id, $_SERVER['DOCUMENT_ROOT'] . "/assets/fs_documentation_docs/", $_POST['document'], $_SERVER['DOCUMENT_ROOT'] . "/assets/fs_documentation/" );
                    $sql = 'UPDATE fs_documentation SET image = ? WHERE id = ?';
                    $conn->exec( $sql, array( $_POST['document'], $id ) );
                  }
                }

                if ($sid)
                    header("Location: ../fs_program_entries/edit.php?add=1&id=$sid");
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
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <link rel="icon" href="/assets/cropped-favicon-150x150.png" sizes="32x32">
        <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300' rel='stylesheet' type='text/css'>
        <script>$(document).ready(function () {
                $('form:first *:input[type!=hidden]:first').focus();
            });</script>
        <script type="text/javascript" src="../includes/tinymce/tinymce.min.js"></script>
        <script type="text/javascript">
            $().ready(function () {
                updateCountdown('#title', 65, '#title_lbl');
                updateCountdown('#description', 255, '#description_lbl');
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
                            <h3>Add a <?php echo ENTITY; ?></h3>
                        </div>
                    </div>

                    <ol class="breadcrumb bc-3">
                        <li><a href="/manager/menu.php">Dashboard</a></li>
                        <li>&nbsp;/&nbsp;</li>
                        <li><strong>Add A Foodservice Documentation</strong></li>
                    </ol>

                    <div class="row my-4 mgr_body foodservice program_edit documentation_add">
                        <div class="col-lg-10 col-md-8">

                            <form action="add.php?sid=<?php echo $sid; ?>" role="form" method="POST" onSubmit="return validateForm();">
                                <input type="hidden" name="insert" value="1">
                                <input type="hidden" name="add_token" value="<?php echo $_SESSION['add_token']; ?>">
                                <input type="hidden" name="fs_program_id" value="<?php echo $sid; ?>">

                                <?php if ($msg) echo "<div class=\"alert alert-danger\">$msg</div>"; ?>



                                <input type="hidden" name="fs_program_id" id="fs_program_id" value="<?php echo $pid; ?>">

                                <table style="width: 100%;" class="add_edit">
                                    <tr>
                                        <td>
                                            <h3>Document Type *</h3>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="col-sm-12">
                                                <select name="document_type_id" id="document_type_id" class="form-control" required>
                                                    <option value="">Select an option...</option>
                                                    <?php echo $FSDocumentation->getDocument_types_Document_typeDropdown(( $msg ) ? $_POST['document_type_id'] : 0 ); ?>
                                                </select>
                                            </div>
                                        </td>
                                    </tr>
                                </table>

                                <table style="width: 100%;" class="add_edit">
                                    <tr>
                                        <td>
                                            <h3>Title *</h3>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="col-sm-12">
                                                <input type="text" class="form-control" id="title" name="title" onKeyUp="updateCountdown('#title', 65, '#title_lbl');" placeholder="" required onKeyDown="updateCountdown('#title', 65, '#title_lbl');" value="<?php echo ( $msg ) ? $_POST['title'] : ''; ?>" maxlength="65">
                                                <span id="title_lbl" class="small"></span>
                                            </div>
                                        </td>
                                    </tr>
                                </table>

                                <!--<div class="form-group row">
                                    <label for="title" class="col-sm-4 col-form-label">Title <span class="required_sign">*</span></label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="title" name="title" onKeyUp="updateCountdown('#title', 65, '#title_lbl');" placeholder="" required onKeyDown="updateCountdown('#title', 65, '#title_lbl');" value="<?php echo ( $msg ) ? $_POST['title'] : ''; ?>" maxlength="65">
                                        <span id="title_lbl" class="small"></span>
                                    </div>
                                </div>-->

                                <table style="width: 100%;" class="add_edit">
                                    <tr>
                                        <td>
                                            <h3>Description *</h3>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="col-sm-12">
                                                <textarea name="description" id="description" class="form-control" rows="3" onkeyup="updateCountdown('#description, 255, '#description_lbl' );" onkeyDown="updateCountdown('#description', 255, '#description_lbl');" placeholder="" required><?php echo htmlspecialchars(( $msg ) ? $_POST['description'] : '' ); ?></textarea>
                                                <span id="description_lbl" class="small"></span>
                                            </div>
                                        </td>
                                    </tr>
                                </table>

                                <!--<div class="form-group row">
                                    <label for="description" class="col-sm-4 col-form-label">Description <span class="required_sign">*</span></label>
                                    <div class="col-sm-8">
                                        <textarea name="description" id="description" class="form-control" rows="3" onkeyup="updateCountdown('#description, 255, '#description_lbl' );" onkeyDown="updateCountdown('#description', 255, '#description_lbl');" placeholder="" required><?php echo htmlspecialchars(( $msg ) ? $_POST['description'] : '' ); ?></textarea>
                                        <span id="description_lbl" class="small"></span>
                                    </div>
                                </div>-->

                                <table style="width: 100%;" class="add_edit">
                                    <tr>
                                        <td>
                                            <h3>Document</h3>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="col-sm-12">
                                                <input type="text" class="form-control" id="document" name="document" placeholder="Click to upload" onfocus="this.blur();" onclick="window.open('../includes/tinymce/plugins/filemanager/dialog.php?type=2&fldr=fs_documentation_docs&field_id=document&popup=1', '<?php echo time(); ?>', 'width=900,height=550,toolbar=0,menubar=0,location=0,status=1,scrollbars=1,resizable=1,left=0,top=0'); return false;" maxlength="65">
                                                <small>Please keep file names down to under 65 characters.</small>
                                            </div>
                                        </td>
                                    </tr>
                                </table>

                                <table style="width: 100%;" class="add_edit">
                                    <tr>
                                        <td>
                                            <h3>Preview Image</h3>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="col-sm-12">
                                                <input type="text" class="form-control" id="image" name="image" placeholder="Click to upload" onfocus="this.blur();" onclick="window.open('../includes/tinymce/plugins/filemanager/dialog.php?type=1&fldr=fs_documentation&field_id=image&popup=1', '<?php echo time(); ?>', 'width=900,height=550,toolbar=0,menubar=0,location=0,status=1,scrollbars=1,resizable=1,left=0,top=0');
                                                        return false;" maxlength="65">
                                                <small>Please keep file names under 65 characters.</small>
                                            </div>
                                        </td>
                                    </tr>
                                </table>

                                <!--<div class="form-group row">
                                    <label for="image" class="col-sm-4 col-form-label">Image <span class="required_sign">*</span></label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="image" name="image" placeholder="Click to upload" onfocus="this.blur();" onclick="window.open('../includes/tinymce/plugins/filemanager/dialog.php?type=1&fldr=fs_documentation&field_id=image&popup=1', '<?php echo time(); ?>', 'width=900,height=550,toolbar=0,menubar=0,location=0,status=1,scrollbars=1,resizable=1,left=0,top=0');
                                                return false;" >
                                    </div>
                                </div>-->

                                <!--<div class="form-group row">
                                    <label for="document" class="col-sm-4 col-form-label">Document <span class="required_sign">*</span></label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="document" name="document" placeholder="Click to upload" onfocus="this.blur();" onclick="window.open('../includes/tinymce/plugins/filemanager/dialog.php?type=2&fldr=fs_documentation_docs&field_id=document&popup=1', '<?php echo time(); ?>', 'width=900,height=550,toolbar=0,menubar=0,location=0,status=1,scrollbars=1,resizable=1,left=0,top=0'); return false;" >
                                    </div>
                                </div>-->

                                <script>
                                    function responsive_filemanager_callback(field_id) {
                                        var url = jQuery('#' + field_id).val();
                                        url = url.replace("https://<?php echo $_SERVER['HTTP_HOST']; ?>/assets/fs_documentation_docs/", '');
                                        url = url.replace("https://<?php echo $_SERVER['HTTP_HOST']; ?>/assets/fs_documentation/", '');
                                        if ( url.length > 65 ) {
                                          alert('The length of your file name is over the limit of 65 characters. Please rename your file and try again.');
                                          jQuery('#' + field_id).val('');
                                        } else {
                                          jQuery('#' + field_id).val(url);
                                        }
                                    }
                                </script>

                                <!--<div class="form-group row">
                                    <label for="document_type_id" class="col-sm-4 col-form-label">Document Type <span class="required_sign">*</span></label>
                                    <div class="col-sm-8">
                                        <select name="document_type_id" id="document_type_id" class="form-control" required>
                                            <option value="">Select an option...</option>
                                <?php // echo $FSDocumentation->getDocument_types_Document_typeDropdown(( $msg ) ? $_POST['document_type_id'] : 0 ); ?>
                                        </select>
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
                                    <div class="col-sm-4">Active</div>
                                    <div class="col-sm-8">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="active" name="active" value="1"  <?php if (isset($_POST['active']) && (int) $_POST['active'] || true) echo "CHECKED"; ?>>
                                        </div>
                                    </div>
                                </div>-->


                                <div class="form-group row">
                                    <div class="col-sm-12">
                                        <button type="submit" class="btn action_btn float-right save">Create</button>
                                        <button type="button" id="cancel" name="cancel" class="btn action_btn cancel float-right" onClick="window.location.href = '../fs_program_entries/edit.php?id=<?php echo $sid; ?>';">Cancel</button>
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

                                                if ($('#title').val() == '')
                                                    return createError('title', 'Please enter a valid title');
                                                if ($('#description').val() == '')
                                                    return createError('description', 'Please enter a valid description');
                                                if ($('#image').val() == '')
                                                    return createError('image', 'Please enter a valid image');
                                                if ($('#document').val() == '')
                                                    return createError('document', 'Please enter a valid document');
                                                return true;
                                            }

                                            function hasHtml5Validation() {
                                                return typeof document.createElement('input').checkValidity === 'function';
                                            }
        </script>

    </body>

</html>
<?php $conn->close(); ?>