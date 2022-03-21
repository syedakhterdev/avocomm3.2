<?php
$title =  'events';
require( '../config.php' );
require( '../includes/pdo.php' );
require( '../includes/check_login.php' );
require( '../includes/EventManager.php' );
$Event = new EventManager($conn);
$msg = '';

// check variables passed in through querystring
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$update = isset($_POST['update']) ? (int) $_POST['update'] : 0;

// if an id is passed in then let's fetch it from the database
if ($id) {
    if (!( $row = $Event->getByID($id) )) {
        $msg = "Sorry, a record with the specified ID could not be found!";
    }


    $del_image = ( isset($_GET['del_image']) ) ? (int) $_GET['del_image'] : 0;
    if ($del_image) {
        $Event->removeImage($id);
        $row['image'] = '';
    }
} else if ($update) { // if the user submitted a form....
    $token = $_SESSION['upd_token'];
    unset($_SESSION['upd_token']);

    if (!$msg && $token != '' && $token == $_POST['upd_token']) {
        if (!$Event->update($_POST['update'], $_POST['title'], $_POST['description'], $_POST['event_date'], 'image', $_POST['category_id'], $_POST['featured'], $_POST['active'])) {
            $msg = "Sorry, an error has occurred, please contact your administrator.<br>Error: " . $Event->error() . ";";
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

<div class="latest_activities hd-grid activity_log">
    <div class="container">
        <div class="heading_sec">
            <h2><bold>Edit AN</bold> Event</h2>
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
        <form enctype="multipart/form-data" action="<?php echo ADMIN_URL?>/events/edit.php" role="form" method="POST" onSubmit="return validateForm();" >
            <input type="hidden" name="update" value="<?php echo $row['id']; ?>">
            <input type="hidden" name="upd_token" value="<?php echo $_SESSION['upd_token']; ?>">

            <div class="form-group text-box">
                <label for="fname">Title *</label><br>
                <input type="text" id="title" name="title" onKeyUp="updateCountdown('#title', 65, '#title_lbl');" placeholder="" required onKeyDown="updateCountdown('#title', 65, '#title_lbl');" value="<?php echo ( $msg ) ? $_POST['title'] : $row['title']; ?>" maxlength="65">
                <span id="title_lbl" class="small"></span>
            </div>

            <div class="form-group text-box">
                <label for="html">Description *</label><br>
                <textarea id="description" name="description" rows="3"><?php echo ( $msg ) ? $_POST['description'] : $row['description']; ?></textarea>
            </div>

            <div class="form-group text-box">
                <label for="fname">Event Date *</label><br>
                <input type="date" id="event_date" name="event_date" placeholder="" required value="<?php echo ( $msg ) ? $_POST['event_date'] : $row['event_date']; ?>">
            </div>

            <?php if ($row['image'] != '') { ?>

                <div class="form-group text-box">
                    <label for="fname">Image *</label><br>
                    <div class="col-sm-12">
                        <img src="<?php echo ADMIN_URL?>/timThumb.php?src=<?php echo SITE_URL?>/assets/events/<?php echo $row['image']; ?>&w=200&h=80" style="border: 1px solid #CCCCCC;padding: 2px;margin: 4px;">
                        <br>
                        <a href="<?php echo ADMIN_URL?>/events/edit.php?id=<?php echo $id; ?>&del_image=1" class="btn action_btn cancel">Remove Image</a>
                    </div>

                </div>
            <?php } else { ?>

                <div class="form-group text-box">
                    <label for="fname">Image</label><br>
                    <input class="form-control file_upload" type="file" id="image" name="image">
                    <small id="lbl_image"></small>
                    <!--<input type="text" id="image" name="image" placeholder="Click to upload" onfocus="this.blur();" onclick="window.open('../includes/tinymce/plugins/filemanager/dialog.php?type=1&fldr=events&field_id=image&popup=1', '<?php /*echo time(); */?>', 'width=900,height=550,toolbar=0,menubar=0,location=0,status=1,scrollbars=1,resizable=1,left=0,top=0');return false;" >
                    <script>
                        function responsive_filemanager_callback(field_id) {
                            var url = jQuery('#' + field_id).val();
                            url = url.replace("https://<?php /*echo $_SERVER['HTTP_HOST']; */?>/assets/events/", '');
                            if ( url.length > 65 ) {
                                alert('The length of your file name is over the limit of 65 characthers. Please rename your file and try again.');
                                jQuery('#' + field_id).val('');
                            } else {
                                jQuery('#' + field_id).val(url);
                            }
                        }
                    </script>-->

                </div>
            <?php } ?>

            <div class="form-group text-box">
                <label for="fname">Category</label><br>
                <select name="category_id" id="category_id">
                    <option value="">Select an option...</option>
                    <?php echo $Event->getEvent_categories_CategoryDropdown(( $msg ) ? $_POST['category_id'] : $row['category_id'] ); ?>
                </select>
            </div>

            <div class="form-group checkbox-wrap">
                <label for="fname">Featured</label><br>
                <div class="checkbox-inner">
                    <div>
                        <input name="featured" id="featured" type="checkbox" value="1" <?php if (isset($_POST['featured']) || (int) $row['featured']) echo "CHECKED"; ?>>
                        <label for="html">Feature</label>
                    </div>
                </div>
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
        if ($('#image').val()!= ''){
            var ext = $('#image').val().split('.').pop().toLowerCase();
            if($.inArray(ext, ['gif','png','jpg','jpeg']) == -1) {

                $('#file_error').text('File should be gif,png,jpg extension')
                return createError('image', 'File should be gif,png,jpg extension');
            }

        }
        return true;
    }
    function createError(field, caption) {
        $('#lbl_' + field).addClass('error');
        $('#lbl_' + field).html(caption);
        $('#' + field).focus();
        return false;
    }
    function hasHtml5Validation() {
        return typeof document.createElement('input').checkValidity === 'function';
    }
</script>
<?php $conn->close(); ?>
<?php include('../includes/footer_new.php');?>
