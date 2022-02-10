<?php
$title =  'administrative';
$subtitle = 'events';
require( '../config.php' );
require( '../includes/pdo.php' );
require( '../includes/check_login.php' );
require( '../includes/EventCategoryManager.php' );
$EventCategory = new EventCategoryManager($conn);
$msg = '';

$insert = isset($_POST['insert']) ? (int) $_POST['insert'] : 0;


if ($insert) {
    $token = $_SESSION['add_token'];
    unset($_SESSION['add_token']);

    if (!$msg) {
        if ($token != '' && $token == $_POST['add_token']) {
            if (!$id = $EventCategory->add($_POST['category'])) {
                $msg = "Sorry, an error has occurred, please contact your administrator.<br>Error: " . $EventCategory->error() . ";";
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
                <h2><bold>Add AN</bold> Event Category</h2>
            </div>
            <div class="add-new-entry-sec">
                <button type="button" id="cancel" name="cancel" class="btn btn-primary back-btn" onclick="window.location.href = '<?php echo ADMIN_URL?>/event_categories/index.php';">
                    <img src="<?php echo ADMIN_URL?>/images/back-button.png" alt="back">
                </button>
            </div>
        </div>
    </div>

    <div class="main-form">
        <div class="container">
            <?php if ($msg) echo "<div class=\"alert alert-success\">$msg</div>"; ?>
            <form action="<?php echo ADMIN_URL?>/event_categories/add.php" role="form" method="POST" onSubmit="return validateForm();">
                <input type="hidden" name="insert" value="1">
                <input type="hidden" name="add_token" value="<?php echo $_SESSION['add_token']; ?>">

                <div class="form-group text-box">
                    <label for="fname">Category*</label><br>
                    <input type="text" id="category" name="category" onKeyUp="updateCountdown('#category', 65, '#category_lbl');" placeholder="" required onKeyDown="updateCountdown('#category', 65, '#category_lbl');" value="<?php echo ( $msg ) ? $_POST['category'] : ''; ?>" maxlength="65">
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
            return true;
        }

        function hasHtml5Validation() {
            return typeof document.createElement('input').checkValidity === 'function';
        }
    </script>

<?php $conn->close(); ?>
<?php include('../includes/footer_new.php');?>