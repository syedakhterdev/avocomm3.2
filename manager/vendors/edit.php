<?php
$title =  'trades';
$subtitle = 'vendor_entries';
require( '../config.php' );


require( '../includes/pdo.php' );
require( '../includes/check_login.php' );
require( '../includes/VendorManager.php' );
$Vendor = new VendorManager($conn);
$msg = '';

// check variables passed in through querystring
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$update = isset($_POST['update']) ? (int) $_POST['update'] : 0;

// if an id is passed in then let's fetch it from the database
if ($id) {
    if (!( $row = $Vendor->getByID($id) )) {
        $msg = "Sorry, a record with the specified ID could not be found!";
    }

    $del_logo = ( isset($_GET['del_logo']) ) ? (int) $_GET['del_logo'] : 0;
    if ($del_logo) {
        $Vendor->removeLogo($id);
        $row['logo'] = '';
    }
} else if ($update) { // if the user submitted a form....
    $token = $_SESSION['upd_token'];
    unset($_SESSION['upd_token']);

    if (!$msg && $token != '' && $token == $_POST['upd_token']) {
        if (!$Vendor->update($_POST['update'], $_POST['title'], $_POST['logo'], $_POST['tier_id'], $_POST['sort'], $_POST['active'])) {
            $msg = "Sorry, an error has occurred, please contact your administrator.<br>Error: " . $Vendor->error() . ";";
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
    <div class="dashboard-sub-menu-sec trade-nav">
        <div class="container">
            <div class="sub-menu-sec">
                <?php require( '../includes/trade_sub_nav.php' );?>
            </div>
        </div>
    </div>

    <div class="latest_activities hd-grid activity_log">
        <div class="container">
            <div class="heading_sec">
                <h2><bold>Edit A</bold> TRADE VENDOR</h2>
            </div>
            <div class="add-new-entry-sec">
                <button type="button" id="cancel" name="cancel" class="btn btn-primary back-btn" onclick="window.location.href = '<?php echo ADMIN_URL?>/vendors/index.php';">
                    <img src="<?php echo ADMIN_URL?>/images/back-button.png" alt="back">
                </button>
            </div>
        </div>
    </div>

    <div class="main-form">
        <div class="container">
            <?php if ($msg) echo "<div class=\"alert alert-success\">$msg</div>"; ?>
            <form action="<?php echo ADMIN_URL?>/vendors/edit.php" role="form" method="POST" onSubmit="return validateForm();">
                <input type="hidden" name="update" value="<?php echo $row['id']; ?>">
                <input type="hidden" name="upd_token" value="<?php echo $_SESSION['upd_token']; ?>">

                <div class="form-group text-box">
                    <label for="fname">Title *</label><br>
                    <input type="text" id="title" name="title" onKeyUp="updateCountdown('#title', 65, '#title_lbl');" placeholder="" required onKeyDown="updateCountdown('#title', 65, '#title_lbl');" value="<?php echo ( $msg ) ? $_POST['title'] : $row['title']; ?>" maxlength="65">
                    <span id="title_lbl" class="small"></span>
                </div>


                <?php if ($row['logo'] != '') { ?>

                    <div class="form-group text-box">
                        <label for="fname">Logo</label><br>
                        <div class="col-sm-12">
                            <img src="<?php echo ADMIN_URL?>/timThumb.php?src=/assets/vendors/<?php echo $row['logo']; ?>&w=200" style="border: 1px solid #CCCCCC;padding: 2px;margin: 4px;">
                            <br>
                            <a href="<?php echo ADMIN_URL?>/vendors/edit.php?id=<?php echo $id; ?>&del_logo=1" class="btn action_btn cancel">Remove Image</a>
                        </div>

                    </div>
                <?php } else { ?>

                    <div class="form-group text-box">
                        <label for="fname">Logo</label><br>
                        <input type="text" class="form-control" id="logo" name="logo" placeholder="Click to upload" onfocus="this.blur();" onclick="window.open('../includes/tinymce/plugins/filemanager/dialog.php?type=1&fldr=vendors&field_id=logo&popup=1', '<?php echo time(); ?>', 'width=900,height=550,toolbar=0,menubar=0,location=0,status=1,scrollbars=1,resizable=1,left=0,top=0');return false;" >
                        <script>
                            function responsive_filemanager_callback(field_id) {
                                var url = jQuery('#' + field_id).val();
                                url = url.replace("https://<?php echo $_SERVER['HTTP_HOST']; ?>/assets/vendors/", '');
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
                    <label for="fname">Tier *</label><br>
                    <select name="tier_id" id="tier_id" required>
                        <option value="">Select an option...</option>
                        <?php echo $Vendor->getVendor_tiers_TierDropdown(( $msg ) ? $_POST['tier_id'] : $row['tier_id'] ); ?>
                    </select>
                </div>

                <div class="form-group text-box">
                    <label for="fname">Sort *</label><br>
                    <select name="sort" id="sort" required>
                        <option value="">Select an option...</option>
                        <?php echo $Vendor->getRangeDropDown(1, 20, ( $msg ) ? $_POST['sort'] : $row['sort'] ); ?>
                    </select>
                </div>

                <div class="form-group checkbox-wrap">
                    <label for="fname">Status</label><br>
                    <div class="checkbox-inner">
                        <div>
                            <input name="active" id="active" type="checkbox" value="1" <?php if (isset($_POST['active']) || (int) $row['active']) echo "CHECKED"; ?> >
                            <label for="html">Active</label>
                        </div>
                    </div>
                </div>


                <button type="submit">
                    <img src="<?php echo ADMIN_URL?>/images/update-btn.png" onmouseover="this.src='<?php echo ADMIN_URL?>/images/update-btn-hvr.png'" onmouseout="this.src='<?php echo ADMIN_URL?>/images/update-btn.png'" alt="login-submit-btn">
                </button>

                <button type="button" id="cancel" name="cancel" onClick="window.location.href = '<?php echo ADMIN_URL?>/vendors/index.php';">
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
        }
        function hasHtml5Validation() {
            return typeof document.createElement('input').checkValidity === 'function';
        }
    </script>
<?php $conn->close(); ?>
<?php include('../includes/footer_new.php');?>