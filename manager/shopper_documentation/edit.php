<?php
$title =  'shoppers';
$subtitle = 'shopper_entries';
require( '../config.php' );

require( '../includes/pdo.php' );
require( '../includes/check_login.php' );
require( '../includes/ShopperDocumentationManager.php' );
$ShopperDocumentation = new ShopperDocumentationManager($conn);
$msg = '';
if ( !(int)$_SESSION['admin_permission_shopper_hub'] ) header( 'Location: ../menu.php' );

// check variables passed in through querystring
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$update = isset($_POST['update']) ? (int) $_POST['update'] : 0;
$sid = (int) $_GET['sid'];

// if an id is passed in then let's fetch it from the database
if ($id) {
    if (!( $row = $ShopperDocumentation->getByID($_SESSION['admin_period_id'], $id) )) {
        $msg = "Sorry, a record with the specified ID could not be found!";
    }

    $del_image = ( isset($_GET['del_image']) ) ? (int) $_GET['del_image'] : 0;
    if ($del_image) {
        $ShopperDocumentation->removeImage($id);
        $row['image'] = '';
    }
    $del_document = ( isset($_GET['del_document']) ) ? (int) $_GET['del_document'] : 0;
    if ($del_document) {
        $ShopperDocumentation->removeDocument($id);
        $row['document'] = '';
    }
} else if ($update) { // if the user submitted a form....
    $token = $_SESSION['upd_token'];
    unset($_SESSION['upd_token']);

    if (!$msg && $token != '' && $token == $_POST['upd_token']) {
        if (!$ShopperDocumentation->update($_POST['update'], $sid, $_SESSION['admin_period_id'], $_POST['title'], $_POST['description'], $_POST['image'], $_POST['document'], $_POST['document_type_id'], $_POST['active'])) {
            $msg = "Sorry, an error has occurred, please contact your administrator.<br>Error: " . $ShopperDocumentation->error() . ";";
        } else {
          if ( $_POST['old_document'] != $_POST['document'] && ( $_POST['document'] != '' || $_POST['image'] == '' ) ) { // if the document has changed, and it's an image, and no preview image was specified
            if ( isset( $_POST['document'] ) && $_POST['document'] != '' ) {
              $document = $_POST['document'];
            } else if ( !isset( $_POST['document'] ) && $_POST['old_document'] != '' ) {
              $document = $_POST['old_document'];
            }
            $ext = pathinfo( $document, PATHINFO_EXTENSION );
            if ( (int)$_POST['document_type_id'] == 1 && in_array( $ext, array( 'jpg', 'jpeg', 'png', 'gif' ) ) ) { // if the document type is an image create a thumbnail
              $ShopperDocumentation->makeThumbnail( $update, $_SERVER['DOCUMENT_ROOT'] . "/assets/shopper_documentation_docs/", $document, $_SERVER['DOCUMENT_ROOT'] . "/assets/shopper_documentation/" );
              $sql = 'UPDATE shopper_documentation SET image = ? WHERE id = ?';
              $conn->exec( $sql, array( $document, $update ) );
            }
          }

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
                <h2><bold>Edit A</bold> SHOPPER DOCUMENTATION</h2>
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
            <form action="<?php echo ADMIN_URL?>/shopper_documentation/edit.php?sid=<?php echo $sid; ?>" role="form" method="POST" onSubmit="return validateForm();" >
                <input type="hidden" name="update" value="<?php echo $row['id']; ?>">
                <input type="hidden" name="upd_token" value="<?php echo $_SESSION['upd_token']; ?>">
                <input type="hidden" name="old_document" value="<?php echo $row['document']; ?>">

                <div class="form-group text-box">
                    <label for="fname">Document Type</label><br>
                    <select name="document_type_id" id="document_type_id" required>
                        <option value="">Select an option...</option>
                        <?php echo $ShopperDocumentation->getDocument_types_Document_typeDropdown(( $msg ) ? $_POST['document_type_id'] : $row['document_type_id'] ); ?>
                    </select>
                </div>

                <div class="form-group text-box">
                    <label for="fname">Title *</label><br>
                    <input type="text" id="title" name="title" onKeyUp="updateCountdown('#title', 65, '#title_lbl');" placeholder="" required onKeyDown="updateCountdown('#title', 65, '#title_lbl');" value="<?php echo ( $msg ) ? $_POST['title'] : $row['title']; ?>" maxlength="65">
                    <span id="title_lbl" class="small"></span>
                </div>

                <div class="form-group text-box">
                    <label for="html">Description *</label><br>
                    <textarea name="description" id="description" rows="3" onkeyup="updateCountdown('#description, 255, '#description_lbl' );" onkeyDown="updateCountdown('#description', 255, '#description_lbl');" placeholder="" required><?php echo htmlspecialchars(( $msg ) ? $_POST['description'] : $row['description'] ); ?></textarea>
                    <span id="description_lbl" class="small"></span>
                </div>

                <?php if ($row['document'] != '') { ?>

                    <div class="form-group text-box">
                        <label for="fname">Document</label><br>
                        <?php
                        $ext = pathinfo( $row['document'], PATHINFO_EXTENSION );
                        $icon = $ext ? "ico_$ext.png" : "ico_file.png";
                        ?>
                        <a href="<?php echo SITE_URL?>/assets/shopper_documentation_docs/<?php echo $row['document']; ?>" download><img src="<?php echo SITE_URL?>/assets/icons/<?php echo $icon; ?>" width="50" height="66" style="border: 1px solid #CCCCCC;padding: 2px;margin: 4px;">
                            <br>
                            <a href="<?php echo ADMIN_URL?>/shopper_documentation/edit.php?id=<?php echo $id; ?>&sid=<?php echo $sid; ?>&del_document=1" class="btn action_btn cancel">Remove Document</a>

                    </div>
                <?php } else { ?>
                    <div class="form-group text-box">
                        <label for="fname">Document</label><br>
                        <input type="text" id="document" name="document" placeholder="Click to upload" onfocus="this.blur();" onclick="window.open('../includes/tinymce/plugins/filemanager/dialog.php?type=2&fldr=shopper_documentation_docs&field_id=document&popup=1', '<?php echo time(); ?>', 'width=900,height=550,toolbar=0,menubar=0,location=0,status=1,scrollbars=1,resizable=1,left=0,top=0'); return false;" maxlength="65">
                        <small>Please keep file names under 65 characters.</small>
                    </div>
                <?php } ?>

                <?php if ($row['image'] != '') { ?>

                    <div class="form-group text-box">
                        <label for="fname">Preview Image</label><br>

                        <img src="<?php echo ADMIN_URL?>/timThumb.php?src=/assets/shopper_documentation/<?php echo $row['image']; ?>&w=200&h=80" style="border: 1px solid #CCCCCC;padding: 2px;margin: 4px;">
                        <br>
                        <a href="<?php echo ADMIN_URL?>/shopper_documentation/edit.php?id=<?php echo $id; ?>&sid=<?php echo $sid; ?>&del_image=1" class="btn action_btn cancel float-left">Remove Image</a>
                        <div class="clearfix"></div>
                        <small><?php echo $row['image']; ?></small>
                    </div>
                <?php } else { ?>
                    <div class="form-group text-box">
                        <label for="fname">Preview Image</label><br>
                        <input type="text" id="image" name="image" placeholder="Click to upload" onfocus="this.blur();" onclick="window.open('../includes/tinymce/plugins/filemanager/dialog.php?type=1&fldr=shopper_documentation&field_id=image&popup=1', '<?php echo time(); ?>', 'width=900,height=550,toolbar=0,menubar=0,location=0,status=1,scrollbars=1,resizable=1,left=0,top=0'); return false;" maxlength="65">
                        <small>Please keep file names under 65 characters.</small>
                    </div>
                <?php } ?>


                <script>
                    function responsive_filemanager_callback(field_id) {
                        var url = jQuery('#' + field_id).val();
                        url = url.replace("https://<?php echo $_SERVER['HTTP_HOST']; ?>/assets/shopper_documentation_docs/", '');
                        url = url.replace("https://<?php echo $_SERVER['HTTP_HOST']; ?>/assets/shopper_documentation/", '');
                        if ( url.length > 65 ) {
                            alert('The length of your file name is over the limit of 65 characters. Please rename your file and try again.');
                            jQuery('#' + field_id).val('');
                        } else {
                            jQuery('#' + field_id).val(url);
                        }
                    }
                </script>

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
    <script type="text/javascript">
        $().ready(function () {
            updateCountdown('#title', 65, '#title_lbl');
            updateCountdown('#description', 255, '#description_lbl');
        });
        function updateCountdown(input, limit, lbl) {
            var remaining = limit - $(input).val().length;
            $(lbl).text(remaining + ' characters remaining.');
        }
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
            if ($('#description').val() == '')
                return createError('description', 'Please enter a valid description');
            if ($('#image').val() == '')
                return createError('image', 'Please enter a valid image');
            if ($('#document').val() == '')
                return createError('document', 'Please enter a valid document');
        }
        function hasHtml5Validation() {
            return typeof document.createElement('input').checkValidity === 'function';
        }
    </script>

<?php $conn->close(); ?>
<?php include('../includes/footer_new.php');?>