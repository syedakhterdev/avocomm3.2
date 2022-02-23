<?php
$title =  'events';
require( '../config.php' );
require( '../includes/pdo.php' );
require( '../includes/check_login.php' );
require( '../includes/EventManager.php' );
$Event = new EventManager($conn);
$msg = '';

$insert = isset($_POST['insert']) ? (int) $_POST['insert'] : 0;


if ($insert) {
    $token = $_SESSION['add_token'];
    unset($_SESSION['add_token']);

    if (!$msg) {
        if ($token != '' && $token == $_POST['add_token']) {
            if (!$id = $Event->add($_POST['title'], $_POST['description'], $_POST['event_date'], $_POST['image'], $_POST['category_id'], $_POST['featured'], $_POST['active'])) {
                $msg = "Sorry, an error has occurred, please contact your administrator.<br>Error: " . $Event->error() . ";";
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

<div class="latest_activities hd-grid activity_log">
    <div class="container">
        <div class="heading_sec">
            <h2><bold>Add AN</bold> Event</h2>
        </div>
        <div class="add-new-entry-sec">
            <button type="button" id="cancel" name="cancel" class="btn btn-primary back-btn" onclick="window.location.href = '<?php echo ADMIN_URL?>/events/';">
                <img src="<?php echo ADMIN_URL?>/images/back-button.png" alt="back">
            </button>
        </div>
    </div>
</div>

<div class="main-form">
    <div class="container">
        <?php if ($msg) echo "<div class=\"alert alert-success\">$msg</div>"; ?>
        <form action="<?php echo ADMIN_URL?>/events/add.php" role="form" method="POST" onSubmit="return validateForm();" >
            <input type="hidden" name="insert" value="1">
            <input type="hidden" name="add_token" value="<?php echo $_SESSION['add_token']; ?>">

            <div class="form-group text-box">
                <label for="fname">Title *</label><br>
                <input type="text" id="title" name="title" onKeyUp="updateCountdown('#title', 65, '#title_lbl');" placeholder="" required onKeyDown="updateCountdown('#title', 65, '#title_lbl');" value="<?php echo ( $msg ) ? $_POST['title'] : ''; ?>" maxlength="65">
                <span id="title_lbl" class="small"></span>
            </div>

            <div class="form-group text-box">
                <label for="html">Description *</label><br>
                <textarea id="description" name="description" rows="3"><?php echo ( $msg ) ? $_POST['description'] : ''; ?></textarea>
            </div>

            <div class="form-group text-box">
                <label for="fname">Event Date *</label><br>
                <input type="date" id="event_date" name="event_date" placeholder="" required value="<?php echo ( $msg ) ? $_POST['event_date'] : ''; ?>">
            </div>

            <div class="form-group text-box">
                <label for="fname">Image</label><br>
                <input type="text" class="form-control" id="image" name="image" placeholder="Click to upload" onfocus="this.blur();" onclick="window.open('../includes/tinymce/plugins/filemanager/dialog.php?type=1&fldr=events&field_id=image&popup=1', '<?php echo time(); ?>', 'width=900,height=550,toolbar=0,menubar=0,location=0,status=1,scrollbars=1,resizable=1,left=0,top=0');return false;" >
            </div>

            <script>
                function responsive_filemanager_callback(field_id) {
                    var url = jQuery('#' + field_id).val();
                    url = url.replace("https://<?php echo $_SERVER['HTTP_HOST']; ?>/assets/events/", '');
                    if ( url.length > 65 ) {
                        alert('The length of your file name is over the limit of 65 characthers. Please rename your file and try again.');
                        jQuery('#' + field_id).val('');
                    } else {
                        jQuery('#' + field_id).val(url);
                    }
                }
            </script>



            <div class="form-group text-box">
                <label for="fname">Category</label><br>
                <select name="category_id" id="category_id" >
                    <option value="">Select an option...</option>
                    <?php echo $Event->getEvent_categories_CategoryDropdown(( $msg ) ? $_POST['category_id'] : 0 ); ?>
                </select>
            </div>

            <div class="form-group checkbox-wrap">
                <label for="fname">Featured</label><br>
                <div class="checkbox-inner">
                    <div>
                        <input type="checkbox" id="featured" name="featured" value="1"  <?php if (isset($_POST['featured']) && (int) $_POST['featured'] {DEFAULT_CHECKED}) echo "CHECKED"; ?>>
                        <label for="html">Feature</label>
                    </div>
                </div>
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

            <button type="button" id="cancel" name="cancel" onClick="window.location.href = '<?php echo ADMIN_URL?>/events/index.php';">
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
    tinymce.init({
        selector: "textarea#description",
        plugins: ["link image hr fullscreen media table textcolor code filemanager lists advlist"],
        toolbar: "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link code responsivefilemanager table | forecolor backcolor",
        external_filemanager_path: "/manager/includes/tinymce/plugins/filemanager/",
        filemanager_title: "File manager", relative_urls: false, image_advtab: true,
        external_plugins: {"filemanager": "/manager/includes/tinymce/plugins/filemanager/plugin.min.js"}
    });
</script>
<script type="text/javascript">
    $().ready(function () {
        updateCountdown('#title', 65, '#title_lbl');
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

            $('#event_date').datepicker();
        }

        $('#submit').click(function () {
            if (!hasHtml5Validation())
                return validateForm();
        });
    });

    function validateForm() {

        if ($('#title').val() == '')
            return createError('title', 'Please enter a valid title');
        if ($('#event_date').val() == '')
            return createError('event_date', 'Please enter a valid event date');
        return true;
    }

    function hasHtml5Validation() {
        return typeof document.createElement('input').checkValidity === 'function';
    }
</script>

<?php $conn->close(); ?>
<?php include('../includes/footer_new.php');?>
