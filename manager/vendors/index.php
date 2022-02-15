<?php
$title =  'trades';
$subtitle = 'vendor_entries';
require( '../config.php' );

require( '../includes/pdo.php' );
require( '../includes/check_login.php' );
require( '../includes/VendorManager.php' );
$Vendor = new VendorManager($conn);

$msg = '';
$error = '';
$criteria = '';
$page = ( empty($_GET['page']) ) ? 1 : (int) $_GET['page'];

$del_id = ( isset($_POST['del']) ) ? (int) $_POST['del'] : '';
$update = ( isset($_GET['update']) ) ? (int) $_GET['update'] : '';
$add = ( isset($_GET['add']) ) ? (int) $_GET['add'] : '';
$active = ( isset($_GET['active']) ) ? (int) $_GET['active'] : '';

if ($del_id && (int)$_SESSION['admin_permission_trade']) {
    // get the delete token that was set previously
    $token = $_SESSION['del_token'];
    unset($_SESSION['del_token']);

    if ($token != '' && $_POST['token'] == $token) {
        if (!$Vendor->delete($del_id)) {
            $msg = "Sorry, an error has occurred, please contact your administrator!<br>Error:" . $conn->error();
        } else {
            $msg = "The specified record was deleted successfully!";
        }
    }
} else if ($update) {
    $msg = "The specified record was updated successfully!";
} else if ($add) {
    $msg = "Your item was added successfully!";
} else if ($active && (int)$_SESSION['admin_permission_trade']) {
    $cur = ( isset($_GET['cur']) ) ? (int) $_GET['cur'] : '';
    $sql = 'UPDATE vendors SET active = ? WHERE id = ?';
    $conn->exec( $sql, array( $cur ? 0 : 1, $active ) );
    $msg = "Active status was changed successfully!";
}

// create a token for secure deletion (from this page only and not remote)
$_SESSION['del_token'] = md5(uniqid());
session_write_close();
?>
<?php require( '../includes/header_new.php' );?>


    <div class="dashboard-sub-menu-sec">
        <div class="container">
            <div class="sub-menu-sec">
                <?php require( '../includes/trade_sub_nav.php' );?>
            </div>
        </div>
    </div>

    <div class="latest_activities hd-grid">
        <div class="container">
            <div class="heading_sec">
                <h2><bold>MANAGE</bold> TRADE VENDORS</h2>
            </div>
            <div class="add-new-entry-sec">
                <a href="<?php echo ADMIN_URL?>/vendors/add.php">
                    <img src="<?php echo ADMIN_URL?>/images/add-new-entry-btn.png" alt="" />
                </a>
            </div>
        </div>
    </div>

    <div class="entry-section">
        <?php if ($msg) echo '<div class="alert alert-success" role="alert">' . $msg . '</div>'; ?>
        <?php if ($error) echo '<div class="alert alert-error" role="alert">' . $error . '</div>'; ?>

        <div class="container">
            <div class="entry-list">
                <div class="entry-row heading">
                    <div class="title-col">
                        <h3>Title</h3>
                    </div>
                    <div class="sort-col">
                        <h3>Sort</h3>
                    </div>
                    <div class="active-col">
                        <h3>active</h3>
                    </div>
                </div>
                <?php

                $rowsPerPage = 15;
                $total_count = $Vendor->getVendorsCount();
                $conn->getPaging($total_count, $page, $rowsPerPage);
                $result = $Vendor->getVendors($conn->offset, $rowsPerPage);

                if ($conn->num_rows() > 0) {
                    while ($row = $conn->fetch($result)) {
                        ?>

                        <div class="entry-row">
                            <div class="title-col">
                                <div class="title-sec">
                                    <?php
                                    if($row['logo']){?>
                                        <div class="entry-img">
                                            <img src="<?php echo ADMIN_URL?>/timThumb.php?src=<?php echo SITE_URL?>/assets/vendors/<?php echo $row['logo']?>&h=60&zc=1" alt="<?php echo $conn->parseOutputString($row['title'])?>"/>
                                        </div>
                                    <?php }?>
                                    <h4><?php echo $conn->parseOutputString($row['title'])?></h4>
                                </div>
                            </div>
                            <div class="sort-col">
                                <?php echo $row['sort']?>
                            </div>
                            <div class="active-col">
                                <div class="action-sec">
                                    <a href="<?php echo ADMIN_URL?>/vendors/edit.php?id=<?php echo $row['id']?>">
                                        <img src="<?php echo ADMIN_URL?>/images/edit-btn.png" alt=""/>
                                    </a>
                                    <div class="delete_form">
                                        <form action="<?php echo ADMIN_URL?>/vendors/index.php?page=<?php echo $page?>&criteria=<?php echo $criteria?>" method="POST" onSubmit="return confirm('Are you sure you want to delete this item?');">
                                            <input type="hidden" name="del" value="<?php echo $row['id']?>">
                                            <input type="hidden" name="token" value="<?php echo $_SESSION['del_token']?>">
                                            <button type="submit" class="action_btn delete">
                                                <img src="<?php echo ADMIN_URL?>/images/delete-btn.png" alt="">
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php }
                }else{?>
                    <div class="entry-row">
                        No entries found.
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
    <script>
        $(document).ready(function () {
            window.setTimeout(function () {
                $(".alert").fadeTo(500, 0).slideUp(500, function () {
                    $(this).remove();
                });
            }, 2000);
        });
    </script>
<?php $conn->close(); ?>
<?php include('../includes/footer_new.php');?>