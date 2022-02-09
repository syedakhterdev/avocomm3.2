<?php
$title =  'administrative';
$subtitle = 'events';
require( '../config.php' );
require( '../includes/pdo.php' );
require( '../includes/check_login.php' );
require( '../includes/EventCategoryManager.php' );
$EventCategory = new EventCategoryManager($conn);
$msg = '';

// check variables passed in through querystring
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$update = isset($_POST['update']) ? (int) $_POST['update'] : 0;

// if an id is passed in then let's fetch it from the database
if ($id) {
    if (!( $row = $EventCategory->getByID($id) )) {
        $msg = "Sorry, a record with the specified ID could not be found!";
    }
} else if ($update) { // if the user submitted a form....
    $token = $_SESSION['upd_token'];
    unset($_SESSION['upd_token']);

    if (!$msg && $token != '' && $token == $_POST['upd_token']) {
        if (!$EventCategory->update($_POST['update'], $_POST['category'])) {
            $msg = "Sorry, an error has occurred, please contact your administrator.<br>Error: " . $EventCategory->error() . ";";
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
    <div class="dashboard-sub-menu-sec">
        <div class="container">
            <div class="sub-menu-sec">
                <?php include('../includes/administrative_sub_nav.php')?>
            </div>
        </div>
    </div>

    <div class="latest_activities hd-grid activity_log">
        <div class="container">
            <div class="heading_sec">
                <h2><bold>Edit AN</bold> Event Category</h2>
            </div>
            <div class="add-new-entry-sec">
                <form>
                    <button type="button" id="cancel" name="cancel" class="btn btn-primary" onclick="window.location.href = '<?php echo ADMIN_URL?>/event_categories/index.php';">Back</button>
                </form>
            </div>
        </div>
    </div>

    <div class="main-form">
        <div class="container">
            <?php if ($msg) echo "<div class=\"alert alert-success\">$msg</div>"; ?>
            <form action="<?php echo ADMIN_URL?>/event_categories/edit.php" role="form" method="POST" onSubmit="return validateForm();">
                <input type="hidden" name="update" value="<?php echo $row['id']; ?>">
                <input type="hidden" name="upd_token" value="<?php echo $_SESSION['upd_token']; ?>">

                <div class="form-group text-box">
                    <label for="fname">Category*</label><br>
                    <input type="text" id="category" name="category" onKeyUp="updateCountdown('#category', 65, '#category_lbl');" placeholder="" required onKeyDown="updateCountdown('#category', 65, '#category_lbl');" value="<?php echo ( $msg ) ? $_POST['category'] : $row['category']; ?>" maxlength="65">
                    <span id="category_lbl" class="small"></span>
                </div>

                <button type="submit">
                    <img src="<?php echo ADMIN_URL?>/images/login-submit-btn.png" onmouseover="this.src='<?php echo ADMIN_URL?>/images/login-submit-hvr-btn.png'" onmouseout="this.src='<?php echo ADMIN_URL?>/images/login-submit-btn.png'" alt="login-submit-btn">
                </button>

                <button type="button" id="cancel" name="cancel" onClick="window.location.href = '<?php echo ADMIN_URL?>/event_categories/index.php';">
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
            updateCountdown('#category', 65, '#category_lbl');
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

            if ($('#category').val() == '')
                return createError('category', 'Please enter a valid category');
        }
        function hasHtml5Validation() {
            return typeof document.createElement('input').checkValidity === 'function';
        }
    </script>

<?php $conn->close(); ?>
<?php include('../includes/footer_new.php');?>