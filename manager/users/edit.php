<?php
$title =  'administrative';
$subtitle = 'users';
require( '../config.php' );
require( '../includes/pdo.php' );
require( '../includes/check_login.php' );
require( '../includes/UserManager.php' );
$User = new UserManager($conn);
$msg = '';

// check variables passed in through querystring
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$update = isset($_POST['update']) ? (int) $_POST['update'] : 0;

// if an id is passed in then let's fetch it from the database
if ($id) {
    if (!( $row = $User->getByID($id) )) {
        $msg = "Sorry, a record with the specified ID could not be found!";
    }
} else if ($update) { // if the user submitted a form....
    $token = $_SESSION['upd_token'];
    unset($_SESSION['upd_token']);

    if (!$msg && $token != '' && $token == $_POST['upd_token']) {
        if (!$User->update($_POST['update'], $_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['password'], $_POST['company'], $_POST['active'])) {
            $msg = "Sorry, an error has occurred, please contact your administrator.<br>Error: " . $User->error() . ";";
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
                <h2><bold>Edit A</bold> User</h2>
            </div>
            <div class="add-new-entry-sec">
                <button type="button" id="cancel" name="cancel" class="btn btn-primary back-btn" onclick="window.location.href = '<?php echo ADMIN_URL?>/users/index.php';">
                    <img src="<?php echo ADMIN_URL?>/images/back-button.png" alt="back">
                </button>
            </div>
        </div>
    </div>

    <div class="main-form">
        <div class="container">
            <?php if ($msg) echo "<div class=\"alert alert-success\">$msg</div>"; ?>
            <form action="<?php echo ADMIN_URL?>/users/edit.php" role="form" method="POST" onSubmit="return validateForm();" >
                <input type="hidden" name="update" value="<?php echo $row['id']; ?>">
                <input type="hidden" name="upd_token" value="<?php echo $_SESSION['upd_token']; ?>">

                <div class="form-group text-box">
                    <label for="fname">First Name</label><br>
                    <input type="text"  id="first_name" name="first_name" onKeyUp="updateCountdown('#first_name', 40, '#first_name_lbl');" placeholder="" required onKeyDown="updateCountdown('#first_name', 40, '#first_name_lbl');" value="<?php echo ( $msg ) ? $_POST['first_name'] : $row['first_name']; ?>" maxlength="40">
                    <span id="first_name_lbl" class="small"></span>
                </div>


                <div class="form-group text-box">
                    <label for="fname">Last Name</label><br>
                    <input type="text" id="last_name" name="last_name" onKeyUp="updateCountdown('#last_name', 40, '#last_name_lbl');" placeholder="" required onKeyDown="updateCountdown('#last_name', 40, '#last_name_lbl');" value="<?php echo ( $msg ) ? $_POST['last_name'] : $row['last_name']; ?>" maxlength="40">
                    <span id="last_name_lbl" class="small"></span>
                </div>


                <div class="form-group text-box">
                    <label for="fname">Email *</label><br>
                    <input type="email" id="email" name="email" onKeyUp="updateCountdown('#email', 120, '#email_lbl');" placeholder="" required onKeyDown="updateCountdown('#email', 120, '#email_lbl');" value="<?php echo ( $msg ) ? $_POST['email'] : $row['email']; ?>" maxlength="120">
                    <span id="email_lbl" class="small"></span>
                </div>

                <div class="form-group text-box">
                    <label for="fname">Password</label><br>
                    <input type="password" id="password" name="password" onKeyUp="updateCountdown('#password', 40, '#password_lbl');" placeholder=""  onKeyDown="updateCountdown('#password', 40, '#password_lbl');" value="<?php echo ( $msg ) ? $_POST['password'] : ''; ?>" maxlength="40" autocomplete="new-password">
                    <span id="password_lbl" class="small"></span>
                </div>

                <div class="form-group text-box">
                    <label for="fname">Company *</label><br>
                    <input type="text" class="form-control" id="company" name="company" onKeyUp="updateCountdown('#company', 80, '#company_lbl');" placeholder="" required onKeyDown="updateCountdown('#company', 80, '#company_lbl');" value="<?php echo ( $msg ) ? $_POST['company'] : $row['company']; ?>" maxlength="80">
                    <span id="company_lbl" class="small"></span>
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

                <div class="form-group checkbox-wrap">
                    <small>Last login: <?php echo date( 'm/d/Y h:i a', strtotime( $row['last_login'] ) ); ?> &nbsp;&nbsp;&nbsp; Last verification: <?php echo date( 'm/d/Y h:i a', strtotime( $row['last_verify'] ) ); ?></small>
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
            updateCountdown('#password', 40, '#password_lbl');
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
        }
        function hasHtml5Validation() {
            return typeof document.createElement('input').checkValidity === 'function';
        }
    </script>

<?php $conn->close(); ?>
<?php include('../includes/footer_new.php');?>