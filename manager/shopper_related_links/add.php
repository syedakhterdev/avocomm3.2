<?php
$title =  'shoppers';
$subtitle = 'shopper_entries';
require( '../config.php' );

require( '../includes/pdo.php' );
require( '../includes/check_login.php' );
require( '../includes/ShopperRelatedLinkManager.php' );
$ShopperRelatedLink = new ShopperRelatedLinkManager($conn);
$msg = '';

$insert = isset($_POST['insert']) ? (int) $_POST['insert'] : 0;
$sid = (int) $_GET['sid'];

if ($insert) {
    $token = $_SESSION['add_token'];
    unset($_SESSION['add_token']);

    if (!$msg) {
        if ($token != '' && $token == $_POST['add_token']) {
            if (!$id = $ShopperRelatedLink->add($_SESSION['admin_period_id'], $sid, $_POST['title'], $_POST['description'], $_POST['image'], $_POST['url'], $_POST['sort'])) {
                $msg = "Sorry, an error has occurred, please contact your administrator.<br>Error: " . $ShopperRelatedLink->error() . ";";
            } else {
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
                <h2><bold>ADD A</bold> SHOPPER RELATED LINK</h2>
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
            <form action="<?php echo ADMIN_URL?>/shopper_related_links/add.php?sid=<?php echo $sid; ?>" role="form" method="POST" onSubmit="return validateForm();">
                <input type="hidden" name="insert" value="1">
                <input type="hidden" name="add_token" value="<?php echo $_SESSION['add_token']; ?>">


                <div class="form-group text-box">
                    <label for="fname">Url</label><br>
                    <input type="url" id="url" name="url" onKeyUp="updateCountdown('#url', 250, '#url_lbl');" placeholder=""  onKeyDown="updateCountdown('#url', 250, '#url_lbl');" value="<?php echo ( $msg ) ? $_POST['url'] : ''; ?>" maxlength="250" onChange="getURLContents( this.value );">
                    <span id="url_lbl" class="small"></span>
                </div>

                <div class="form-group text-box">
                    <label for="html">Title *</label><br>
                    <input type="text" id="title" name="title" onKeyUp="updateCountdown('#title', 85, '#title_lbl');" placeholder="" required onKeyDown="updateCountdown('#title', 85, '#title_lbl');" value="<?php echo ( $msg ) ? $_POST['title'] : ''; ?>" maxlength="85">
                    <span id="title_lbl" class="small"></span>
                </div>

                <div class="form-group text-box">
                    <label for="fname">Description *</label><br>
                    <textarea name="description" id="description" rows="3" onkeyup="updateCountdown('#description, 255, '#description_lbl' );" onkeyDown="updateCountdown('#description', 255, '#description_lbl');" placeholder="" required><?php echo htmlspecialchars(( $msg ) ? $_POST['description'] : '' ); ?></textarea>
                    <span id="description_lbl" class="small"></span>
                </div>

                <div class="form-group text-box">
                    <label for="fname">Image</label><br>
                    <input type="text" id="image" name="image" placeholder="Click to upload" onfocus="this.blur();" onclick="window.open('../includes/tinymce/plugins/filemanager/dialog.php?type=1&fldr=shopper_related_links&field_id=image&popup=1', '<?php echo time(); ?>', 'width=900,height=550,toolbar=0,menubar=0,location=0,status=1,scrollbars=1,resizable=1,left=0,top=0');return false;" >
                </div>

                <script>
                    function responsive_filemanager_callback(field_id) {
                        var url = jQuery('#' + field_id).val();
                        url = url.replace("https://<?php echo $_SERVER['HTTP_HOST']; ?>/assets/shopper_related_links/", '');
                        if ( url.length > 65 ) {
                            alert('The length of your file name is over the limit of 65 characthers. Please rename your file and try again.');
                            jQuery('#' + field_id).val('');
                        } else {
                            jQuery('#' + field_id).val(url);
                        }
                    }
                </script>

                <div class="form-group text-box">
                    <label for="fname">Sort *</label><br>
                    <select name="sort" id="sort" class="form-control" required>
                        <option value="">Select an option...</option>
                        <?php echo $ShopperRelatedLink->getRangeDropDown(1, 20, ( $msg ) ? $_POST['sort'] : 0 ); ?>
                    </select>
                </div>

                <button type="submit">
                    <img src="<?php echo ADMIN_URL?>/images/login-submit-btn.png" onmouseover="this.src='<?php echo ADMIN_URL?>/images/login-submit-hvr-btn.png'" onmouseout="this.src='<?php echo ADMIN_URL?>/images/login-submit-btn.png'" alt="login-submit-btn">
                </button>

                <button type="button" id="cancel" name="cancel" onClick="window.location.href = '<?php echo $sid ? ''.ADMIN_URL.'/shopper_programs/edit.php?id=' . $sid : 'index.php'; ?>';">
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
            updateCountdown('#description', 255, '#description_lbl');
            updateCountdown('#url', 250, '#url_lbl');
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
            return true;
        }

        function hasHtml5Validation() {
            return typeof document.createElement('input').checkValidity === 'function';
        }

        function getURLContents( url ) {
            console.log(url);
            if ( url != '' ) {
                $.ajax({
                    type: "GET", //rest Type
                    data: { url: url, dir: 'shopper_related_links' },
                    dataType: 'json', //mispelled
                    url: '../fetch_url_meta_tags.php',
                    contentType: "application/json; charset=utf-8",
                    success: function (data) {
                        console.log( data );
                        console.log( data.og_title );
                        if ( data.og_title == null ) {
                            alert( 'Could not find meta tags in the specified URL.' );
                        } else {
                            $('#title').val( data.og_title );
                            $('#description').val( data.og_description );
                            $('#image').val( data.og_image );
                        }
                    },
                    error: function(xhr){
                        alert('Request Status: ' + xhr.status + ' Status Text: ' + xhr.statusText + ' ' + xhr.responseText);
                    }
                });
            }
        }
    </script>

<?php $conn->close(); ?>
<?php include('../includes/footer_new.php');?>