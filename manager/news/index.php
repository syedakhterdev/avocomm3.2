<?php
$title =  'news';
require( '../config.php' );
require( '../includes/pdo.php' );
require( '../includes/check_login.php' );
require( '../includes/NewManager.php' );
$New = new NewManager($conn);

$msg = '';
$error = '';
$criteria = '';
$page = ( empty($_GET['page']) ) ? 1 : (int) $_GET['page'];

$del_id = ( isset($_POST['del']) ) ? (int) $_POST['del'] : '';
$update = ( isset($_GET['update']) ) ? (int) $_GET['update'] : '';
$add = ( isset($_GET['add']) ) ? (int) $_GET['add'] : '';
$active = ( isset($_GET['active']) ) ? (int) $_GET['active'] : '';
$sort = ( isset($_GET['sort']) ) ? $_GET['sort'] : '';
$mo = ( isset($_GET['mo']) ) ? $_GET['mo'] : '';
$yr = ( isset($_GET['yr']) ) ? $_GET['yr'] : '';

if ($del_id) {
    // get the delete token that was set previously
    $token = $_SESSION['del_token'];
    unset($_SESSION['del_token']);

    if ($token != '' && $_POST['token'] == $token) {
        if (!$New->delete($_SESSION['admin_period_id'], $del_id)) {
            $msg = "Sorry, an error has occurred, please contact your administrator!<br>Error:" . $conn->error();
        } else {
            $msg = "The specified record was deleted successfully!";
        }
    }
} else if ($update) {
    $msg = "The specified record was updated successfully!";
} else if ($add) {
    $msg = "Your item was added successfully!";
} else if ($active) {
    $cur = ( isset($_GET['cur']) ) ? (int) $_GET['cur'] : '';
    $sql = 'UPDATE news SET active = ? WHERE id = ?';
    $conn->exec( $sql, array( $cur ? 0 : 1, $active ) );
    $msg = "Active status was changed successfully!";
}

// create a token for secure deletion (from this page only and not remote)
$_SESSION['del_token'] = md5(uniqid());
session_write_close();
?>

<?php require( '../includes/header_new.php' );?>

    <div class="latest_activities hd-grid activity_log">
        <?php if ($msg) echo '<div class="alert alert-success" role="alert">' . $msg . '</div>'; ?>
        <?php if ($error) echo '<div class="alert alert-error" role="alert">' . $error . '</div>'; ?>

        <div class="container">
            <div class="heading_sec">
                <h2><bold>MANAGE</bold> News</h2>
            </div>

            <div class="add-new-entry-sec">
                <form action="<?php echo ADMIN_URL?>/news/index.php" method="GET">
                    <select name="mo" class="entry-options">
                        <option value="">Month</option>
                        <option value="01" <?php if ( $mo == '01' ) echo 'SELECTED'; ?>>January</option>
                        <option value="02" <?php if ( $mo == '02' ) echo 'SELECTED'; ?>>February</option>
                        <option value="03" <?php if ( $mo == '03' ) echo 'SELECTED'; ?>>March</option>
                        <option value="04" <?php if ( $mo == '04' ) echo 'SELECTED'; ?>>April</option>
                        <option value="05" <?php if ( $mo == '05' ) echo 'SELECTED'; ?>>May</option>
                        <option value="06" <?php if ( $mo == '06' ) echo 'SELECTED'; ?>>June</option>
                        <option value="07" <?php if ( $mo == '07' ) echo 'SELECTED'; ?>>July</option>
                        <option value="08" <?php if ( $mo == '08' ) echo 'SELECTED'; ?>>August</option>
                        <option value="09" <?php if ( $mo == '09' ) echo 'SELECTED'; ?>>September</option>
                        <option value="10" <?php if ( $mo == '10' ) echo 'SELECTED'; ?>>October</option>
                        <option value="11" <?php if ( $mo == '11' ) echo 'SELECTED'; ?>>November</option>
                        <option value="12" <?php if ( $mo == '12' ) echo 'SELECTED'; ?>>December</option>
                    </select>
                    <select name="yr" class="entry-options">
                        <option value="">Year</option>
                        <?php
                        for ( $i = 2019; $i <= date( 'Y' ); $i ++ ) {
                            if ( $i == $yr )
                                echo '<option SELECTED value="' . $i . '">' . $i . '</option>' . "\n";
                            else
                                echo '<option value="' . $i . '">' . $i . '</option>' . "\n";
                        }
                        ?>
                    </select>
                    <button type="submit" name="search" id="search" class="btn btn-primary">Go</button>
                </form>
                <a href="<?php echo ADMIN_URL?>/news/add.php">
                    <img src="<?php echo ADMIN_URL?>/images/add-new-entry-btn.png" alt="" />
                </a>
            </div>
        </div>
    </div>


    <div class="entry-section report-section">
        <div class="container">
            <div class="entry-list">
                <div class="entry-row heading">
                    <div class="title-col">
                        <h3><a href="<?php echo ADMIN_URL?>/news/index.php?page=<?php echo $page; ?>&sort=title">Title</a></h3>
                    </div>
                    <div class="title-col sort-col">
                        <h3><a href="<?php echo ADMIN_URL?>/news/index.php?page=<?php echo $page; ?>&sort=date_created">Date</a></h3>
                    </div>
                    <div class="active-col">
                        <h3>Action</h3>
                    </div>
                </div>


                <?php
                //$sql = "SELECT COUNT(*) FROM shopper_programs WHERE id > 0;";
                $rowsPerPage = 25;
                $total_count = $New->getNewsCount($_SESSION['admin_period_id'], $mo, $yr);
                $conn->getPaging($total_count, $page, $rowsPerPage, '&mo=' . $mo . '&yr=' . $yr);
                $result = $New->getNews($_SESSION['admin_period_id'], $sort, $conn->offset, $rowsPerPage, $mo, $yr);
                if ($conn->num_rows() > 0) {
                    while ($row = $conn->fetch($result)) { ?>

                        <div class="entry-row">
                            <div class="title-col">
                                <div class="title-sec">
                                    <?php if($row['image']){?>
                                        <div class="entry-img">
                                            <img src="<?php echo ADMIN_URL?>/timThumb.php?src=<?php echo SITE_URL?>/assets/news/<?php echo $row['image']?>&w=28&h=28&zc=1" width="28" height="28" alt=""/>
                                        </div>
                                    <?php }?>
                                    <h4><?php echo $conn->parseOutputString($row['title'])?></h4>
                                </div>
                            </div>
                            <div class="title-col sort-col">
                                <div class="title-sec">
                                    <h4><?php echo date( 'm/d/Y', strtotime( $row['date_created'] ) )?></h4>
                                </div>
                            </div>
                            <div class="active-col">
                                <div class="action-sec">
                                    <?php if($row['active']==1){?>
                                        <a onClick="return confirm('Are you sure you want to change the active status of this item?');" href="<?php echo ADMIN_URL?>/news/index.php?active=<?php echo $row['id']?>&cur=<?php echo (int)$row['active']?>">
                                            <img src="<?php echo ADMIN_URL?>/images/on-btn.png" alt=""/>
                                        </a>
                                    <?php }else{?>
                                        <a onClick="return confirm('Are you sure you want to change the active status of this item?');" href="<?php echo ADMIN_URL?>/news/index.php?active=<?php echo $row['id']?>&cur=<?php echo (int)$row['active']?>">
                                            <img src="<?php echo ADMIN_URL?>/images/off-btn.png" alt=""/>
                                        </a>
                                    <?php }?>
                                    <a href="<?php echo ADMIN_URL?>/news/edit.php?id=<?php echo $row['id']?>">
                                        <img src="<?php echo ADMIN_URL?>/images/edit-btn.png" alt=""/>
                                    </a>
                                    <div class="delete_form">
                                        <form action="<?php echo ADMIN_URL?>/news/index.php?page=<?php echo $page?>&criteria=<?php echo $criteria?>" method="POST" onSubmit="return confirm('Are you sure you want to delete this item?');">
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
<?php echo $conn->paging(); ?>
<?php include('../includes/footer_new.php');?>