<?php
$title =  'administrative';
$subtitle = 'users';
require( '../config.php' );
require( '../includes/pdo.php' );
require( '../includes/check_login.php' );
require( '../includes/UserManager.php' );
require_once('../includes/EmailManager.php');
$User = new UserManager($conn);
$msg = '';

$insert = isset($_POST['insert']) ? (int) $_POST['insert'] : 0;
function getRandomString( $length = 8 ) {
    $characters = '0123456789';
    $charactersLength = strlen( $characters );
    $randomString = '';
    for ( $i = 0; $i < $length; $i++ ) {
        $randomString .= $characters[rand( 0, $charactersLength - 1 )];
    }
    return $randomString;
}

if ($insert) {
    $token = $_SESSION['add_token'];
    unset($_SESSION['add_token']);

    if (!$msg) {
        if ($token != '' && $token == $_POST['add_token']) {
            if (!$id = $User->add($_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['company'], $_POST['active'])) {
                $msg = "Sorry, an error has occurred, please contact your administrator.<br>Error: " . $User->error() . ";";
            } else {
                $code = getRandomString(8);
                $mail = new EmailManager(
                    $conn,
                    '',
                    2, // use template # 1
                    $_POST['email'],
                    stripslashes( $_POST['first_name'] . ' ' . $_POST['last_name'] ),
                    '',
                    '',
                    true,
                    [
                        'NAME' => stripslashes( $_POST['first_name'] . ' ' . $_POST['last_name'] ),
                        'HOST' => $_SERVER['HTTP_HOST'],
                        'VERIFY_LINK' => '<a href="https://' . $_SERVER['HTTP_HOST'] . '/set_password.php?code=' . $code . '&email=' . $_POST['email'] . '">Set Your Password</a>'
                    ]
                );
                $mail->send();

                $sql = 'UPDATE users SET set_password = ? WHERE id = ? LIMIT 1;';
                $conn->exec( $sql, array( $code, $id ) );

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
                <h2><bold>Add AN</bold> User</h2>
            </div>
            <div class="add-new-entry-sec">
                <form>
                    <button type="button" id="cancel" name="cancel" class="btn btn-primary" onclick="window.location.href = '<?php echo ADMIN_URL?>/users/index.php';">Back</button>
                </form>
            </div>
        </div>
    </div>

    <div class="main-form">
        <div class="container">
            <?php if ($msg) echo "<div class=\"alert alert-success\">$msg</div>"; ?>
            <form action="<?php echo ADMIN_URL?>/users/add.php" role="form" method="POST" onSubmit="return validateForm();" autocomplete="off" >
                <input type="hidden" name="insert" value="1">
                <input type="hidden" name="add_token" value="<?php echo $_SESSION['add_token']; ?>">

                <div class="form-group text-box">
                    <label for="fname">First Name</label><br>
                    <input type="text"  id="first_name" name="first_name" onKeyUp="updateCountdown('#first_name', 40, '#first_name_lbl');" placeholder="" required onKeyDown="updateCountdown('#first_name', 40, '#first_name_lbl');" value="<?php echo ( $msg ) ? $_POST['first_name'] : ''; ?>" maxlength="40">
                    <span id="first_name_lbl" class="small"></span>
                </div>


                <div class="form-group text-box">
                    <label for="fname">Last Name</label><br>
                    <input type="text" id="last_name" name="last_name" onKeyUp="updateCountdown('#last_name', 40, '#last_name_lbl');" placeholder="" required onKeyDown="updateCountdown('#last_name', 40, '#last_name_lbl');" value="<?php echo ( $msg ) ? $_POST['last_name'] : ''; ?>" maxlength="40">
                    <span id="last_name_lbl" class="small"></span>
                </div>


                <div class="form-group text-box">
                    <label for="fname">Email *</label><br>
                    <input type="email" id="email" name="email" onKeyUp="updateCountdown('#email', 120, '#email_lbl');" placeholder="" required onKeyDown="updateCountdown('#email', 120, '#email_lbl');" value="<?php echo ( $msg ) ? $_POST['email'] : ''; ?>" maxlength="120" autocomplete="off" />
                    <span id="email_lbl" class="small"></span>
                </div>

                <div class="form-group text-box">
                    <label for="fname">Company *</label><br>
                    <input type="text" id="company" name="company" onKeyUp="updateCountdown('#company', 80, '#company_lbl');" placeholder="" required onKeyDown="updateCountdown('#company', 80, '#company_lbl');" value="<?php echo ( $msg ) ? $_POST['company'] : ''; ?>" maxlength="80">
                    <span id="company_lbl" class="small"></span>
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

                <button type="button" id="cancel" name="cancel" onClick="window.location.href = '<?php echo ADMIN_URL?>/users/index.php';">
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
            updateCountdown('#first_name', 40, '#first_name_lbl');
            updateCountdown('#last_name', 40, '#last_name_lbl');
            updateCountdown('#email', 120, '#email_lbl');
            updateCountdown('#company', 80, '#company_lbl');
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

            if ($('#first_name').val() == '')
                return createError('first_name', 'Please enter a valid first name');
            if ($('#last_name').val() == '')
                return createError('last_name', 'Please enter a valid last name');
            if ($('#email').val() == '')
                return createError('email', 'Please enter a valid email');
            if ($('#company').val() == '')
                return createError('company', 'Please enter a valid company');
            return true;
        }

        function hasHtml5Validation() {
            return typeof document.createElement('input').checkValidity === 'function';
        }
    </script>
<?php $conn->close(); ?>
<?php include('../includes/footer_new.php');?>
