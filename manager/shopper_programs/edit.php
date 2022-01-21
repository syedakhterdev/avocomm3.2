<?php
session_start();
require( '../includes/pdo.php' );
require( '../includes/check_login.php' );
require( '../includes/ShopperProgramManager.php' );
$ShopperProgram = new ShopperProgramManager($conn);
$msg = '';

// check variables passed in through querystring
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$update = isset($_POST['update']) ? (int) $_POST['update'] : 0;

// if an id is passed in then let's fetch it from the database
if ($id) {
    if (!( $row = $ShopperProgram->getByID($id) )) {
        $msg = "Sorry, a record with the specified ID could not be found!";
    }

    $del_bin = isset($_GET['del_bin']) ? (int) $_GET['del_bin'] : 0;
    if ($del_bin) {
        if ($ShopperProgram->removeBins($id, $del_bin)) {
            $child_msg = "Bin deleted successfully!";
        } else {
            $child_msg = "Bin could not be deleted!";
        }
    }

    $del_par = isset($_GET['del_par']) ? (int) $_GET['del_par'] : 0;
    if ($del_par) {
        if ($ShopperProgram->removePartners($id, $del_par)) {
            $child_msg = "Partner deleted successfully!";
        } else {
            $child_msg = "Partner could not be deleted!";
        }
    }

    $add_par = isset($_POST['add_par']) ? (int) $_POST['shopper_partner_id'] : 0;
    if ($add_par) {
        if ($ShopperProgram->addToPartners($id, $add_par)) {
            $child_msg = "Partner added successfully!";
        } else {
            $child_msg = "Partner could not be added!";
        }
    }

    $del_image = ( isset($_GET['del_image']) ) ? (int) $_GET['del_image'] : 0;
    if ($del_image) {
        $ShopperProgram->removeImage($id);
        $row['image'] = '';
    }
} else if ($update) { // if the user submitted a form....
    $token = $_SESSION['upd_token'];
    unset($_SESSION['upd_token']);

    if (!$msg && $token != '' && $token == $_POST['upd_token']) {
        if (!$ShopperProgram->update($_POST['update'], $_POST['title'], $_POST['image'], $_POST['start_date'], $_POST['end_date'], $_POST['intro'], $_POST['sort'], $_POST['active'])) {
            $msg = "Sorry, an error has occurred, please contact your administrator.<br>Error: " . $ShopperProgram->error() . ";";
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
        <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300' rel='stylesheet' type='text/css'>
        <script>$(document).ready(function () {
                $('form:first *:input[type!=hidden]:first').focus();
            });</script>
        <script type="text/javascript" src="../includes/tinymce/tinymce.min.js"></script>

        <script type="text/javascript">
            $().ready(function () {
                updateCountdown('#title', 85, '#title_lbl');
                updateCountdown('#intro', 255, '#intro_lbl');
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
                            <h3>Edit a Shopper Program</h3>
                        </div>
                    </div>

                    <ol class="breadcrumb bc-3">
                        <li><a href="/manager/menu.php">Dashboard</a></li>
                        <li>&nbsp;/&nbsp;</li>
                        <li><a href="/manager/shopper_programs/">Shopper Programs</a></li>
                        <li>&nbsp;/&nbsp;</li>
                        <li><strong>Edit a Shopper Program</strong></li>
                    </ol>

                    <div class="row my-4 mgr_body shopper program_edit ">
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
                                                    <img src="/manager/timThumb.php?src=/assets/shopper_programs/<?php echo $row['image']; ?>&w=200&h=80" style="border: 1px solid #CCCCCC;padding: 2px;margin: 4px;">
                                                    <br>
                                                    <a href="edit.php?id=<?php echo $id; ?>&del_image=1" class="btn action_btn cancel">Remove Image</a>
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
                                                    <input type="text" class="form-control" id="image" name="image" placeholder="Click to upload" onfocus="this.blur();" onclick="window.open('../includes/tinymce/plugins/filemanager/dialog.php?type=1&fldr=shopper_programs&field_id=image&popup=1', '<?php echo time(); ?>', 'width=900,height=550,toolbar=0,menubar=0,location=0,status=1,scrollbars=1,resizable=1,left=0,top=0');return false;" >
                                                    <small>Recommended Dimensions: 160px wide by 146px tall</small>
                                                </div>
                                                <script>
                                                    function responsive_filemanager_callback(field_id) {
                                                        var url = jQuery('#' + field_id).val();
                                                        url = url.replace("https://<?php echo $_SERVER['HTTP_HOST']; ?>/assets/shopper_programs/", '');
                                                        if ( url.length > 65 ) {
                                                          alert('The length of your file name is over the limit of 65 characthers. Please rename your file and try again.');
                                                          jQuery('#' + field_id).val('');
                                                        } else {
                                                          jQuery('#' + field_id).val(url);
                                                        }
                                                    }
                                                </script>
                                            </td>
                                        </tr>
                                    </table>
                                <?php } ?>

                                <?php /* if ($row['image'] != '') { ?>
                                  <div class="form-group row">
                                  <label for="image" class="col-sm-4 col-form-label">Image</label>
                                  <div class="col-sm-8">
                                  <img src="/manager/timThumb.php?src=/assets/shopper_programs/<?php echo $row['image']; ?>&w=200&h=80" style="border: 1px solid #CCCCCC;padding: 2px;margin: 4px;">
                                  <br>
                                  <a href="edit.php?id=<?php echo $id; ?>&del_image=1">Remove Image</a>
                                  </div>
                                  </div>
                                  <?php } else { ?>
                                  <div class="form-group row">
                                  <label for="image" class="col-sm-4 col-form-label">Image</label>
                                  <div class="col-sm-8">
                                  <input type="text" class="form-control" id="image" name="image" placeholder="Click to upload" onfocus="this.blur();" onclick="window.open('../includes/tinymce/plugins/filemanager/dialog.php?type=1&fldr=shopper_programs&field_id=image&popup=1', '<?php echo time(); ?>', 'width=900,height=550,toolbar=0,menubar=0,location=0,status=1,scrollbars=1,resizable=1,left=0,top=0');return false;" >
                                  <small>Recommended dimensions 160px wide by 146px tall</small>
                                  </div>
                                  </div>
                                  <script>
                                  function responsive_filemanager_callback(field_id) {
                                  var url = jQuery('#' + field_id).val();
                                  url = url.replace("https://<?php echo $_SERVER['HTTP_HOST']; ?>/assets/shopper_programs/", '');
                                  jQuery('#' + field_id).val(url);
                                  }
                                  </script>
                                  <?php } */ ?>

                                <table style="width: 100%;" class="add_edit">
                                    <tr>
                                        <td>
                                            <h3>Start Date *</h3>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="col-sm-12">
                                                <input type="date" id="start_date" name="start_date" class="form-control" placeholder="" required value="<?php echo ( $msg ) ? $_POST['start_date'] : $row['start_date']; ?>">
                                            </div>
                                        </td>
                                    </tr>
                                </table>

                                <!--<div class="form-group row">
                                    <label for="start_date" class="col-sm-4 col-form-label">Start Date <span class="required_sign">*</span></label>
                                    <div class="col-sm-8">
                                        <input type="date" id="start_date" name="start_date" class="form-control" placeholder="" required value="<?php echo ( $msg ) ? $_POST['start_date'] : $row['start_date']; ?>">
                                    </div>
                                </div>-->

                                <table style="width: 100%;" class="add_edit">
                                    <tr>
                                        <td>
                                            <h3>End Date *</h3>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="col-sm-12">
                                                <input type="date" id="end_date" name="end_date" class="form-control" placeholder="" required value="<?php echo ( $msg ) ? $_POST['end_date'] : $row['end_date']; ?>">
                                            </div>
                                        </td>
                                    </tr>
                                </table>

                                <!--<div class="form-group row">
                                    <label for="end_date" class="col-sm-4 col-form-label">End Date <span class="required_sign">*</span></label>
                                    <div class="col-sm-8">
                                        <input type="date" id="end_date" name="end_date" class="form-control" placeholder="" required value="<?php echo ( $msg ) ? $_POST['end_date'] : $row['end_date']; ?>">
                                    </div>
                                </div>-->

                                <table style="width: 100%;" class="add_edit">
                                    <tr>
                                        <td>
                                            <h3>Intro *</h3>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="col-sm-12">
                                                <textarea id="intro" name="intro" class="form-control" rows="12" maxlength="255" required onKeyUp="updateCountdown('#intro', 255, '#intro_lbl');" onKeyDown="updateCountdown('#intro', 255, '#intro_lbl');"><?php echo ( $msg ) ? $_POST['intro'] : $row['intro']; ?></textarea>
                                                <span id="intro_lbl" class="small"></span>
                                            </div>
                                        </td>
                                    </tr>
                                </table>

                                <!--<div class="form-group row">
                                    <label for="intro" class="col-sm-4 col-form-label">Intro <span class="required_sign">*</span></label>
                                    <div class="col-sm-8">
                                        <textarea id="intro" name="intro" class="form-control" rows="12" maxlength="255" required onKeyUp="updateCountdown('#intro', 255, '#intro_lbl');" onKeyDown="updateCountdown('#intro', 255, '#intro_lbl');"><?php echo ( $msg ) ? $_POST['intro'] : $row['intro']; ?></textarea>
                                        <span id="intro_lbl" class="small"></span>
                                    </div>
                                </div>-->

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
                                                    <?php echo $ShopperProgram->getRangeDropDown(1, 20, ( $msg ) ? $_POST['sort'] : $row['sort'] ); ?>
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
                                <?php echo $ShopperProgram->getRangeDropDown(1, 20, ( $msg ) ? $_POST['sort'] : $row['sort'] ); ?>
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
                                        <button type="button" id="cancel" name="cancel" class="btn action_btn cancel float-right" onClick="window.location.href = 'index.php';">Cancel</button>
                                    </div>
                                </div>

                            </form>
                        </div>
                        <div style="clear:both;"></div>
                        <!-- children -->
                        <div class="col-lg-10 col-md-8 col-sm-12 col-xs-12 ">

                            <!--<h4>Partners</h4>-->
                            <table style="width: 100%;" class="add_edit">
                                <tr>
                                    <td colspan="2">
                                        <h3>Partners</h3>
                                    </td>
                                </tr>
                                <?php
                                $sql = "SELECT a.shopper_partner_id, b.title FROM shopper_programs_and_partners a, shopper_partners b
													WHERE a.shopper_partner_id = b.id AND a.shopper_program_id = ?
                      		ORDER BY b.title;";
                                $result = $conn->query($sql, array($row['id']));

                                if ($conn->num_rows() > 0) {
                                    $num_results = $conn->num_rows();

                                    while ($row_ch = $conn->fetch($result)) {
                                        echo "<tr>
                                                <td style=\"width: 190px;\">
                                                    " . stripslashes($row_ch['title']) . "
                                                </td>
                                                <td align=\"right\" nowrap>
                                                    <a href=\"edit.php?id=" . $row['id'] . "&del_par=" . $row_ch['shopper_partner_id'] . "\" onClick=\"return confirm( 'Are you sure you want to delete this item?')\" title=\"Delete\" class=\"btn action_btn delete\">
                                                        DELETE
                                                    </a>
                                                </td>
                                            </tr>\n";
                                    }
                                } else {
                                    echo '<tr><td colspan="2">No partners found</td></tr>';
                                }
                                ?>

                                <form action="edit.php?id=<?php echo $row['id']; ?>" method="POST">
                                    <input type="hidden" name="add_par" value="1" class="hidden">
                                    <tr>
                                        <td style="text-align:right;" colspan="2">
                                            <table style="width: 100%;">
                                                <tr>
                                                    <td>
                                                        <select name="shopper_partner_id" style="font-size: 12px;width: 100%;">
                                                            <option value="">Select...</option>
                                                            <?php
                                                            $sql = "SELECT a.id, a.title FROM shopper_partners a
															WHERE a.id NOT IN (
																SELECT shopper_partner_id FROM shopper_programs_and_partners WHERE shopper_program_id = ? ) ORDER BY a.title";
                                                            $items = $conn->query($sql, array($row['id']));
                                                            if ($conn->num_rows() > 0) {
                                                                while ($item = $conn->fetch($items)) {
                                                                    echo "<option value=\"" . $item['id'] . "\">" . stripslashes($item['title']) . "</option>\n";
                                                                }
                                                            } else {
                                                                echo "<option value=\"\">No partners found</option>";
                                                            }
                                                            ?>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="submit" value="Add" name="add" style="" class="btn action_btn add">
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr><td style="text-align:right;" colspan="2"><a href="../shopper_partners/add.php?sid=<?php echo $row['id']; ?>" class="btn action_btn add float-right">Add New</a></td></tr>
                                </form>

                            </table>

                            <!--<h4>Bins</h4>-->
                            <table style="width: 100%;" class="add_edit">
                                <tr>
                                    <td colspan="2">
                                        <h3>Kit Options</h3>
                                    </td>
                                </tr>
                                <?php
                                $sql = "SELECT id, title FROM shopper_program_bins WHERE shopper_program_id = ? ORDER BY sort;";
                                $result = $conn->query($sql, array($row['id']));

                                if ($conn->num_rows() > 0) {
                                    $num_results = $conn->num_rows();

                                    while ($row_ch = $conn->fetch($result)) {
                                        echo "<tr>
                                                <td style=\"width: 190px;\">
                                                    " . stripslashes($row_ch['title']) . "
                                                </td>
                                                <td align=\"right\" nowrap>
                                                    <a href=\"../shopper_program_bins/edit.php?id=" . $row_ch['id'] . "&sid=" . $row['id'] . "\" title=\"Edit\" class=\"btn action_btn edit\">
                                                        Edit
                                                    </a>&nbsp;
                                                    <a href=\"edit.php?id=" . $row['id'] . "&del_bin=" . $row_ch['id'] . "\" onClick=\"return confirm( 'Are you sure you want to delete this item?')\" title=\"Delete\" class=\"btn action_btn delete\">
                                                        DELETE
                                                    </a>
                                                </td>
                                            </tr>\n";
                                    }
                                } else {
                                    echo '<tr><td colspan="2">No bins found</td></tr>';
                                }
                                ?>
                                <tr><td style="text-align:right;" colspan="2"><a href="../shopper_program_bins/add.php?sid=<?php echo $row['id']; ?>" class="btn action_btn add float-right">Add New</a></td></tr>
                            </table>

                        </div>
                        <!-- /children -->


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

                                                    $('#start_date').datepicker();
                                                    $('#end_date').datepicker();
                                                }

                                                $('#submit').click(function () {
                                                    if (!hasHtml5Validation())
                                                        return validateForm();
                                                });
                                            });
                                            function validateForm() {

                                                if ($('#title').val() == '')
                                                    return createError('title', 'Please enter a valid title');
                                                if ($('#start_date').val() == '')
                                                    return createError('start_date', 'Please enter a valid start date');
                                                if ($('#end_date').val() == '')
                                                    return createError('end_date', 'Please enter a valid end date');
                                            }
                                            function hasHtml5Validation() {
                                                return typeof document.createElement('input').checkValidity === 'function';
                                            }
        </script>

    </body>

</html>
<?php $conn->close(); ?>