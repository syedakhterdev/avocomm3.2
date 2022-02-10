<?php
$title =  'reports';
require( '../config.php' );
require( '../includes/pdo.php' );
require( '../includes/check_login.php' );
if (!(int) $_SESSION['admin_permission_reports'])
    header('Location: /manager/menu.php');
require( '../includes/ReportManager.php' );
$Report = new ReportManager($conn);
$msg = '';
$insert = isset($_POST['insert']) ? (int) $_POST['insert'] : 0;

if ($insert) {
    $token = $_SESSION['add_token'];
    unset($_SESSION['add_token']);

    if (!$msg) {
        if ($token != '' && $token == $_POST['add_token']) {
            if (!$id = $Report->add($_SESSION['admin_period_id'], $_POST['title'], $_POST['description'], $_POST['image'], $_POST['doc'], $_POST['sort'], $_POST['active'])) {
                $msg = "Sorry, an error has occurred, please contact your administrator.<br>Error: " . $Report->error() . ";";
            } else {
              if ( isset( $_POST['doc'] ) && $_POST['doc'] != '' ) { // if the document type is an image create a thumbnail
                $ext = pathinfo( $_POST['doc'], PATHINFO_EXTENSION );
                if ( $_POST['image'] == '' && in_array( $ext, array( 'jpg', 'jpeg', 'png', 'gif' ) ) ) {
                  $Report->makeThumbnail( $id, $_SERVER['DOCUMENT_ROOT'] . "/assets/report_docs/", $_POST['doc'], $_SERVER['DOCUMENT_ROOT'] . "/assets/reports/" );
                  $sql = 'UPDATE reports SET image = ? WHERE id = ?';
                  $conn->exec( $sql, array( $_POST['doc'], $id ) );
                }
              }

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
                <h2><bold>Add A</bold> Report</h2>
            </div>
            <div class="add-new-entry-sec">
                <button type="button" id="cancel" name="cancel" class="btn btn-primary back-btn" onclick="window.location.href = '<?php echo ADMIN_URL?>/reports/index.php';">
                    <img src="<?php echo ADMIN_URL?>/images/back-button.png" alt="back">
                </button>
            </div>
        </div>
    </div>

    <div class="main-form">
        <div class="container">
            <?php if ($msg) echo "<div class=\"alert alert-success\">$msg</div>"; ?>
            <form action="<?php echo ADMIN_URL?>/reports/add.php" role="form" method="POST" onSubmit="return validateForm();" >
                <input type="hidden" name="insert" value="1">
                <input type="hidden" name="add_token" value="<?php echo $_SESSION['add_token']; ?>">

                <div class="form-group text-box">
                    <label for="fname">Title *</label><br>
                    <input type="text" id="title" name="title" onKeyUp="updateCountdown('#title', 85, '#title_lbl');" placeholder="" required onKeyDown="updateCountdown('#title', 85, '#title_lbl');" value="<?php echo ( $msg ) ? $_POST['title'] : ''; ?>" maxlength="85">
                    <span id="title_lbl" class="small"></span>
                </div>

                <div class="form-group text-box">
                    <label for="html">Description *</label><br>
                    <textarea id="description" name="description" required rows="3" required onKeyUp="updateCountdown('#description', 255, '#description_lbl');" onKeyDown="updateCountdown('#description', 255, '#description_lbl');" maxlength="255"><?php echo ( $msg ) ? $_POST['description'] : ''; ?></textarea>
                    <span id="description_lbl" class="small"></span>
                </div>

                <div class="form-group text-box">
                    <label for="fname">Image *</label><br>
                    <input type="text" id="image" name="image" placeholder="Click to upload" onfocus="this.blur();" onclick="window.open('<?php echo ADMIN_URL?>/includes/tinymce/plugins/filemanager/dialog.php?type=1&fldr=reports&field_id=image&popup=1', '<?php echo time(); ?>', 'width=900,height=550,toolbar=0,menubar=0,location=0,status=1,scrollbars=1,resizable=1,left=0,top=0');
                            return false;" >
                    <small>Recommended dimensions 160px wide by 146px tall</small>
                </div>

                <div class="form-group text-box">
                    <label for="fname">Document</label><br>
                    <input type="text" class="form-control" id="doc" name="doc" placeholder="Click to upload" onfocus="this.blur();" onclick="window.open('<?php echo ADMIN_URL?>/includes/tinymce/plugins/filemanager/dialog.php?type=2&fldr=report_docs&field_id=doc&popup=1', '<?php echo time(); ?>', 'width=900,height=550,toolbar=0,menubar=0,location=0,status=1,scrollbars=1,resizable=1,left=0,top=0');return false;" >
                </div>

                <script>
                    function responsive_filemanager_callback(field_id) {
                        var url = jQuery('#' + field_id).val();
                        url = url.replace("https://<?php echo $_SERVER['HTTP_HOST']; ?>/assets/reports/", '');
                        url = url.replace("https://<?php echo $_SERVER['HTTP_HOST']; ?>/assets/report_docs/", '');

                        if ( url.length > 65 )
                            alert( 'The file name you have selected is too long. Please rename the file and try again.' );
                        else
                            jQuery('#' + field_id).val(url);
                    }
                </script>


                <div class="form-group text-box">
                    <label for="fname">Sort *</label><br>
                    <select name="sort" id="sort" required>
                        <option value="">Select an option...</option>
                        <?php echo $Report->getRangeDropDown(1, 20, ( $msg ) ? $_POST['sort'] : 0 ); ?>
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

                <button type="button" id="cancel" name="cancel" onClick="window.location.href = '<?php echo ADMIN_URL?>/reports/index.php';">
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
            updateCountdown('#description', 85, '#description_lbl');
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
            }

            $('#submit').click(function () {
                if (!hasHtml5Validation())
                    return validateForm();
            });
        });

        function validateForm() {

            if ($('#title').val() == '')
                return createError('title', 'Please enter a valid title');
            return true;
        }

        function hasHtml5Validation() {
            return typeof document.createElement('input').checkValidity === 'function';
        }
    </script>

<?php $conn->close(); ?>
<?php include('../includes/footer_new.php');?>