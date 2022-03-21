<?php
$title =  'trades';
$subtitle = 'trade_entries';
require( '../config.php' );


require( '../includes/pdo.php' );
require( '../includes/check_login.php' );
require( '../includes/VendorDocumentationManager.php' );
$VendorDocumentation = new VendorDocumentationManager($conn);
$msg = '';

// check variables passed in through querystring
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$update = isset($_POST['update']) ? (int) $_POST['update'] : 0;
$vid = (int) $_GET['sid'];

// if an id is passed in then let's fetch it from the database
if ($id) {
    if (!( $row = $VendorDocumentation->getByID($id, $_SESSION['admin_period_id']) )) {
        $msg = "Sorry, a record with the specified ID could not be found!";
    }

    $del_image = ( isset($_GET['del_image']) ) ? (int) $_GET['del_image'] : 0;
    if ($del_image) {
        $VendorDocumentation->removeImage($id);
        $row['image'] = '';
    }
    $del_document = ( isset($_GET['del_document']) ) ? (int) $_GET['del_document'] : 0;
    if ($del_document) {
        $VendorDocumentation->removeDocument($id);
        $row['document'] = '';
    }
} else if ($update) { // if the user submitted a form....
    $token = $_SESSION['upd_token'];
    unset($_SESSION['upd_token']);

    if (!$msg && $token != '' && $token == $_POST['upd_token']) {
        if (!$VendorDocumentation->update($_POST['update'], $_POST['vendor_id'], $_SESSION['admin_period_id'], $_POST['title'], $_POST['description'], 'document', $_POST['documen_type_id'], $_POST['active'])) {
            $msg = "Sorry, an error has occurred, please contact your administrator.<br>Error: " . $VendorDocumentation->error() . ";";
        } else {

          /*if ( $_POST['old_document'] != $_POST['document'] && ( $_POST['document'] != '' || $_POST['image'] == '' ) ) { // if the document has changed, and it's an image, and no preview image was specified
            if ( isset( $_POST['document'] ) && $_POST['document'] != '' ) {
              $document = $_POST['document'];
            } else if ( !isset( $_POST['document'] ) && $_POST['old_document'] != '' ) {
              $document = $_POST['old_document'];
            }
            $ext = pathinfo( $document, PATHINFO_EXTENSION );
            if ( (int)$_POST['documen_type_id'] == 1 && in_array( $ext, array( 'jpg', 'jpeg', 'png', 'gif' ) ) ) { // if the document type is an image create a thumbnail
              $VendorDocumentation->makeThumbnail( $update, $_SERVER['DOCUMENT_ROOT'] . "/assets/documentation_docs/", $document, $_SERVER['DOCUMENT_ROOT'] . "/assets/documentation_images/" );
              $sql = 'UPDATE vendor_documentation SET image = ? WHERE id = ?';
              $conn->exec( $sql, array( $document, $update ) );
            }
          }*/

          if ($vid)
            header("Location: ../trade_vendor_entries/edit.php?upd=1&id=$vid");
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
                <h2><bold>Add a</bold> VENDOR DOCUMENTATION</h2>
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
            <form enctype="multipart/form-data" action="<?php echo ADMIN_URL?>/vendor_documentation/edit.php?sid=<?php echo $vid; ?>" role="form" method="POST" onSubmit="return validateForm();">
                <input type="hidden" name="update" value="<?php echo $row['id']; ?>">
                <input type="hidden" name="upd_token" value="<?php echo $_SESSION['upd_token']; ?>">
                <input type="hidden" name="old_document" value="<?php echo $row['document']; ?>">
                <input type="hidden" name="vendor_id" id="vendor_id" value="<?php echo $row['vendor_id']; ?>">

                <div class="form-group text-box">
                    <label for="fname">Document Type *</label><br>
                    <select name="documen_type_id" id="documen_type_id" required>
                        <option value="">Select an option...</option>
                        <?php echo $VendorDocumentation->getDocument_types_Document_typeDropdown(( $msg ) ? $_POST['documen_type_id'] : $row['documen_type_id'] ); ?>
                    </select>
                </div>

                <div class="form-group text-box">
                    <label for="fname">Title *</label><br>
                    <input type="text" id="title" name="title" onKeyUp="updateCountdown('#title', 65, '#title_lbl');" placeholder="" required onKeyDown="updateCountdown('#title', 65, '#title_lbl');" value="<?php echo ( $msg ) ? $_POST['title'] : $row['title']; ?>" maxlength="65">
                    <span id="lbl_title" class="small"></span>
                </div>

                <div class="form-group text-box">
                    <label for="html">Description *</label><br>
                    <textarea name="description" id="description" rows="3" onkeyup="updateCountdown('#description', 255, '#description_lbl' );" onkeyDown="updateCountdown('#description', 255, '#description_lbl');" placeholder="" required><?php echo htmlspecialchars(( $msg ) ? $_POST['description'] : $row['description'] ); ?></textarea>
                    <span id="lbl_description" class="small"></span>
                </div>

                <?php if ($row['document'] != '') { ?>

                    <div class="form-group text-box">
                        <label for="fname">Document</label><br>
                        <?php
                        $ext = pathinfo( $row['document'], PATHINFO_EXTENSION );
                        $icon = $ext ? "ico_$ext.png" : "ico_file.png";
                        ?>
                        <a href="/assets/documentation_docs/<?php echo $row['document']; ?>" download>
                            <img src="<?php echo ADMIN_URL?>/timThumb.php?src=/assets/documentation_images/<?php echo $row['image']; ?>&w=200&h=80" style="border: 1px solid #CCCCCC;padding: 2px;margin: 4px;">
                        </a>
                        <br>
                        <a href="edit.php?id=<?php echo $id; ?>&sid=<?php echo $vid; ?>&del_document=1&del_image=1" class="btn action_btn cancel">Remove Document</a>
                    </div>
                <?php } else { ?>
                    <div class="form-group text-box">
                        <label for="fname">Document</label><br>
                        <input class="form-control file_upload" type="file" id="document" name="document">
                        <small id="lbl_document"></small>
                        <!--<input type="text" id="doc" name="doc" placeholder="Click to upload" onfocus="this.blur();" onclick="window.open('<?php /*echo ADMIN_URL*/?>/includes/tinymce/plugins/filemanager/dialog.php?type=2&fldr=report_docs&field_id=doc&popup=1', '<?php /*echo time(); */?>', 'width=900,height=550,toolbar=0,menubar=0,location=0,status=1,scrollbars=1,resizable=1,left=0,top=0');return false;" >-->
                    </div>
                <?php } ?>



                <div class="form-group checkbox-wrap">
                    <label for="fname">Status</label><br>
                    <div class="checkbox-inner">
                        <div>
                            <input type="checkbox" id="active" name="active" value="1"  <?php if (isset($_POST['active']) || (int) $row['active']) echo "CHECKED"; ?>>
                            <label for="html">Active</label>
                        </div>
                    </div>
                </div>

                <button type="submit">
                    <img src="<?php echo ADMIN_URL?>/images/login-submit-btn.png" onmouseover="this.src='<?php echo ADMIN_URL?>/images/login-submit-hvr-btn.png'" onmouseout="this.src='<?php echo ADMIN_URL?>/images/login-submit-btn.png'" alt="login-submit-btn">
                </button>

                <button type="button" id="cancel" name="cancel" onClick="window.location.href = '<?php echo $vid ? ''.ADMIN_URL.'/trade_vendor_entries/edit.php?id=' . $vid : 'index.php'; ?>';">
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

            var type_id  =   $('#documen_type_id').val();

            if ($('#title').val() == '')
                return createError('title', 'Please enter a valid title');
            if ($('#description').val() == '')
                return createError('description', 'Please enter a valid description');
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