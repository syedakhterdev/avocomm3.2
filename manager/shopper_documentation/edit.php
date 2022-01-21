<?php
session_start();
require( '../includes/pdo.php' );
require( '../includes/check_login.php' );
require( '../includes/ShopperDocumentationManager.php' );
$ShopperDocumentation = new ShopperDocumentationManager($conn);
$msg = '';
if ( !(int)$_SESSION['admin_permission_shopper_hub'] ) header( 'Location: ../menu.php' );

// check variables passed in through querystring
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$update = isset($_POST['update']) ? (int) $_POST['update'] : 0;
$sid = (int) $_GET['sid'];

// if an id is passed in then let's fetch it from the database
if ($id) {
    if (!( $row = $ShopperDocumentation->getByID($_SESSION['admin_period_id'], $id) )) {
        $msg = "Sorry, a record with the specified ID could not be found!";
    }

    $del_image = ( isset($_GET['del_image']) ) ? (int) $_GET['del_image'] : 0;
    if ($del_image) {
        $ShopperDocumentation->removeImage($id);
        $row['image'] = '';
    }
    $del_document = ( isset($_GET['del_document']) ) ? (int) $_GET['del_document'] : 0;
    if ($del_document) {
        $ShopperDocumentation->removeDocument($id);
        $row['document'] = '';
    }
} else if ($update) { // if the user submitted a form....
    $token = $_SESSION['upd_token'];
    unset($_SESSION['upd_token']);

    if (!$msg && $token != '' && $token == $_POST['upd_token']) {
        if (!$ShopperDocumentation->update($_POST['update'], $sid, $_SESSION['admin_period_id'], $_POST['title'], $_POST['description'], $_POST['image'], $_POST['document'], $_POST['document_type_id'], $_POST['active'])) {
            $msg = "Sorry, an error has occurred, please contact your administrator.<br>Error: " . $ShopperDocumentation->error() . ";";
        } else {
          if ( $_POST['old_document'] != $_POST['document'] && ( $_POST['document'] != '' || $_POST['image'] == '' ) ) { // if the document has changed, and it's an image, and no preview image was specified
            if ( isset( $_POST['document'] ) && $_POST['document'] != '' ) {
              $document = $_POST['document'];
            } else if ( !isset( $_POST['document'] ) && $_POST['old_document'] != '' ) {
              $document = $_POST['old_document'];
            }
            $ext = pathinfo( $document, PATHINFO_EXTENSION );
            if ( (int)$_POST['document_type_id'] == 1 && in_array( $ext, array( 'jpg', 'jpeg', 'png', 'gif' ) ) ) { // if the document type is an image create a thumbnail
              $ShopperDocumentation->makeThumbnail( $update, $_SERVER['DOCUMENT_ROOT'] . "/assets/shopper_documentation_docs/", $document, $_SERVER['DOCUMENT_ROOT'] . "/assets/shopper_documentation/" );
              $sql = 'UPDATE shopper_documentation SET image = ? WHERE id = ?';
              $conn->exec( $sql, array( $document, $update ) );
            }
          }

            if ($sid)
                header("Location: ../shopper_program_entries/edit.php?upd=1&id=$sid");
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
        <link rel="icon" href="/assets/cropped-favicon-150x150.png" sizes="32x32">
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
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
                            <h3>Edit a <?php echo ENTITY; ?></h3>
                        </div>
                    </div>

                    <ol class="breadcrumb bc-3">
                        <li><a href="/manager/menu.php">Dashboard</a></li>
                        <li>&nbsp;/&nbsp;</li>
                        <li><strong>Edit a Shopper Documentation</strong></li>
                    </ol>

                    <div class="row my-4 mgr_body shopper program_edit documentation_add">
                        <div class="col-lg-10 col-md-8">

                            <form action="edit.php?sid=<?php echo $sid; ?>" role="form" method="POST" onSubmit="return validateForm();">
                                <input type="hidden" name="update" value="<?php echo $row['id']; ?>">
                                <input type="hidden" name="upd_token" value="<?php echo $_SESSION['upd_token']; ?>">
                                <input type="hidden" name="old_document" value="<?php echo $row['document']; ?>">

                                <?php if ($msg) echo "<div class=\"alert alert-danger\">$msg</div>"; ?>

                                <div class="form-group row">
                                    <div class="col-sm-12">
                                        <button type="button" id="cancel" name="back" class="btn btn-default back_btn float-right" onclick="window.location.href = '../shopper_program_entries/edit.php?id=<?php echo $sid; ?>';">Back</button>
                                    </div>
                                </div>

                                <input type="hidden" name="shopper_program_id" id="shopper_program_id" value="<?php echo $pid; ?>">

                                <table style="width: 100%;" class="add_edit">
                                    <tr>
                                        <td>
                                            <h3>Document Type</h3>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="col-sm-12">
                                                <select name="document_type_id" id="document_type_id" class="form-control" required>
                                                    <option value="">Select an option...</option>
                                                    <?php echo $ShopperDocumentation->getDocument_types_Document_typeDropdown(( $msg ) ? $_POST['document_type_id'] : $row['document_type_id'] ); ?>
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
                                                <input type="text" class="form-control" id="title" name="title" onKeyUp="updateCountdown('#title', 65, '#title_lbl');" placeholder="" required onKeyDown="updateCountdown('#title', 65, '#title_lbl');" value="<?php echo ( $msg ) ? $_POST['title'] : $row['title']; ?>" maxlength="65">
                                                <span id="title_lbl" class="small"></span>
                                            </div>
                                        </td>
                                    </tr>
                                </table>

                                <!--<div class="form-group row">
                                    <label for="title" class="col-sm-4 col-form-label">Title <span class="required_sign">*</span></label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="title" name="title" onKeyUp="updateCountdown('#title', 65, '#title_lbl');" placeholder="" required onKeyDown="updateCountdown('#title', 65, '#title_lbl');" value="<?php echo ( $msg ) ? $_POST['title'] : $row['title']; ?>" maxlength="65">
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
                                                <textarea name="description" id="description" class="form-control" rows="3" onkeyup="updateCountdown('#description, 255, '#description_lbl' );" onkeyDown="updateCountdown('#description', 255, '#description_lbl');" placeholder="" required><?php echo htmlspecialchars(( $msg ) ? $_POST['description'] : $row['description'] ); ?></textarea>
                                                <span id="description_lbl" class="small"></span>
                                            </div>
                                        </td>
                                    </tr>
                                </table>

                                <!--<div class="form-group row">
                                    <label for="description" class="col-sm-4 col-form-label">Description <span class="required_sign">*</span></label>
                                    <div class="col-sm-8">
                                        <textarea name="description" id="description" class="form-control" rows="3" onkeyup="updateCountdown('#description, 255, '#description_lbl' );" onkeyDown="updateCountdown('#description', 255, '#description_lbl');" placeholder="" required><?php echo htmlspecialchars(( $msg ) ? $_POST['description'] : $row['description'] ); ?></textarea>
                                        <span id="description_lbl" class="small"></span>
                                    </div>
                                </div>-->

                                <?php if ($row['document'] != '') { ?>
                                    <table style="width: 100%;" class="add_edit">
                                        <tr>
                                            <td>
                                                <h3>Document</h3>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="col-sm-12">
                                                  <?php
                                                  $ext = pathinfo( $row['doc'], PATHINFO_EXTENSION );
                                                  $icon = $ext ? "ico_$ext.png" : "ico_file.png";
                                                  ?>
                                                  <a href="/assets/shopper_documentation_docs/<?php echo $row['document']; ?>" download><img
                                                    src="/assets/icons/<?php echo $icon; ?>" width="50" height="66" style="border: 1px solid #CCCCCC;padding: 2px;margin: 4px;"></a>
                                                    <br>
                                                    <a href="edit.php?id=<?php echo $id; ?>&sid=<?php echo $sid; ?>&del_document=1" class="btn action_btn cancel float-left">Remove documentation</a>
                                                    <div class="clearfix"></div>
                                                    <small><?php echo $row['document']; ?></small>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                <?php } else { ?>
                                    <table style="width: 100%;" class="add_edit">
                                        <tr>
                                            <td>
                                                <h3>Document</h3>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="col-sm-12">
                                                    <input type="text" class="form-control" id="document" name="document" placeholder="Click to upload" onfocus="this.blur();" onclick="window.open('../includes/tinymce/plugins/filemanager/dialog.php?type=2&fldr=shopper_documentation_docs&field_id=document&popup=1', '<?php echo time(); ?>', 'width=900,height=550,toolbar=0,menubar=0,location=0,status=1,scrollbars=1,resizable=1,left=0,top=0'); return false;" maxlength="65">
                                                    <small>Please keep file names under 65 characters.</small>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                <?php } ?>

                                <?php if ($row['image'] != '') { ?>
                                    <table style="width: 100%;" class="add_edit">
                                        <tr>
                                            <td>
                                                <h3>Preview Image</h3>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="col-sm-12">
                                                    <img src="/manager/timThumb.php?src=/assets/shopper_documentation/<?php echo $row['image']; ?>&w=200&h=80" style="border: 1px solid #CCCCCC;padding: 2px;margin: 4px;">
                                                    <br>
                                                    <a href="edit.php?id=<?php echo $id; ?>&sid=<?php echo $sid; ?>&del_image=1" class="btn action_btn cancel float-left">Remove Image</a>
                                                    <div class="clearfix"></div>
                                                    <small><?php echo $row['image']; ?></small>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                <?php } else { ?>
                                    <table style="width: 100%;" class="add_edit">
                                        <tr>
                                            <td>
                                                <h3>Preview Image</h3>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="col-sm-12">
                                                    <input type="text" class="form-control" id="image" name="image" placeholder="Click to upload" onfocus="this.blur();" onclick="window.open('../includes/tinymce/plugins/filemanager/dialog.php?type=1&fldr=shopper_documentation&field_id=image&popup=1', '<?php echo time(); ?>', 'width=900,height=550,toolbar=0,menubar=0,location=0,status=1,scrollbars=1,resizable=1,left=0,top=0'); return false;" maxlength="65">
                                                    <small>Please keep file names under 65 characters.</small>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                <?php } ?>

                                <?php /* if ($row['image'] != '') { ?>
                                  <div class="form-group row">
                                  <label for="image" class="col-sm-4 col-form-label">Image <span class="required_sign">*</span></label>
                                  <div class="col-sm-8">
                                  <img src="/manager/timThumb.php?src=/assets/shopper_documentation/<?php echo $row['image']; ?>&w=200&h=80" style="border: 1px solid #CCCCCC;padding: 2px;margin: 4px;">
                                  <br>
                                  <a href="edit.php?id=<?php echo $id; ?>&del_image=1">Remove Image</a>
                                  </div>
                                  </div>
                                  <?php } else { ?>
                                  <div class="form-group row">
                                  <label for="image" class="col-sm-4 col-form-label">Image <span class="required_sign">*</span></label>
                                  <div class="col-sm-8">
                                  <input type="text" class="form-control" id="image" name="image" placeholder="Click to upload" onfocus="this.blur();" onclick="window.open('../includes/tinymce/plugins/filemanager/dialog.php?type=1&fldr=shopper_documentation&field_id=image&popup=1', '<?php echo time(); ?>', 'width=900,height=550,toolbar=0,menubar=0,location=0,status=1,scrollbars=1,resizable=1,left=0,top=0'); return false;" >
                                  </div>
                                  </div>
                                  <?php } */ ?>


                                <script>
                                    function responsive_filemanager_callback(field_id) {
                                        var url = jQuery('#' + field_id).val();
                                        url = url.replace("https://<?php echo $_SERVER['HTTP_HOST']; ?>/assets/shopper_documentation_docs/", '');
                                        url = url.replace("https://<?php echo $_SERVER['HTTP_HOST']; ?>/assets/shopper_documentation/", '');
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
                                <?php // echo $ShopperDocumentation->getDocument_types_Document_typeDropdown(( $msg ) ? $_POST['document_type_id'] : $row['document_type_id'] ); ?>
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
                                        <button type="submit" class="btn action_btn float-right save">Update</button>
                                        <button type="button" id="cancel" name="cancel" class="btn action_btn cancel float-right" onClick="window.location.href = '../shopper_program_entries/edit.php?id=<?php echo $sid; ?>';">Cancel</button>
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
                                            }
                                            function hasHtml5Validation() {
                                                return typeof document.createElement('input').checkValidity === 'function';
                                            }
        </script>

    </body>

</html>
<?php $conn->close(); ?>