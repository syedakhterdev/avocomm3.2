<?php
$title =  'administrative';
$subtitle = 'admins';
require( '../config.php' );
require( '../includes/pdo.php' );
require( '../includes/check_login.php' );
require( '../includes/AdminManager.php' );
$Admin = new AdminManager($conn);
$msg = '';

if (!(int) $_SESSION['admin_sa'])
    header('Location: /manager/menu.php');

// check variables passed in through querystring
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$update = isset($_POST['update']) ? (int) $_POST['update'] : 0;

// if an id is passed in then let's fetch it from the database
if ($id) {
    if (!( $row = $Admin->getByID($id) )) {
        $msg = "Sorry, a record with the specified ID could not be found!";
    }

    $del_photo = ( isset($_GET['del_photo']) ) ? (int) $_GET['del_photo'] : 0;
    if ($del_photo) {
        $Admin->removePhoto($id);
        $row['photo'] = '';
    }
} else if ($update) { // if the user submitted a form....
    $token = $_SESSION['upd_token'];
    unset($_SESSION['upd_token']);

    if (!$msg && $token != '' && $token == $_POST['upd_token']) {
        if (!$Admin->update($_POST['update'], $_POST['username'], $_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['password'], $_POST['photo'], $_POST['permission_news'], $_POST['permission_events'], $_POST['permission_reports'], $_POST['permission_trade'], $_POST['permission_marketing_activities'], $_POST['permission_shopper_hub'], $_POST['permission_fs_hub'], $_POST['permission_periods'], $_POST['permission_users'], $_POST['sa'], $_POST['active'])) {
            $msg = "Sorry, an error has occurred, please contact your administrator.<br>Error: " . $Admin->error() . ";";
        } else {
            if ((int) $_POST['update'] == (int) $_SESSION['admin_id']) {
                $_SESSION['admin_permission_news'] = (int) $_POST['permission_news'];
                $_SESSION['admin_permission_events'] = (int) $_POST['permission_events'];
                $_SESSION['admin_permission_trade'] = (int) $_POST['permission_trade'];
                $_SESSION['admin_permission_reports'] = (int) $_POST['permission_reports'];
                $_SESSION['admin_permission_periods'] = (int) $_POST['permission_periods'];
                $_SESSION['admin_permission_shopper_hub'] = (int) $_POST['permission_shopper_hub'];
                $_SESSION['admin_permission_fs_hub'] = (int) $_POST['permission_fs_hub'];
                $_SESSION['admin_permission_marketing_activities'] = (int) $_POST['permission_marketing_activities'];
                $_SESSION['admin_permission_users'] = (int) $_POST['permission_users'];
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
                <h2><bold>Edit an</bold> ADMIN</h2>
            </div>
            <div class="add-new-entry-sec">
                <button type="button" id="cancel" name="cancel" class="btn btn-primary back-btn" onclick="window.location.href = '<?php echo ADMIN_URL?>/Admins/index.php';">
                    <img src="<?php echo ADMIN_URL?>/images/back-button.png" alt="back">
                </button>
            </div>
        </div>
    </div>


    <div class="main-form">
        <div class="container">
            <?php if ($msg) echo "<div class=\"alert alert-success\">$msg</div>"; ?>
            <form action="<?php echo ADMIN_URL?>/Admins/edit.php" role="form" method="POST" onSubmit="return validateForm();">
                <input type="hidden" name="update" value="<?php echo $row['id']; ?>">
                <input type="hidden" name="upd_token" value="<?php echo $_SESSION['upd_token']; ?>">

                <div class="form-group text-box">
                    <label for="fname">Username *</label><br>
                    <input type="text" id="username" name="username" onKeyUp="updateCountdown('#username', 25, '#username_lbl');" placeholder="" required onKeyDown="updateCountdown('#username', 25, '#username_lbl');" value="<?php echo ( $msg ) ? $_POST['username'] : $row['username']; ?>" maxlength="25">
                    <span id="username_lbl" class="small"></span>
                </div>


                <div class="form-group text-box">
                    <label for="fname">First Name</label><br>
                    <input type="text" id="first_name" name="first_name" onKeyUp="updateCountdown('#first_name', 35, '#first_name_lbl');" placeholder=""  onKeyDown="updateCountdown('#first_name', 35, '#first_name_lbl');" value="<?php echo ( $msg ) ? $_POST['first_name'] : $row['first_name']; ?>" maxlength="35">
                    <span id="first_name_lbl" class="small"></span>
                </div>


                <div class="form-group text-box">
                    <label for="fname">Last Name</label><br>
                    <input type="text" id="last_name" name="last_name" onKeyUp="updateCountdown('#last_name', 35, '#last_name_lbl');" placeholder=""  onKeyDown="updateCountdown('#last_name', 35, '#last_name_lbl');" value="<?php echo ( $msg ) ? $_POST['last_name'] : $row['last_name']; ?>" maxlength="35">
                    <span id="last_name_lbl" class="small"></span>
                </div>


                <div class="form-group text-box">
                    <label for="fname">Email *</label><br>
                    <input type="email" id="email" name="email" onKeyUp="updateCountdown('#email', 120, '#email_lbl');" placeholder="" required onKeyDown="updateCountdown('#email', 120, '#email_lbl');" value="<?php echo ( $msg ) ? $_POST['email'] : $row['email']; ?>" maxlength="120">
                    <span id="email_lbl" class="small"></span>
                </div>

                <div class="form-group text-box">
                    <label for="fname">Password *</label><br>
                    <input type="password" id="password" name="password" onKeyUp="updateCountdown('#password', 40, '#password_lbl');" placeholder=""  onKeyDown="updateCountdown('#password', 40, '#password_lbl');" maxlength="40" autocomplete="off">
                    <span id="password_lbl" class="small"></span>
                </div>
                <?php if ($row['photo'] != '') { ?>

                <div class="form-group text-box">
                    <label for="fname">Photo</label><br>
                    <div class="col-sm-12">
                        <img src="<?php echo ADMIN_URL?>/timThumb.php?src=/assets/admins/<?php echo $row['photo']; ?>&w=200&h=80" style="border: 1px solid #CCCCCC;padding: 2px;margin: 4px;">
                        <br>
                        <a href="<?php echo ADMIN_URL?>/Admins/edit.php?id=<?php echo $id; ?>&del_photo=1" class="btn action_btn cancel">Remove Photo</a>
                    </div>

                </div>
                <?php } else { ?>

                <div class="form-group text-box">
                    <label for="fname">Photo</label><br>
                    <input type="text" id="photo" name="photo" placeholder="Click to upload" onfocus="this.blur();" onclick="window.open('../includes/tinymce/plugins/filemanager/dialog.php?type=1&fldr=admins&field_id=photo&popup=1', '<?php echo time(); ?>', 'width=900,height=550,toolbar=0,menubar=0,location=0,status=1,scrollbars=1,resizable=1,left=0,top=0');return false;" >
                    <script>
                        function responsive_filemanager_callback(field_id) {
                            var url = jQuery('#' + field_id).val();
                            url = url.replace("https://<?php echo $_SERVER['HTTP_HOST']; ?>/assets/admins/", '');
                            jQuery('#' + field_id).val(url);
                        }
                    </script>

                </div>
                <?php } ?>

                <div class="form-group checkbox-wrap">
                    <label for="fname">Create/Modify</label><br>
                    <div class="checkbox-inner">
                        <div>
                            <input name="permission_news" id="permission_news" type="checkbox" value="1" <?php if (isset($_POST['permission_news']) || (int) $row['permission_news']) echo "CHECKED"; ?>>
                            <label for="html">News</label>
                        </div>
                        <div>
                            <input name="permission_events" id="permission_events" type="checkbox" value="1" <?php if (isset($_POST['permission_events']) || (int) $row['permission_events']) echo "CHECKED"; ?>>
                            <label for="html">Events</label>
                        </div>
                        <div>
                            <input name="permission_reports" id="permission_reports" type="checkbox" value="1" <?php if (isset($_POST['permission_reports']) || (int) $row['permission_reports']) echo "CHECKED"; ?>>
                            <label for="html">Reports</label>
                        </div>
                        <div>
                            <input name="permission_trade" id="permission_trade" type="checkbox" value="1" <?php if (isset($_POST['permission_trade']) || (int) $row['permission_trade']) echo "CHECKED"; ?> >
                            <label for="html">Trade</label>
                        </div>
                        <div>
                            <input name="permission_marketing_activities" id="permission_marketing_activities" type="checkbox" value="1" <?php if (isset($_POST['permission_marketing_activities']) || (int) $row['permission_marketing_activities']) echo "CHECKED"; ?>>
                            <label for="html">Marketing Activities</label>
                        </div>
                        <div>
                            <input name="permission_shopper_hub" id="permission_shopper_hub" type="checkbox" value="1" <?php if (isset($_POST['permission_shopper_hub']) || (int) $row['permission_shopper_hub']) echo "CHECKED"; ?>>
                            <label for="html">Shopper Hub</label>
                        </div>
                        <div>
                            <input name="permission_fs_hub" id="permission_fs_hub" type="checkbox" value="1" <?php if (isset($_POST['permission_fs_hub']) || (int) $row['permission_fs_hub']) echo "CHECKED"; ?>>
                            <label for="html">Foodservice Hub</label>
                        </div>
                        <div>
                            <input name="permission_periods" id="permission_periods" type="checkbox" value="1" <?php if (isset($_POST['permission_periods']) || (int) $row['permission_periods']) echo "CHECKED"; ?>>
                            <label for="html">Periods</label>
                        </div>
                        <div>
                            <input name="permission_users" id="permission_users" type="checkbox" value="1" <?php if (isset($_POST['permission_users']) || (int) $row['permission_users']) echo "CHECKED"; ?>>
                            <label for="html">Users</label>
                        </div>
                    </div>
                </div>

                <div class="form-group checkbox-wrap">
                    <label for="fname">SA</label><br>
                    <div class="checkbox-inner">
                        <div>
                            <input name="sa" id="sa" type="checkbox" value="1" <?php if (isset($_POST['sa']) || (int) $row['sa']) echo "CHECKED"; ?>>
                            <label for="html">SA</label>
                        </div>
                    </div>
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
                    <img src="<?php echo ADMIN_URL?>/images/login-submit-btn.png" onmouseover="this.src='<?php echo ADMIN_URL?>/images/login-submit-hvr-btn.png'" onmouseout="this.src='<?php echo ADMIN_URL?>/images/login-submit-btn.png'" alt="login-submit-btn">
                </button>

                <button type="button" id="cancel" name="cancel" onClick="window.location.href = '<?php echo ADMIN_URL?>/Admins/index.php';">
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
            updateCountdown('#username', 25, '#username_lbl');
            updateCountdown('#first_name', 35, '#first_name_lbl');
            updateCountdown('#last_name', 35, '#last_name_lbl');
            updateCountdown('#email', 120, '#email_lbl');
            updateCountdown('#password', 40, '#password_lbl');
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
            if ($('#username').val() == '')
                return createError('username', 'Please enter a valid username');
            if ($('#email').val() == '')
                return createError('email', 'Please enter a valid email');
        }

        function hasHtml5Validation() {
            return typeof document.createElement('input').checkValidity === 'function';
        }
    </script>
<?php $conn->close(); ?>
<?php include('../includes/footer_new.php');?>