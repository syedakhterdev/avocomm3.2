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

$insert = isset($_POST['insert']) ? (int) $_POST['insert'] : 0;
$sid = (int) $_GET['sid'];

if ($insert) {
    $token = $_SESSION['add_token'];
    unset($_SESSION['add_token']);

    if (!$msg) {
        if ($token != '' && $token == $_POST['add_token']) {
            if (!$id = $ShopperDocumentation->add($sid, $_SESSION['admin_period_id'], $_POST['title'], $_POST['description'], 'document', $_POST['document_type_id'], $_POST['active'])) {
                $msg = "Sorry, an error has occurred, please contact your administrator.<br>Error: " . $ShopperDocumentation->error() . ";";
            } else {
              /*if ( (int)$_POST['document_type_id'] == 1 && isset( $_POST['document'] ) ) { // if the document type is an image create a thumbnail
                $ext = pathinfo( $_POST['document'], PATHINFO_EXTENSION );
                if ( $_POST['image'] == '' && in_array( $ext, array( 'jpg', 'jpeg', 'png', 'gif' ) ) ) {
                  $ShopperDocumentation->makeThumbnail( $id, $_SERVER['DOCUMENT_ROOT'] . "/assets/shopper_documentation_docs/", $_POST['document'], $_SERVER['DOCUMENT_ROOT'] . "/assets/shopper_documentation/" );
                  $sql = 'UPDATE shopper_documentation SET image = ? WHERE id = ?';
                  $conn->exec( $sql, array( $_POST['document'], $id ) );
                }
              }*/

                if ($sid)
                    header("Location: ../shopper_program_entries/edit.php?add=1&id=$sid");
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
                <h2><bold>ADD A</bold> SHOPPER DOCUMENTATION</h2>
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
            <form enctype="multipart/form-data" action="<?php echo ADMIN_URL?>/shopper_documentation/add.php?sid=<?php echo $sid; ?>" role="form" method="POST" onSubmit="return validateForm();" >
                <input type="hidden" name="insert" value="1">
                <input type="hidden" name="add_token" value="<?php echo $_SESSION['add_token']; ?>">
                <input type="hidden" name="shopper_program_id" value="<?php echo $sid; ?>">

                <div class="form-group text-box">
                    <label for="fname">Document Type</label><br>
                    <select name="document_type_id" id="document_type_id" required>
                        <option value="">Select an option...</option>
                        <?php echo $ShopperDocumentation->getDocument_types_Document_typeDropdown(( $msg ) ? $_POST['document_type_id'] : 0 ); ?>
                    </select>
                </div>

                <div class="form-group text-box">
                    <label for="fname">Title *</label><br>
                    <input type="text" id="title" name="title" onKeyUp="updateCountdown('#title', 65, '#title_lbl');" placeholder="" required onKeyDown="updateCountdown('#title', 65, '#title_lbl');" value="<?php echo ( $msg ) ? $_POST['title'] : ''; ?>" maxlength="65">
                    <span id="lbl_title" class="small"></span>
                </div>

                <div class="form-group text-box">
                    <label for="html">Description *</label><br>
                    <textarea name="description" id="description" rows="3" onkeyup="updateCountdown('#description, 255, '#description_lbl' );" onkeyDown="updateCountdown('#description', 255, '#description_lbl');" placeholder="" required><?php echo htmlspecialchars(( $msg ) ? $_POST['description'] : '' ); ?></textarea>
                    <span id="lbl_description" class="small"></span>
                </div>

                <div class="form-group text-box">
                    <label for="fname">Document</label><br>
                    <input class="form-control file_upload" type="file" id="document" name="document">
                    <!--<input type="text" id="document" name="document" placeholder="Click to upload" onfocus="this.blur();" onclick="window.open('../includes/tinymce/plugins/filemanager/dialog.php?type=2&fldr=shopper_documentation_docs&field_id=document&popup=1', '<?php /*echo time(); */?>', 'width=900,height=550,toolbar=0,menubar=0,location=0,status=1,scrollbars=1,resizable=1,left=0,top=0'); return false;" maxlength="65">-->
                    <small id="lbl_document"></small>
                </div>

               <!-- <div class="form-group text-box">
                    <label for="fname">Preview Image</label><br>
                    <input type="text" id="image" name="image" placeholder="Click to upload" onfocus="this.blur();" onclick="window.open('../includes/tinymce/plugins/filemanager/dialog.php?type=1&fldr=shopper_documentation&field_id=image&popup=1', '<?php /*echo time(); */?>', 'width=900,height=550,toolbar=0,menubar=0,location=0,status=1,scrollbars=1,resizable=1,left=0,top=0'); return false;" maxlength="65">
                    <small>Please keep file names under 65 characters.</small>
                </div>-->

                <!--<script>
                    function responsive_filemanager_callback(field_id) {
                        var url = jQuery('#' + field_id).val();
                        url = url.replace("https://<?php /*echo $_SERVER['HTTP_HOST']; */?>/assets/shopper_documentation_docs/", '');
                        url = url.replace("https://<?php /*echo $_SERVER['HTTP_HOST']; */?>/assets/shopper_documentation/", '');
                        if ( url.length > 65 ) {
                            alert('The length of your file name is over the limit of 65 characthers. Please rename your file and try again.');
                            jQuery('#' + field_id).val('');
                        } else {
                            jQuery('#' + field_id).val(url);
                        }
                    }
                </script>-->

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
    <script type="text/javascript" src="../includes/tinymce/tinymce.min.js"></script>
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

            var type_id  =   $('#document_type_id').val();

            if ($('#title').val() == '')
                return createError('title', 'Please enter a valid title');
            if ($('#description').val() == '')
                return createError('description', 'Please enter a valid description');
            if ($('#image').val() == '')
                return createError('image', 'Please enter a valid image');
            if ($('#document').val()!= ''){
                var ext = $('#document').val().split('.').pop().toLowerCase();
                if(type_id==1){

                    if($.inArray(ext, ['gif','png','jpg','jpeg']) == -1) {

                        $('#file_error').text('File should be gif,png,jpg extension')
                        return createError('document', 'File should be gif,png,jpg extension');
                    }

                }else if(type_id==2){

                    if($.inArray(ext, ['doc','docx','pdf','xls','xlsx','ppt','pptx','txt']) == -1) {

                        $('#file_error').text('File should be doc,docx,pdf,xls,xlsx,ppt,pptx,txt extension')
                        return createError('document', 'File should be doc,docx,pdf,xls,xlsx,ppt,pptx,txt extension');
                    }

                }else if(type_id==3){

                    if($.inArray(ext, ['mp4']) == -1) {

                        $('#file_error').text('File should be mp4 extension')
                        return createError('document', 'File should be mp4 extension');
                    }

                }else if(type_id==4){

                    if($.inArray(ext, ['mp3']) == -1) {

                        $('#file_error').text('File should be mp3 extension')
                        return createError('document', 'File should be mp3 extension');
                    }

                }


            }else if($('#document').val()== ''){
                return createError('document', 'Please enter a valid document');
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