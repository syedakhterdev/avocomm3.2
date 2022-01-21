<?php
session_start();
require( '../includes/pdo.php' );
require( '../includes/check_login.php' );
if (!(int) $_SESSION['admin_permission_reports'])
    header('Location: /manager/menu.php');
require( '../includes/ReportManager.php' );
$Report = new ReportManager($conn);
$msg = '';
$insert = isset($_POST['insert']) ? (int) $_POST['insert'] : 0;

if ($insert) {
    $token = $_SESSION['add_token'];
    unset($_SESSION['add_token']);

    if (!$msg) {
        if ($token != '' && $token == $_POST['add_token']) {
            if (!$id = $Report->add($_SESSION['admin_period_id'], $_POST['title'], $_POST['description'], $_POST['image'], $_POST['doc'], $_POST['sort'], $_POST['active'])) {
                $msg = "Sorry, an error has occurred, please contact your administrator.<br>Error: " . $Report->error() . ";";
            } else {
              if ( isset( $_POST['doc'] ) && $_POST['doc'] != '' ) { // if the document type is an image create a thumbnail
                $ext = pathinfo( $_POST['doc'], PATHINFO_EXTENSION );
                if ( $_POST['image'] == '' && in_array( $ext, array( 'jpg', 'jpeg', 'png', 'gif' ) ) ) {
                  $Report->makeThumbnail( $id, $_SERVER['DOCUMENT_ROOT'] . "/assets/report_docs/", $_POST['doc'], $_SERVER['DOCUMENT_ROOT'] . "/assets/reports/" );
                  $sql = 'UPDATE reports SET image = ? WHERE id = ?';
                  $conn->exec( $sql, array( $_POST['doc'], $id ) );
                }
              }

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
        <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300' rel='stylesheet' type='text/css'>
        <link rel="icon" href="/assets/cropped-favicon-150x150.png" sizes="32x32">
        <script>$(document).ready(function () {
                $('form:first *:input[type!=hidden]:first').focus();
            });</script>
        <script type="text/javascript" src="../includes/tinymce/tinymce.min.js"></script>

        <script type="text/javascript">
            $().ready(function () {
                updateCountdown('#title', 85, '#title_lbl');
                updateCountdown('#description', 85, '#description_lbl');
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
                        <li><a href="/manager/reports/">Reports</a></li>
                        <li>&nbsp;/&nbsp;</li>
                        <li><strong>Add a Report</strong></li>
                    </ol>

                    <div class="row my-4 mgr_body reports report_add program_edit">
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
                                            <h3>Title *</h3>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="col-sm-12">
                                                <input type="text" class="form-control" id="title" name="title" onKeyUp="updateCountdown('#title', 85, '#title_lbl');" placeholder="" required onKeyDown="updateCountdown('#title', 85, '#title_lbl');" value="<?php echo ( $msg ) ? $_POST['title'] : ''; ?>" maxlength="85">
                                                <span id="title_lbl" class="small"></span>
                                            </div>
                                        </td>
                                    </tr>
                                </table>

                                <!--<div class="form-group row">
                                    <label for="title" class="col-sm-4 col-form-label">Title <span class="required_sign">*</span></label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="title" name="title" onKeyUp="updateCountdown('#title', 85, '#title_lbl');" placeholder="" required onKeyDown="updateCountdown('#title', 85, '#title_lbl');" value="<?php echo ( $msg ) ? $_POST['title'] : ''; ?>" maxlength="85">
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
                                                <textarea id="description" name="description" class="form-control" required rows="3" required onKeyUp="updateCountdown('#description', 255, '#description_lbl');" onKeyDown="updateCountdown('#description', 255, '#description_lbl');" maxlength="255"><?php echo ( $msg ) ? $_POST['description'] : ''; ?></textarea>
                                                <span id="description_lbl" class="small"></span>
                                            </div>
                                        </td>
                                    </tr>
                                </table>

                                <!--<div class="form-group row">
                                    <label for="description" class="col-sm-4 col-form-label">Description <span class="required_sign">*</span></label>
                                    <div class="col-sm-8">
                                        <textarea id="description" name="description" class="form-control" required rows="3" required onKeyUp="updateCountdown('#description', 255, '#description_lbl');" onKeyDown="updateCountdown('#description', 255, '#description_lbl');" maxlength="255"><?php echo ( $msg ) ? $_POST['description'] : ''; ?></textarea>
                                        <span id="description_lbl" class="small"></span>
                                    </div>
                                </div>-->

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

                                <!--<div class="form-group row">
                                    <label for="image" class="col-sm-4 col-form-label">Image</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="image" name="image" placeholder="Click to upload" onfocus="this.blur();" onclick="window.open('../includes/tinymce/plugins/filemanager/dialog.php?type=1&fldr=reports&field_id=image&popup=1', '<?php echo time(); ?>', 'width=900,height=550,toolbar=0,menubar=0,location=0,status=1,scrollbars=1,resizable=1,left=0,top=0');
                                                return false;" >
                                        <small>Recommended dimensions 160px wide by 146px tall</small>
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
                                                <input type="text" class="form-control" id="doc" name="doc" placeholder="Click to upload" onfocus="this.blur();" onclick="window.open('../includes/tinymce/plugins/filemanager/dialog.php?type=2&fldr=report_docs&field_id=doc&popup=1', '<?php echo time(); ?>', 'width=900,height=550,toolbar=0,menubar=0,location=0,status=1,scrollbars=1,resizable=1,left=0,top=0');return false;" >
                                            </div>
                                        </td>
                                    </tr>
                                </table>

                                <!--<div class="form-group row">
                                    <label for="doc" class="col-sm-4 col-form-label">Document</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="doc" name="doc" placeholder="Click to upload" onfocus="this.blur();" onclick="window.open('../includes/tinymce/plugins/filemanager/dialog.php?type=2&fldr=report_docs&field_id=doc&popup=1', '<?php echo time(); ?>', 'width=900,height=550,toolbar=0,menubar=0,location=0,status=1,scrollbars=1,resizable=1,left=0,top=0');return false;" >
                                    </div>
                                </div>-->

                                <script>
                                    function responsive_filemanager_callback(field_id) {
                                        var url = jQuery('#' + field_id).val();
                                        url = url.replace("https://<?php echo $_SERVER['HTTP_HOST']; ?>/assets/reports/", '');
                                        url = url.replace("https://<?php echo $_SERVER['HTTP_HOST']; ?>/assets/report_docs/", '');

                                        if ( url.length > 65 )
                                          alert( 'The file name you have selected is too long. Please rename the file and try again.' );
                                        else
                                          jQuery('#' + field_id).val(url);
                                    }
                                </script>

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
                                                    <?php echo $Report->getRangeDropDown(1, 20, ( $msg ) ? $_POST['sort'] : 0 ); ?>
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
                                <?php // echo $Report->getRangeDropDown(1, 20, ( $msg ) ? $_POST['sort'] : 0 ); ?>
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
                                                return true;
                                            }

                                            function hasHtml5Validation() {
                                                return typeof document.createElement('input').checkValidity === 'function';
                                            }
        </script>

    </body>

</html>
<?php $conn->close(); ?>