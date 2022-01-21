<?php
session_start();
require( '../includes/pdo.php' );
require( '../includes/check_login.php' );
if (!(int) $_SESSION['admin_permission_reports'])
    header('Location: /manager/menu.php');
require( '../includes/ReportManager.php' );
$Report = new ReportManager($conn);
$msg = '';

// check variables passed in through querystring
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$update = isset($_POST['update']) ? (int) $_POST['update'] : 0;

// if an id is passed in then let's fetch it from the database
if ($id) {
    if (!( $row = $Report->getByID($_SESSION['admin_period_id'], $id) )) {
        $msg = "Sorry, a record with the specified ID could not be found!";
    }

    $del_image = ( isset($_GET['del_image']) ) ? (int) $_GET['del_image'] : 0;
    if ($del_image) {
        $Report->removeImage($id);
        $row['image'] = '';
    }

    $del_doc = ( isset($_GET['del_doc']) ) ? (int) $_GET['del_doc'] : 0;
    if ($del_doc) {
        $Report->removeDocument($id);
        $row['doc'] = '';
    }

} else if ($update) { // if the user submitted a form....
    $token = $_SESSION['upd_token'];
    unset($_SESSION['upd_token']);

    if (!$msg && $token != '' && $token == $_POST['upd_token']) {
        if (!$Report->update($_POST['update'], $_SESSION['admin_period_id'], $_POST['title'], $_POST['description'], $_POST['image'], $_POST['doc'], $_POST['sort'], $_POST['active'])) {
            $msg = "Sorry, an error has occurred, please contact your administrator.<br>Error: " . $Report->error() . ";";
        } else {
          if ( $_POST['old_doc'] != $_POST['doc'] && ( $_POST['doc'] != '' || $_POST['image'] == '' ) ) { // if the document has changed, and it's an image, and no preview image was specified
            if ( isset( $_POST['doc'] ) && $_POST['doc'] != '' ) {
              $document = $_POST['doc'];
            } else if ( !isset( $_POST['doc'] ) && $_POST['old_doc'] != '' ) {
              $document = $_POST['old_doc'];
            }
            $ext = pathinfo( $document, PATHINFO_EXTENSION );
            if ( (int)$_POST['document_type_id'] == 1 && in_array( $ext, array( 'jpg', 'jpeg', 'png', 'gif' ) ) ) { // if the document type is an image create a thumbnail
              $Report->makeThumbnail( $update, $_SERVER['DOCUMENT_ROOT'] . "/assets/report_docs/", $document, $_SERVER['DOCUMENT_ROOT'] . "/assets/reports/" );
              $sql = 'UPDATE reports SET image = ? WHERE id = ?';
              $conn->exec( $sql, array( $document, $update ) );
            }
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
                updateCountdown('#title', 85, '#title_lbl');
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
                        <li><a href="/manager/reports/">Reports</a></li>
                        <li>&nbsp;/&nbsp;</li>
                        <li><strong>Edit a Report</strong></li>
                    </ol>

                    <div class="row my-4 mgr_body reports report_edit program_edit">
                        <div class="col-lg-10 col-md-8">

                            <form action="edit.php" role="form" method="POST" onSubmit="return validateForm();">
                                <input type="hidden" name="update" value="<?php echo $row['id']; ?>">
                                <input type="hidden" name="upd_token" value="<?php echo $_SESSION['upd_token']; ?>">
                                <input type="hidden" name="old_doc" value="<?php echo $row['doc']; ?>">

                                <?php if ($msg) echo "<div class=\"alert alert-danger\">$msg</div>"; ?>

                                <div class="form-group row">
                                    <div class="col-sm-12">
                                        <button type="button" id="cancel" name="cancel" class="btn btn-default back_btn float-right" onclick="window.location.href = 'index.php';">Back</button>
                                    </div>
                                </div>

                                <table style="width: 100%;" class="add_edit">
                                    <tr>
                                        <td>
                                            <h3>Title *</h3>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="col-sm-12">
                                                <input type="text" class="form-control" id="title" name="title" onKeyUp="updateCountdown('#title', 85, '#title_lbl');" placeholder="" required onKeyDown="updateCountdown('#title', 85, '#title_lbl');" value="<?php echo ( $msg ) ? $_POST['title'] : $row['title']; ?>" maxlength="85">
                                                <span id="title_lbl" class="small"></span>
                                            </div>
                                        </td>
                                    </tr>
                                </table>

                                <!--<div class="form-group row">
                                    <label for="title" class="col-sm-4 col-form-label">Title <span class="required_sign">*</span></label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="title" name="title" onKeyUp="updateCountdown('#title', 85, '#title_lbl');" placeholder="" required onKeyDown="updateCountdown('#title', 85, '#title_lbl');" value="<?php echo ( $msg ) ? $_POST['title'] : $row['title']; ?>" maxlength="85">
                                        <span id="title_lbl" class="small"></span>
                                    </div>
                                </div>-->

                                <?php if ($row['image'] != '') { ?>
                                    <table style="width: 100%;" class="add_edit">
                                        <tr>
                                            <td>
                                                <h3>Image</h3>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="col-sm-12">
                                                    <img src="/manager/timThumb.php?src=/assets/reports/<?php echo $row['image']; ?>&w=200&h=80" style="border: 1px solid #CCCCCC;padding: 2px;margin: 4px;">
                                                    <br>
                                                    <a href="edit.php?id=<?php echo $id; ?>&del_image=1" style="width: 206px; margin: 4px;" class="btn cancel action_btn">Remove Image</a>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                <?php } else { ?>
                                    <table style="width: 100%;" class="add_edit">
                                        <tr>
                                            <td>
                                                <h3>Image</h3>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="col-sm-12">
                                                    <input type="text" class="form-control" id="image" name="image" placeholder="Click to upload" onfocus="this.blur();" onclick="window.open('../includes/tinymce/plugins/filemanager/dialog.php?type=1&fldr=reports&field_id=image&popup=1', '<?php echo time(); ?>', 'width=900,height=550,toolbar=0,menubar=0,location=0,status=1,scrollbars=1,resizable=1,left=0,top=0');
                                                            return false;" >
                                                    <small>Recommended dimensions 160px wide by 146px tall</small>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                <?php } ?>

                                <script>
                                    function responsive_filemanager_callback(field_id) {
                                        var url = jQuery('#' + field_id).val();
                                        url = url.replace("https://<?php echo $_SERVER['HTTP_HOST']; ?>/assets/reports/", '');
                                        url = url.replace("https://<?php echo $_SERVER['HTTP_HOST']; ?>/assets/report_docs/", '');
                                        jQuery('#' + field_id).val(url);
                                    }
                                </script>

                                <?php /* if ($row['image'] != '') { ?>
                                  <div class="form-group row">
                                  <label for="image" class="col-sm-4 col-form-label">Image</label>
                                  <div class="col-sm-8">
                                  <img src="/manager/timThumb.php?src=/assets/reports/<?php echo $row['image']; ?>&w=200&h=80" style="border: 1px solid #CCCCCC;padding: 2px;margin: 4px;">
                                  <br>
                                  <a href="edit.php?id=<?php echo $id; ?>&del_image=1">Remove Image</a>
                                  </div>
                                  </div>
                                  <?php } else { ?>
                                  <div class="form-group row">
                                  <label for="image" class="col-sm-4 col-form-label">Image</label>
                                  <div class="col-sm-8">
                                  <input type="text" class="form-control" id="image" name="image" placeholder="Click to upload" onfocus="this.blur();" onclick="window.open('../includes/tinymce/plugins/filemanager/dialog.php?type=1&fldr=reports&field_id=image&popup=1', '<?php echo time(); ?>', 'width=900,height=550,toolbar=0,menubar=0,location=0,status=1,scrollbars=1,resizable=1,left=0,top=0');
                                  return false;" >
                                  <small>Recommended dimensions 160px wide by 146px tall</small>
                                  </div>
                                  </div>
                                  <script>
                                  function responsive_filemanager_callback(field_id) {
                                  var url = jQuery('#' + field_id).val();
                                  url = url.replace("https://<?php echo $_SERVER['HTTP_HOST']; ?>/assets/reports/", '');
                                  jQuery('#' + field_id).val(url);
                                  }
                                  </script>
                                  <?php } */ ?>

                                <?php if ($row['doc'] != '') { ?>
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
                                                    <a href="/assets/report_docs/<?php echo $row['doc']; ?>" download><img src="/assets/icons/<?php echo $icon; ?>" width="50" height="66" style="border: 1px solid #CCCCCC;padding: 2px;margin: 4px;">
                                                    <br>
                                                    <a href="edit.php?id=<?php echo $id; ?>&del_doc=1" class="btn action_btn cancel">Remove Document</a>
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
                                                    <input type="text" class="form-control" id="doc" name="doc" placeholder="Click to upload" onfocus="this.blur();" onclick="window.open('../includes/tinymce/plugins/filemanager/dialog.php?type=2&fldr=report_docs&field_id=doc&popup=1', '<?php echo time(); ?>', 'width=900,height=550,toolbar=0,menubar=0,location=0,status=1,scrollbars=1,resizable=1,left=0,top=0');
                                                                return false;" >
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                <?php } ?>

                                <?php /* if ($row['doc'] != '') { ?>
                                  <div class="form-group row">
                                  <label for="doc" class="col-sm-4 col-form-label">Document</label>
                                  <div class="col-sm-8">
                                  <img src="/manager/timThumb.php?src=/assets/report_docs/<?php echo $row['image']; ?>&w=200&h=80" style="border: 1px solid #CCCCCC;padding: 2px;margin: 4px;">
                                  <br>
                                  <a href="edit.php?id=<?php echo $id; ?>&del_doc=1">Remove Document</a>
                                  </div>
                                  </div>
                                  <?php } else { ?>
                                  <div class="form-group row">
                                  <label for="image" class="col-sm-4 col-form-label">Image</label>
                                  <div class="col-sm-8">
                                  <input type="text" class="form-control" id="doc" name="doc" placeholder="Click to upload" onfocus="this.blur();" onclick="window.open('../includes/tinymce/plugins/filemanager/dialog.php?type=2&fldr=report_docs&field_id=doc&popup=1', '<?php echo time(); ?>', 'width=900,height=550,toolbar=0,menubar=0,location=0,status=1,scrollbars=1,resizable=1,left=0,top=0');
                                  return false;" >
                                  </div>
                                  </div>
                                  <script>
                                  function responsive_filemanager_callback(field_id) {
                                  var url = jQuery('#' + field_id).val();
                                  url = url.replace("https://<?php echo $_SERVER['HTTP_HOST']; ?>/assets/reports/", '');
                                  jQuery('#' + field_id).val(url);
                                  }
                                  </script>
                                  <?php } */ ?>

                                <table style="width: 100%;" class="add_edit">
                                    <tr>
                                        <td>
                                            <h3>Sort *</h3>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="col-sm-12">
                                                <select name="sort" id="sort" class="form-control" required>
                                                    <option value="">Select an option...</option>
                                                    <?php echo $Report->getRangeDropDown(1, 20, ( $msg ) ? $_POST['sort'] : $row['sort'] ); ?>
                                                </select>
                                            </div>
                                        </td>
                                    </tr>
                                </table>

                                <!--<div class="form-group row">
                                    <label for="sort" class="col-sm-4 col-form-label">Sort <span class="required_sign">*</span></label>
                                    <div class="col-sm-8">
                                        <select name="sort" id="sort" class="form-control" required>
                                            <option value="">Select an option...</option>
                                <?php // echo $Report->getRangeDropDown(1, 20, ( $msg ) ? $_POST['sort'] : $row['sort'] ); ?>
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

                                <!--                                <div class="form-group row">
                                                                    <div class="col-sm-4"><label for="active">Active</label></div>
                                                                    <div class="col-sm-8">
                                                                        <div class="form-check">
                                                                            <input name="active" id="active" type="checkbox" value="1" <?php if (isset($_POST['active']) || (int) $row['active']) echo "CHECKED"; ?> class="form-check-input">
                                                                        </div>
                                                                    </div>
                                                                </div>-->

                                <div class="form-group row">
                                    <div class="col-sm-12">
                                        <button type="submit" class="btn action_btn save float-right" style="min-width: 125px;">Update</button>
                                        <button type="button" id="cancel" name="cancel" class="btn action_btn cancel float-right" onClick="window.location.href = 'index.php';" style="min-width: 125px;">Cancel</button>
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

                                                var datefield = document.createElement("input")
                                                datefield.setAttribute("type", "date")

                                                if (datefield.type != "date") { //if browser doesn't support input type="date", initialize date picker widget:
                                                }

                                                $('#submit').click(function () {
                                                    if (!hasHtml5Validation())
                                                        return validateForm();
                                                });
                                            });
                                            function validateForm() {

                                                if ($('#title').val() == '')
                                                    return createError('title', 'Please enter a valid title');
                                            }
                                            function hasHtml5Validation() {
                                                return typeof document.createElement('input').checkValidity === 'function';
                                            }
        </script>

    </body>

</html>
<?php $conn->close(); ?>