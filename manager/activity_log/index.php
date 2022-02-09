<?php
$title =  'administrative';
$subtitle = 'activity_log';
require( '../config.php' );

require( '../includes/pdo.php' );
require( '../includes/check_login.php' );
require( '../includes/ActivityLogManager.php' );
$Activity = new ActivityLogManager($conn);

$msg = '';
$error = '';
$filters = '';
$page = ( empty($_GET['page']) ) ? 1 : (int) $_GET['page'];
$activity_type_id = ( isset($_GET['aid']) ) ? (int) $_GET['aid'] : '';
$user_id = ( isset($_GET['uid']) ) ? (int) $_GET['uid'] : '';
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
        <?php if ($msg) echo '<div class="alert alert-success" role="alert">' . $msg . '</div>'; ?>
        <?php if ($error) echo '<div class="alert alert-error" role="alert">' . $error . '</div>'; ?>

        <div class="container">
            <div class="heading_sec">
                <h2><bold>MANAGE</bold> ACTIVITY LOG</h2>
            </div>

            <div class="add-new-entry-sec">
                <form action="<?php echo ADMIN_URL?>/activity_log/index.php" method="GET">
                <select class="entry-options" name="aid" id="aid">
                    <option value="">Activity Type...</option>
                    <?php
                    $sql = "SELECT id, activity_type FROM activity_types ORDER BY activity_type";
                    $acts = $conn->query($sql, array());
                    if ($conn->num_rows() > 0) {
                        while ($act = $conn->fetch($acts)) {
                            if ((int) $activity_type_id == (int) $act['id'])
                                echo '<option SELECTED value="' . $act['id'] . '">' . $act['activity_type'] . '</option>' . "\n";
                            else
                                echo '<option value="' . $act['id'] . '">' . $act['activity_type'] . '</option>' . "\n";
                        }
                    }
                    ?>
                </select>
                <select class="entry-options" name="uid" id="uid">
                    <option value="">User...</option>
                    <?php
                    $sql = "SELECT id, CONCAT( last_name, ', ', first_name ) AS full_name FROM users ORDER BY last_name";
                    $uids = $conn->query($sql, array());
                    if ($conn->num_rows() > 0) {
                        while ($uid = $conn->fetch($uids)) {
                            if ((int) $user_id == (int) $uid['id'])
                                echo '<option SELECTED value="' . $uid['id'] . '">' . $uid['full_name'] . '</option>' . "\n";
                            else
                                echo '<option value="' . $uid['id'] . '">' . $uid['full_name'] . '</option>' . "\n";
                        }
                    }
                    ?>
                </select>
                    <button type="submit" name="search" id="search" class="btn btn-primary">Go</button>
                </form>
                <a class="new-add-btn" href="<?php echo ADMIN_URL?>/activity_log/download.php?aid=<?php echo $activity_type_id; ?>&uid=<?php echo $user_id; ?>">
                    Download
                </a>
            </div>
        </div>
    </div>

    <div class="data-section">
        <div class="container">
            <div class="data-list">
                <div class="data-row heading">
                    <div class="date-col">
                        <h3>Date</h3>
                    </div>
                    <div class="active-col">
                        <h3>Activity</h3>
                    </div>
                    <div class="user-col">
                        <h3>User</h3>
                    </div>
                    <div class="note-col">
                        <h3>Note</h3>
                    </div>
                </div>
                <?php
                //$sql = "SELECT COUNT(*) FROM events WHERE id > 0;";
                $rowsPerPage = 50;
                if ($activity_type_id)
                    $filters .= ' AND a.activity_type_id = ' . $activity_type_id;
                if ($user_id)
                    $filters .= ' AND a.user_id = ' . $user_id;
                $total_count = $Activity->getActivityCount($filters);
                $conn->getPaging($total_count, $page, $rowsPerPage, "&uid=$user_id&aid=$activity_type_id");
                $result = $Activity->getActivity($conn->offset, $rowsPerPage, $filters);

                if ($conn->num_rows() > 0) {
                    while ($row = $conn->fetch($result)) {
                        ?>
                        <div class="data-row">
                            <div class="date-col">
                                <?php echo date('m/d/Y', strtotime($row['date_created']))?>
                            </div>
                            <div class="active-col">
                                <?php echo $conn->parseOutputString($row['activity_type'])?>
                            </div>
                            <div class="user-col">
                                <?php echo $conn->parseOutputString($row['full_name'])?>
                            </div>
                            <div class="note-col">
                                <?php echo $conn->parseOutputString($row['reference'])?>
                            </div>
                        </div>

                    <?php }
                }else{?>
                    <div class="data-row">
                        No Record Found
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
