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

// check variables passed in through querystring
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$update = isset($_POST['update']) ? (int) $_POST['update'] : 0;

// if an id is passed in then let's fetch it from the database
if ($id) {
    if (!( $row = $Report->getByID($_SESSION['admin_period_id'], $id) )) {
        $msg = "Sorry, a record with the specified ID could not be found!";
    }

    $del_image = ( isset($_GET['del_image']) ) ? (int) $_GET['del_image'] : 0;
    if ($del_image) {
        $Report->removeImage($id);
        $row['image'] = '';
    }

    $del_doc = ( isset($_GET['del_doc']) ) ? (int) $_GET['del_doc'] : 0;
    if ($del_doc) {
        $Report->removeDocument($id);
        $row['doc'] = '';
    }

} else if ($update) { // if the user submitted a form....
    $token = $_SESSION['upd_token'];
    unset($_SESSION['upd_token']);

    if (!$msg && $token != '' && $token == $_POST['upd_token']) {
        if (!$Report->update($_POST['update'], $_SESSION['admin_period_id'], $_POST['title'], $_POST['description'], $_POST['image'], $_POST['doc'], $_POST['sort'], $_POST['active'])) {
            $msg = "Sorry, an error has occurred, please contact your administrator.<br>Error: " . $Report->error() . ";";
        } else {
          if ( $_POST['old_doc'] != $_POST['doc'] && ( $_POST['doc'] != '' || $_POST['image'] == '' ) ) { // if the document has changed, and it's an image, and no preview image was specified
            if ( isset( $_POST['doc'] ) && $_POST['doc'] != '' ) {
              $document = $_POST['doc'];
            } else if ( !isset( $_POST['doc'] ) && $_POST['old_doc'] != '' ) {
              $document = $_POST['old_doc'];
            }
            $ext = pathinfo( $document, PATHINFO_EXTENSION );
            if ( (int)$_POST['document_type_id'] == 1 && in_array( $ext, array( 'jpg', 'jpeg', 'png', 'gif' ) ) ) { // if the document type is an image create a thumbnail
              $Report->makeThumbnail( $update, $_SERVER['DOCUMENT_ROOT'] . "/assets/report_docs/", $document, $_SERVER['DOCUMENT_ROOT'] . "/assets/reports/" );
              $sql = 'UPDATE reports SET image = ? WHERE id = ?';
              $conn->exec( $sql, array( $document, $update ) );
            }
          }

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
                <h2><bold>Edit A</bold> Report</h2>
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
            <form action="<?php echo ADMIN_URL?>/reports/edit.php" role="form" method="POST" onSubmit="return validateForm();" >
                <input type="hidden" name="update" value="<?php echo $row['id']; ?>">
                <input type="hidden" name="upd_token" value="<?php echo $_SESSION['upd_token']; ?>">
                <input type="hidden" name="old_doc" value="<?php echo $row['doc']; ?>">

                <div class="form-group text-box">
                    <label for="fname">Title *</label><br>
                    <input type="text" id="title" name="title" onKeyUp="updateCountdown('#title', 85, '#title_lbl');" placeholder="" required onKeyDown="updateCountdown('#title', 85, '#title_lbl');" value="<?php echo ( $msg ) ? $_POST['title'] : $row['title']; ?>" maxlength="85">
                    <span id="title_lbl" class="small"></span>
                </div>

                <?php if ($row['doc'] != '') { ?>

                    <div class="form-group text-box">
                        <label for="fname">Document</label><br>
                        <?php
                        $ext = pathinfo( $row['doc'], PATHINFO_EXTENSION );
                        $icon = $ext ? "ico_$ext.png" : "ico_file.png";
                        ?>
                        <a href="/assets/report_docs/<?php echo $row['doc']; ?>" download>
                            <img src="<?php echo ADMIN_URL?>/timThumb.php?src=/assets/reports/<?php echo $row['image']; ?>&w=200&h=80" style="border: 1px solid #CCCCCC;padding: 2px;margin: 4px;">
                        </a><br>
                        <a href="edit.php?id=<?php echo $id; ?>&del_doc=1" class="btn action_btn cancel">Remove Document</a>
                    </div>
                <?php } else { ?>
                    <div class="form-group text-box">
                        <label for="fname">Document</label><br>
                        <input type="text" id="doc" name="doc" placeholder="Click to upload" onfocus="this.blur();" onclick="window.open('<?php echo ADMIN_URL?>/includes/tinymce/plugins/filemanager/dialog.php?type=2&fldr=report_docs&field_id=doc&popup=1', '<?php echo time(); ?>', 'width=900,height=550,toolbar=0,menubar=0,location=0,status=1,scrollbars=1,resizable=1,left=0,top=0');return false;" >
                    </div>
                <?php } ?>


                <div class="form-group text-box">
                    <label for="fname">Sort *</label><br>
                    <select name="sort" id="sort" required>
                        <option value="">Select an option...</option>
                        <?php echo $Report->getRangeDropDown(1, 20, ( $msg ) ? $_POST['sort'] : $row['sort'] ); ?>
                    </select>
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
        }
        function hasHtml5Validation() {
            return typeof document.createElement('input').checkValidity === 'function';
        }
    </script>

<?php $conn->close(); ?>
<?php include('../includes/footer_new.php');?>