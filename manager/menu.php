<?php
$title = 'dashboard';
require( 'config.php' );
require( 'includes/pdo.php' );
require( 'includes/check_login.php' );
require( 'includes/header_new.php' );
?>


<div class="latest_activities">
    <div class="container">
        <div class="heading_sec">
            <h2>Latest Activities</h2>
        </div>
        <div class="exp_log_sec">
            <a href="<?php echo ADMIN_URL?>/export-log.php">
                <img src="<?php echo ADMIN_URL?>/images/exp-log-btn.png" onmouseover="this.src = '<?php echo ADMIN_URL?>/images/exp-log-btn-hvr.png'" onmouseout="this.src = '<?php echo ADMIN_URL?>/images/exp-log-btn.png'" alt="" />
            </a>
        </div>
        <div class="scroll-horizontal">
            Scroll horizontally to see more
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
                    <h3>Active</h3>
                </div>
                <div class="user-col">
                    <h3>User</h3>
                </div>
                <div class="note-col">
                    <h3>Note</h3>
                </div>
            </div>
            <?php
            require( 'includes/ActivityLogManager.php' );
            $Activity = new ActivityLogManager($conn);
            $rowsPerPage = 15;
            $filters = '';
            $total_count = $Activity->getActivityCount($filters);
            $conn->getPaging($total_count, 1, $rowsPerPage, "");

            $result = $Activity->getActivity( $conn->offset, $rowsPerPage );

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


       <!-- <div class="data-list-footer">
            <div class="data-count">
                Showing 16 of 32 Entries
            </div>
            <div class="data-count-pagi">
                <a href="javascript:void(0)">
                    <img src="<?php /*echo ADMIN_URL*/?>/images/pagi-prev-arrow.png" onmouseover="this.src = '<?php /*echo ADMIN_URL*/?>images/pagi-prev-arrow-hvr.png'" onmouseout="this.src = 'images/pagi-prev-arrow.png'" alt="" />
                </a>
                1/2
                <a href="javascript:void(0)">
                    <img src="images/pagi-next-arrow.png" onmouseover="this.src = 'images/pagi-next-arrow-hvr.png'" onmouseout="this.src = 'images/pagi-next-arrow.png'" alt="" />
                </a>
            </div>
        </div>-->

    </div>
</div>


<?php
require( 'includes/footer_new.php' );
?>
<?php $conn->close(); ?>
