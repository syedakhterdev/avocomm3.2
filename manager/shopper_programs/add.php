<?php
$title =  'shoppers';
$subtitle = 'shopper_program';
require( '../config.php' );

require( '../includes/pdo.php' );
require( '../includes/check_login.php' );
require( '../includes/ShopperProgramManager.php' );
$ShopperProgram = new ShopperProgramManager($conn);
$msg = '';

$insert = isset($_POST['insert']) ? (int) $_POST['insert'] : 0;


if ($insert) {
    $token = $_SESSION['add_token'];
    unset($_SESSION['add_token']);

    if (!$msg) {
        if ($token != '' && $token == $_POST['add_token']) {
            if (!$id = $ShopperProgram->add($_POST['title'], $_POST['image'], $_POST['start_date'], $_POST['end_date'], $_POST['intro'], $_POST['sort'], $_POST['active'])) {
                $msg = "Sorry, an error has occurred, please contact your administrator.<br>Error: " . $ShopperProgram->error() . ";";
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
<?php require( '../includes/header_new.php' );?>
    <script type="text/javascript" src="<?php echo ADMIN_URL?>/includes/tinymce/tinymce.min.js"></script>
    <div class="dashboard-sub-menu-sec shopper-nav">
        <div class="container">
            <div class="sub-menu-sec">
                <?php require( '../includes/shopper_sub_nav.php' );?>
            </div>
        </div>
    </div>

    <div class="latest_activities hd-grid activity_log">
        <div class="container">
            <div class="heading_sec">
                <h2><bold>Add A</bold> SHOPPER PROGRAM</h2>
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
            <form action="<?php echo ADMIN_URL?>/shopper_programs/add.php" role="form" method="POST" onSubmit="return validateForm();" >
                <input type="hidden" name="insert" value="1">
                <input type="hidden" name="add_token" value="<?php echo $_SESSION['add_token']; ?>">

                <div class="form-group text-box">
                    <label for="fname">Title *</label><br>
                    <input type="text" id="title" name="title" onKeyUp="updateCountdown('#title', 85, '#title_lbl');" placeholder="" required onKeyDown="updateCountdown('#title', 85, '#title_lbl');" value="<?php echo ( $msg ) ? $_POST['title'] : ''; ?>" maxlength="85">
                    <span id="title_lbl" class="small"></span>
                </div>


                <div class="form-group text-box">
                    <label for="fname">Image</label><br>
                    <input type="text" id="image" name="image" placeholder="Click to upload" onfocus="this.blur();" onclick="window.open('../includes/tinymce/plugins/filemanager/dialog.php?type=1&fldr=shopper_programs&field_id=image&popup=1', '<?php echo time(); ?>', 'width=900,height=550,toolbar=0,menubar=0,location=0,status=1,scrollbars=1,resizable=1,left=0,top=0');return false;" >
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


                <div class="form-group text-box">
                    <label for="fname">Start Date *</label><br>
                    <input type="date" id="start_date" name="start_date" placeholder="" required value="<?php echo ( $msg ) ? $_POST['start_date'] : ''; ?>">
                </div>

                <div class="form-group text-box">
                    <label for="fname">End Date *</label><br>
                    <input type="date" id="end_date" name="end_date" class="form-control" placeholder="" required value="<?php echo ( $msg ) ? $_POST['end_date'] : ''; ?>">
                </div>

                <div class="form-group text-box">
                    <label for="fname">Intro *</label><br>
                    <textarea id="intro" name="intro" required rows="3" required onKeyUp="updateCountdown('#intro', 255, '#intro_lbl');" onKeyDown="updateCountdown('#intro', 255, '#intro_lbl');" maxlength="255"><?php echo ( $msg ) ? $_POST['intro'] : ''; ?></textarea>
                    <span id="intro_lbl" class="small"></span>
                </div>

                <div class="form-group text-box">
                    <label for="fname">Sort *</label><br>
                    <select name="sort" id="sort" required>
                        <option value="">Select an option...</option>
                        <?php echo $ShopperProgram->getRangeDropDown(1, 20, ( $msg ) ? $_POST['sort'] : 0 ); ?>
                    </select>
                </div>

                <div class="form-group checkbox-wrap">
                    <label for="fname">Status</label><br>
                    <div class="checkbox-inner">
                        <div>
                            <input type="checkbox" id="active" name="active" value="1"  <?php if (isset($_POST['active']) && (int) $_POST['active'] || true) echo "CHECKED"; ?>>
                            <label for="html">Active</label>
                        </div>
                    </div>
                </div>

                <button type="submit">
                    <img src="<?php echo ADMIN_URL?>/images/login-submit-btn.png" onmouseover="this.src='<?php echo ADMIN_URL?>/images/login-submit-hvr-btn.png'" onmouseout="this.src='<?php echo ADMIN_URL?>/images/login-submit-btn.png'" alt="login-submit-btn">
                </button>

                <button type="button" id="cancel" name="cancel" onClick="window.location.href = '<?php echo ADMIN_URL?>/shopper_programs/index.php';">
                    <img src="<?php echo ADMIN_URL?>/images/cancel-btn.png" onmouseover="this.src='<?php echo ADMIN_URL?>/images/cancel-hvr-btn.png'" onmouseout="this.src='<?php echo ADMIN_URL?>/images/cancel-btn.png'" alt="login-submit-btn">
                </button>
            </form>
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
            return true;
        }

        function hasHtml5Validation() {
            return typeof document.createElement('input').checkValidity === 'function';
        }
    </script>
<?php $conn->close(); ?>
<?php include('../includes/footer_new.php');?>