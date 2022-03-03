<?php
$title =  'shoppers';
$subtitle = 'shopper_entries';
require( '../config.php' );

require( '../includes/pdo.php' );
require( '../includes/check_login.php' );
require( '../includes/ShopperProgramUpdateManager.php' );
$ShopperProgramUpdate = new ShopperProgramUpdateManager($conn);
$msg = '';

// check variables passed in through querystring
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$update = isset($_POST['update']) ? (int) $_POST['update'] : 0;
$sid = (int) $_GET['sid'];

// if an id is passed in then let's fetch it from the database
if ($id) {
    if (!( $row = $ShopperProgramUpdate->getByID($sid, $_SESSION['admin_period_id'], $id) )) {
        $msg = "Sorry, a record with the specified ID could not be found!";
    }
} else if ($update) { // if the user submitted a form....
    $token = $_SESSION['upd_token'];
    unset($_SESSION['upd_token']);

    if (!$msg && $token != '' && $token == $_POST['upd_token']) {
        if (!$ShopperProgramUpdate->update($_POST['update'], $sid, $_SESSION['admin_period_id'], $_POST['description'], $_POST['updates'])) {
            $msg = "Sorry, an error has occurred, please contact your administrator.<br>Error: " . $ShopperProgramUpdate->error() . ";";
        } else {
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
            <h2><bold>Edit a</bold> SHOPPER PROGRAM UPDATE</h2>
        </div>
        <div class="add-new-entry-sec">
            <button type="button" id="cancel" name="cancel" class="btn btn-primary back-btn" onclick="window.location.href = '<?php echo ADMIN_URL?>/shopper_program_entries/edit.php?id=<?php echo $sid; ?>';">
                <img src="<?php echo ADMIN_URL?>/images/back-button.png" alt="back">
            </button>
        </div>
    </div>
</div>

<div class="main-form">
    <div class="container">
        <?php if ($msg) echo "<div class=\"alert alert-success\">$msg</div>"; ?>
        <form action="<?php echo ADMIN_URL?>/shopper_program_updates/edit.php?sid=<?php echo $sid; ?>" role="form" method="POST" onSubmit="return validateForm();">
            <input type="hidden" name="update" value="<?php echo $row['id']; ?>">
            <input type="hidden" name="upd_token" value="<?php echo $_SESSION['upd_token']; ?>">


            <div class="form-group text-box">
                <label for="html">Description *</label><br>
                <textarea id="description" name="description" rows="3"><?php echo ( $msg ) ? $_POST['description'] : $row['description']; ?></textarea>
            </div>

            <div class="form-group text-box">
                <label for="html">Updates *</label><br>
                <textarea id="updates" name="updates" rows="3"><?php echo ( $msg ) ? $_POST['updates'] : $row['updates']; ?></textarea>
            </div>

            <button type="submit">
                <img src="<?php echo ADMIN_URL?>/images/login-submit-btn.png" onmouseover="this.src='<?php echo ADMIN_URL?>/images/login-submit-hvr-btn.png'" onmouseout="this.src='<?php echo ADMIN_URL?>/images/login-submit-btn.png'" alt="login-submit-btn">
            </button>

            <button type="button" id="cancel" name="cancel" onClick="window.location.href = '<?php echo ADMIN_URL?>/shopper_program_entries/edit.php?id=<?php echo $sid; ?>';">
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

<?php $conn->close(); ?>
<?php include('../includes/footer_new.php');?>