<?php
$title =  'trades';
$subtitle = 'trade_entries';
require( '../config.php' );

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

<?php require( '../includes/header_new.php' );?>
<script type="text/javascript" src="<?php echo ADMIN_URL?>/includes/tinymce/tinymce.min.js"></script>
<div class="dashboard-sub-menu-sec">
    <div class="container">
        <div class="sub-menu-sec">
            <?php require( '../includes/trade_sub_nav.php' );?>
        </div>
    </div>
</div>

<div class="latest_activities hd-grid activity_log">
    <div class="container">
        <div class="heading_sec">
            <h2><bold>ADD A</bold> VENDOR UPDATE</h2>
        </div>
        <div class="add-new-entry-sec">
            <button type="button" id="cancel" name="cancel" class="btn btn-primary back-btn" onclick="window.location.href = '<?php echo ADMIN_URL?>/trade_vendor_entries/edit.php?id=<?php echo $vid; ?>';">
                <img src="<?php echo ADMIN_URL?>/images/back-button.png" alt="back">
            </button>
        </div>
    </div>
</div>

<div class="main-form">
    <div class="container">
        <?php if ($msg) echo "<div class=\"alert alert-success\">$msg</div>"; ?>
        <form action="<?php echo ADMIN_URL?>/vendor_updates/add.php?sid=<?php echo $vid; ?>" role="form" method="POST" onSubmit="return validateForm();">
            <input type="hidden" name="insert" value="1">
            <input type="hidden" name="add_token" value="<?php echo $_SESSION['add_token']; ?>">
            <input type="hidden" name="vendor_id" id="vendor_id" value="<?php echo $vid; ?>">

            <input type="hidden" name="period_id" id="period_id" value="<?php echo $_SESSION['admin_period_id']; ?>">

            <div class="form-group text-box">
                <label for="html">Current Marketing Activities</label><br>
                <textarea id="current_marketing_activities" name="current_marketing_activities" class="form-control" rows="8"><?php echo ( $msg ) ? $_POST['current_marketing_activities'] : ''; ?></textarea>
            </div>

            <div class="form-group text-box">
                <label for="html">Upcoming Marketing Activities</label><br>
                <textarea id="upcoming_marketing_activities" name="upcoming_marketing_activities" class="form-control" rows="8"><?php echo ( $msg ) ? $_POST['upcoming_marketing_activities'] : ''; ?></textarea>
            </div>

            <div class="form-group text-box">
                <label for="html">Current Shopper Marketing Activities</label><br>
                <textarea id="current_shopper_marketing_activities" name="current_shopper_marketing_activities" class="form-control" rows="8"><?php echo ( $msg ) ? $_POST['current_shopper_marketing_activities'] : ''; ?></textarea>
            </div>

            <div class="form-group text-box">
                <label for="html">Upcoming Shopper Marketing Activities</label><br>
                <textarea id="upcoming_shopper_marketing_activiites" name="upcoming_shopper_marketing_activiites" class="form-control" rows="8"><?php echo ( $msg ) ? $_POST['upcoming_shopper_marketing_activiites'] : ''; ?></textarea>
            </div>

            <button type="submit">
                <img src="<?php echo ADMIN_URL?>/images/login-submit-btn.png" onmouseover="this.src='<?php echo ADMIN_URL?>/images/login-submit-hvr-btn.png'" onmouseout="this.src='<?php echo ADMIN_URL?>/images/login-submit-btn.png'" alt="login-submit-btn">
            </button>

            <button type="button" id="cancel" name="cancel" onClick="window.location.href = '<?php echo ADMIN_URL?>/trade_vendor_entries/edit.php?id=<?php echo $vid; ?>';">
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
<?php $conn->close(); ?>
<?php include('../includes/footer_new.php');?>
