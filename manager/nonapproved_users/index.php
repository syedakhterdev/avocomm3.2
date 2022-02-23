<?php
$title =  'administrative';
$subtitle = 'users';
require( '../config.php' );
require( '../includes/pdo.php' );
require( '../includes/check_login.php' );
require( '../includes/UserManager.php' );
$User = new UserManager($conn);

$msg = '';
$error = '';
$criteria = '';
$page = ( empty($_GET['page']) ) ? 1 : (int) $_GET['page'];

$del_id = ( isset($_POST['del']) ) ? (int) $_POST['del'] : '';
$update = ( isset($_GET['update']) ) ? (int) $_GET['update'] : '';
$add = ( isset($_GET['add']) ) ? (int) $_GET['add'] : '';
$active = ( isset($_GET['active']) ) ? (int) $_GET['active'] : '';
$resend = ( isset($_GET['resend']) ) ? (int) $_GET['resend'] : '';

if ($del_id && (int)$_SESSION['admin_permission_users']) {
    // get the delete token that was set previously
    $token = $_SESSION['del_token'];
    unset($_SESSION['del_token']);

    if ($token != '' && $_POST['token'] == $token) {
        if (!$User->delete($del_id)) {
            $msg = "Sorry, an error has occurred, please contact your administrator!<br>Error:" . $conn->error();
        } else {
            $msg = "The specified record was deleted successfully!";
        }
    }
} else if ($update) {
    $msg = "The specified record was updated successfully!";
} else if ($add) {
    $msg = "Your item was added successfully!";
} else if ($active && (int)$_SESSION['admin_permission_users']) {
    $cur = ( isset($_GET['cur']) ) ? (int) $_GET['cur'] : '';
    $sql = 'UPDATE users SET active = ? WHERE id = ?';
    $conn->exec( $sql, array( $cur ? 0 : 1, $active ) );
    $msg = "Active status was changed successfully!";
}
else if ($resend && (int)$_SESSION['admin_permission_users']) {
    $user_data  =   $User->getByID($resend);
    require_once('../includes/EmailManager.php');
    $code = getRandomString(8);
    $mail = new EmailManager(
        $conn,
        '',
        2, // use template # 1
        $user_data['email'],
        stripslashes( $user_data['first_name'] . ' ' . $user_data['last_name'] ),
        '',
        '',
        true,
        [
            'NAME' => stripslashes( $user_data['first_name'] . ' ' . $user_data['last_name'] ),
            'HOST' => $_SERVER['HTTP_HOST'],
            'VERIFY_LINK' => '<a href="https://' . $_SERVER['HTTP_HOST'] . '/set_password.php?code=' . $code . '&email=' . $user_data['email'] . '">Set Your Password</a>'
        ]
    );
    $mail->send();

    $sql = 'UPDATE users SET set_password = ? WHERE id = ? LIMIT 1;';
    $conn->exec( $sql, array( $code, $resend ) );
    $msg = "Resent welcome email successfully!";
}

function getRandomString( $length = 8 ) {
    $characters = '0123456789';
    $charactersLength = strlen( $characters );
    $randomString = '';
    for ( $i = 0; $i < $length; $i++ ) {
        $randomString .= $characters[rand( 0, $charactersLength - 1 )];
    }
    return $randomString;
}

// create a token for secure deletion (from this page only and not remote)
$_SESSION['del_token'] = md5(uniqid());
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
    <div class="latest_activities hd-grid">
        <div class="container">
            <div class="heading_sec">
                <h2><bold>Non Approved</bold> USERS</h2>
            </div>
            <div class="add-new-entry-sec">

                <a class="new-add-btn" href="<?php echo ADMIN_URL?>/users">
                   All Users
                </a>
                <a class="new-add-btn" href="<?php echo ADMIN_URL?>/approved_users">
                    Approved Users
                </a>
            </div>
        </div>
    </div>
    <div class="entry-section administration-section">
        <div class="container">
            <div class="entry-list">
                <div class="entry-row heading">
                    <div class="title-col">
                        <h3>Name</h3>
                    </div>
                    <div class="sort-col">
                        <h3>Company</h3>
                    </div>
                    <div class="active-col">
                        <h3>Action</h3>
                    </div>
                </div>


                <?php
                //$sql = "SELECT COUNT(*) FROM users WHERE id > 0;";
                $rowsPerPage = 15;
                $total_count = $User->getNonApprovedUsersCount();
                $conn->getPaging($total_count, $page, $rowsPerPage);
                $result = $User->getNonApprovedUsers($conn->offset, $rowsPerPage);

                if ($conn->num_rows() > 0) {
                    while ($row = $conn->fetch($result)) { ?>

                        <div class="entry-row">
                            <div class="title-col">
                                <div class="title-sec">
                                    <h4><?php echo $conn->parseOutputString($row['first_name'] . ' ' . $row['last_name'])?></h4>
                                </div>
                            </div>
                            <div class="sort-col">
                                <?php echo $conn->parseOutputString($row['company'])?>
                            </div>
                            <div class="active-col">
                                <div class="action-sec">
                                    <?php if($row['active']==1){?>
                                        <a onClick="return confirm('Are you sure you want to change the active status of this item?');" href="index.php?active=<?php echo $row['id']?>&cur=<?php echo (int)$row['active']?>&page=<?php echo $page?>">
                                            <img src="<?php echo ADMIN_URL?>/images/on-btn.svg" alt=""/>
                                        </a>
                                    <?php }else{?>
                                        <a onClick="return confirm('Are you sure you want to change the active status of this item?');" href="index.php?active=<?php echo $row['id']?>&cur=<?php echo (int)$row['active']?>&page=<?php echo $page?>">
                                            <img src="<?php echo ADMIN_URL?>/images/off-btn.svg" alt=""/>
                                        </a>
                                    <?php }?>
                                    <a href="<?php echo ADMIN_URL?>/users/edit.php?id=<?php echo $row['id']?>">
                                        <img src="<?php echo ADMIN_URL?>/images/edit-btn.svg" alt=""/>
                                    </a>
                                    <div class="delete_form">
                                        <form action="<?php echo ADMIN_URL?>/users/index.php?page=<?php echo $page?>&criteria=<?php echo $criteria?>" method="POST" onSubmit="return confirm('Are you sure you want to delete this item?');">
                                            <input type="hidden" name="del" value="<?php echo $row['id']?>">
                                            <input type="hidden" name="token" value="<?php echo $_SESSION['del_token']?>">
                                            <button type="submit" class="action_btn delete">
                                                <img src="<?php echo ADMIN_URL?>/images/delete-btn.svg" alt="">
                                            </button>
                                        </form>
                                    </div>
                                    <a href = "<?php echo ADMIN_URL?>/users/index.php?resend=<?php echo $row['id']?>&page=<?php echo $page?>" title = "Resend Welcome Email" class="action_btn resend_email">
                                        <img src="<?php echo ADMIN_URL?>/images/wc-email.png" alt=""/>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php }
                }else{?>
                    <div class="entry-row">
                        No record Found
                    </div>
                <?php }?>

            </div>

            <div class="data-list-footer">
                <div class="data-count-pagi">
                    <?php echo $conn->paging(); ?>
                </div>
            </div>

        </div>
    </div>
<?php $conn->close(); ?>
<?php include('../includes/footer_new.php');?>