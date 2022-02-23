<?php
$title =  'shoppers';
$subtitle = 'shopper_program';
require( '../config.php' );

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

<?php require( '../includes/header_new.php' );?>
    <script type="text/javascript" src="<?php echo ADMIN_URL?>/includes/tinymce/tinymce.min.js"></script>
    <div class="dashboard-sub-menu-sec">
        <div class="container">
            <div class="sub-menu-sec">
                <?php require( '../includes/shopper_sub_nav.php' );?>
            </div>
        </div>
    </div>

    <div class="latest_activities hd-grid activity_log">
        <div class="container">
            <div class="heading_sec">
                <h2><bold>Edit A</bold> SHOPPER PROGRAM</h2>
            </div>
            <div class="add-new-entry-sec">
                <button type="button" id="cancel" name="cancel" class="btn btn-primary back-btn" onclick="window.location.href = '<?php echo ADMIN_URL?>/shopper_programs/index.php';">
                    <img src="<?php echo ADMIN_URL?>/images/back-button.png" alt="back">
                </button>
            </div>
        </div>
    </div>

    <div class="main-form">
        <div class="container">
            <?php if ($msg) echo "<div class=\"alert alert-success\">$msg</div>"; ?>
            <form action="<?php echo ADMIN_URL?>/shopper_programs/edit.php" role="form" method="POST" onSubmit="return validateForm();" >
                <input type="hidden" name="update" value="<?php echo $row['id']; ?>">
                <input type="hidden" name="upd_token" value="<?php echo $_SESSION['upd_token']; ?>">

                <div class="form-group text-box">
                    <label for="fname">Title *</label><br>
                    <input type="text" id="title" name="title" onKeyUp="updateCountdown('#title', 85, '#title_lbl');" placeholder="" required onKeyDown="updateCountdown('#title', 85, '#title_lbl');" value="<?php echo ( $msg ) ? $_POST['title'] : $row['title']; ?>" maxlength="85">
                    <span id="title_lbl" class="small"></span>
                </div>

                <?php if ($row['image'] != '') { ?>

                    <div class="form-group text-box">
                        <label for="fname">Image</label><br>
                        <div class="col-sm-12">
                            <img src="<?php echo ADMIN_URL?>/timThumb.php?src=/assets/shopper_programs/<?php echo $row['image']; ?>&w=200&h=80" style="border: 1px solid #CCCCCC;padding: 2px;margin: 4px;">
                            <br>
                            <a href="<?php echo ADMIN_URL?>/shopper_programs/edit.php?id=<?php echo $id; ?>&del_image=1" class="btn action_btn cancel">Remove Image</a>
                        </div>

                    </div>
                <?php } else { ?>

                    <div class="form-group text-box">
                        <label for="fname">Image</label><br>
                        <input type="text"  id="image" name="image" placeholder="Click to upload" onfocus="this.blur();" onclick="window.open('../includes/tinymce/plugins/filemanager/dialog.php?type=1&fldr=shopper_programs&field_id=image&popup=1', '<?php echo time(); ?>', 'width=900,height=550,toolbar=0,menubar=0,location=0,status=1,scrollbars=1,resizable=1,left=0,top=0');return false;" >
                        <small>Recommended Dimensions: 160px wide by 146px tall</small>
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

                    </div>
                <?php } ?>

                <div class="form-group text-box">
                    <label for="fname">Start Date *</label><br>
                    <input type="date" id="start_date" name="start_date"  placeholder="" required value="<?php echo ( $msg ) ? $_POST['start_date'] : $row['start_date']; ?>">
                </div>

                <div class="form-group text-box">
                    <label for="fname">End Date *</label><br>
                    <input type="date" id="end_date" name="end_date" class="form-control" placeholder="" required value="<?php echo ( $msg ) ? $_POST['end_date'] : $row['end_date']; ?>">
                </div>

                <div class="form-group text-box">
                    <label for="fname">Intro *</label><br>
                    <textarea id="intro" name="intro" rows="12" maxlength="255" required onKeyUp="updateCountdown('#intro', 255, '#intro_lbl');" onKeyDown="updateCountdown('#intro', 255, '#intro_lbl');"><?php echo ( $msg ) ? $_POST['intro'] : $row['intro']; ?></textarea>
                    <span id="intro_lbl" class="small"></span>
                </div>

                <div class="form-group text-box">
                    <label for="fname">Sort *</label><br>
                    <select name="sort" id="sort" required>
                        <option value="">Select an option...</option>
                        <?php echo $ShopperProgram->getRangeDropDown(1, 20, ( $msg ) ? $_POST['sort'] : $row['sort'] ); ?>
                    </select>
                </div>

                <div class="form-group checkbox-wrap">
                    <label for="fname">Status</label><br>
                    <div class="checkbox-inner">
                        <div>
                            <input name="active" id="active" type="checkbox" value="1" <?php if (isset($_POST['active']) || (int) $row['active']) echo "CHECKED"; ?>>
                            <label for="html">Active</label>
                        </div>
                    </div>
                </div>

                <button type="submit">
                    <img src="<?php echo ADMIN_URL?>/images/update-btn.png" onmouseover="this.src='<?php echo ADMIN_URL?>/images/update-btn-hvr.png'" onmouseout="this.src='<?php echo ADMIN_URL?>/images/update-btn.png'" alt="login-submit-btn">
                </button>

                <button type="button" id="cancel" name="cancel" onClick="window.location.href = '<?php echo ADMIN_URL?>/shopper_programs/index.php';">
                    <img src="<?php echo ADMIN_URL?>/images/cancel-btn.png" onmouseover="this.src='<?php echo ADMIN_URL?>/images/cancel-hvr-btn.png'" onmouseout="this.src='<?php echo ADMIN_URL?>/images/cancel-btn.png'" alt="login-submit-btn">
                </button>
            </form>
        </div>
    </div>

    <div class="entry-section shopper-partner">
        <div class="container">
            <div class="entry-list">
                <div class="entry-row heading">
                    <div class="title-col">
                        <h3>Partners</h3>
                    </div>
                    <div class="active-col">
                        <h3>Action</h3>
                    </div>
                </div>

                <?php
                $sql = "SELECT a.shopper_partner_id, b.title FROM shopper_programs_and_partners a, shopper_partners b
                                        WHERE a.shopper_partner_id = b.id AND a.shopper_program_id = ?
                ORDER BY b.title;";
                $result = $conn->query($sql, array($row['id']));

                if ($conn->num_rows() > 0) {
                $num_results = $conn->num_rows();

                while ($row_ch = $conn->fetch($result)) { ?>


                <div class="entry-row">
                    <div class="title-col">
                        <div class="title-sec">
                            <h4><?php echo stripslashes($row_ch['title'])?></h4>
                        </div>
                    </div>
                    <div class="active-col">
                        <div class="action-sec">
                            <a href="<?php echo ADMIN_URL?>/shopper_programs/edit.php?id=<?php echo $row['id']?>&del_par=<?php echo $row_ch['shopper_partner_id']?>" onClick="return confirm( 'Are you sure you want to delete this item?')">
                                <img src="<?php echo ADMIN_URL?>/images/delete-btn.svg" alt=""/>
                            </a>
                        </div>
                    </div>
                </div>
                <?php }
                }else{?>
                    <div class="entry-row">
                        No partners found
                    </div>
                <?php }?>
            </div>


        </div>
    </div>

    <div class="main-form">
        <div class="container">
            <?php if ($msg) echo "<div class=\"alert alert-success\">$msg</div>"; ?>
            <form action="<?php echo ADMIN_URL?>/shopper_programs/edit.php?id=<?php echo $row['id']; ?>" method="POST" >
                <input type="hidden" name="add_par" value="1" class="hidden">

                <div class="form-group text-box">
                    <select name="shopper_partner_id">
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
                </div>


                <button type="submit" name="add">
                    <img src="<?php echo ADMIN_URL?>/images/login-submit-btn.png" onmouseover="this.src='<?php echo ADMIN_URL?>/images/login-submit-hvr-btn.png'" onmouseout="this.src='<?php echo ADMIN_URL?>/images/login-submit-btn.png'" alt="login-submit-btn">
                </button>
            </form>
        </div>
    </div>

    <div class="entry-section shopper-partner">
        <div class="container">
            <div class="entry-list">
                <div class="entry-row heading">
                    <div class="title-col">
                        <h3>Kit Options</h3>
                    </div>
                    <div class="active-col">
                        <h3>Action</h3>
                    </div>
                </div>

                <?php
                $sql = "SELECT id, title FROM shopper_program_bins WHERE shopper_program_id = ? ORDER BY sort;";
                $result = $conn->query($sql, array($row['id']));

                if ($conn->num_rows() > 0) {
                    $num_results = $conn->num_rows();

                    while ($row_ch = $conn->fetch($result)) { ?>


                        <div class="entry-row">
                            <div class="title-col">
                                <div class="title-sec">
                                    <h4><?php echo stripslashes($row_ch['title'])?></h4>
                                </div>
                            </div>
                            <div class="active-col">
                                <div class="action-sec">
                                    <a href="<?php echo ADMIN_URL?>/shopper_programs/edit.php?id=<?php echo $row_ch['id']?>&sid=<?php echo $row['id']?>">
                                        <img src="<?php echo ADMIN_URL?>/images/edit-btn.svg" alt=""/>
                                    </a>
                                    <a href="<?php echo ADMIN_URL?>/shopper_programs/edit.php?id=<?php echo $row['id']?>&del_bin=<?php echo $row_ch['id']?>" onClick="return confirm( 'Are you sure you want to delete this item?')">
                                        <img src="<?php echo ADMIN_URL?>/images/delete-btn.svg" alt=""/>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php }
                }else{?>
                    <div class="entry-row">
                        No bins found
                    </div>
                <?php }?>
                <div class="latest_activities hd-grid activity_log">
                    <div class="container">
                        <div class="add-new-entry-sec">
                            <button type="button" id="cancel" name="cancel" class="btn btn-primary" onclick="window.location.href = '<?php echo ADMIN_URL?>/shopper_program_bins/add.php?sid=<?php echo $row['id']; ?>';">
                                <img src="<?php echo ADMIN_URL?>/images/create-button.png" alt="back">
                            </button>
                        </div>
                    </div>
                </div>

            </div>


        </div>
    </div>

    <script>
        $(document).ready(function () {
            $('form:first *:input[type!=hidden]:first').focus();
        });
    </script>

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

<?php $conn->close(); ?>
<?php include('../includes/footer_new.php');?>