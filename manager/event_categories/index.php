<?php
$title =  'administrative';
$subtitle = 'events';
require( '../config.php' );
require( '../includes/pdo.php' );
require( '../includes/check_login.php' );
require( '../includes/EventCategoryManager.php' );
$EventCategory = new EventCategoryManager($conn);

$msg = '';
$error = '';
$criteria = '';
$page = ( empty($_GET['page']) ) ? 1 : (int) $_GET['page'];

$del_id = ( isset($_POST['del']) ) ? (int) $_POST['del'] : '';
$update = ( isset($_GET['update']) ) ? (int) $_GET['update'] : '';
$add = ( isset($_GET['add']) ) ? (int) $_GET['add'] : '';

if ($del_id) {
    // get the delete token that was set previously
    $token = $_SESSION['del_token'];
    unset($_SESSION['del_token']);

    if ($token != '' && $_POST['token'] == $token) {
        if (!$EventCategory->delete($del_id)) {
            $msg = "Sorry, an error has occurred, please contact your administrator!<br>Error:" . $conn->error();
            $msg = 'The category could not be deleted, please check to make sure no events have been assigned to this category.';
        } else {
            $msg = "The specified record was deleted successfully!";
        }
    }
} else if ($update) {
    $msg = "The specified record was updated successfully!";
} else if ($add) {
    $msg = "Your item was added successfully!";
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
        <?php if ($msg) echo '<div class="alert alert-success" role="alert">' . $msg . '</div>'; ?>
        <?php if ($error) echo '<div class="alert alert-error" role="alert">' . $error . '</div>'; ?>

        <div class="container">
            <div class="heading_sec">
                <h2><bold>MANAGE</bold> EVENT CATEGORIES</h2>
            </div>
            <div class="add-new-entry-sec">
                <a href="<?php echo ADMIN_URL?>/event_categories/add.php">
                    <img src="<?php echo ADMIN_URL?>/images/add-new-entry-btn.png" alt="" />
                </a>
            </div>
        </div>
    </div>

    <div class="entry-section event-category-section">
        <div class="container">
            <div class="entry-list">
                <div class="entry-row heading">
                    <div class="title-col">
                        <h3>Category</h3>
                    </div>
                    <div class="active-col">
                        <h3>Action</h3>
                    </div>
                </div>


                <?php
                //$sql = "SELECT COUNT(*) FROM users WHERE id > 0;";
                $rowsPerPage = 15;
                $total_count = $EventCategory->getEvent_CategoriesCount();
                $conn->getPaging($total_count, $page, $rowsPerPage);
                $result = $EventCategory->getEvent_Categories($conn->offset, $rowsPerPage);
                if ($conn->num_rows() > 0) {
                    while ($row = $conn->fetch($result)) { ?>

                        <div class="entry-row">
                            <div class="title-col">
                                <div class="title-sec">
                                    <h4><?php echo $conn->parseOutputString($row['category'])?></h4>
                                </div>
                            </div>
                            <div class="active-col">
                                <div class="action-sec">
                                    <a href="<?php echo ADMIN_URL?>/event_categories/edit.php?id=<?php echo $row['id']?>">
                                        <img src="<?php echo ADMIN_URL?>/images/edit-btn.png" alt=""/>
                                    </a>
                                    <div class="delete_form">
                                        <form action="<?php echo ADMIN_URL?>/event_categories/index.php?page=<?php echo $page?>&criteria=<?php echo $criteria?>" method="POST" onSubmit="return confirm('Are you sure you want to delete this item?');">
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
