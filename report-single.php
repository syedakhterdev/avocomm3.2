<?php
include_once 'header.php';
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id) {
    $sql = 'SELECT * FROM reports WHERE id = ? AND period_id = ? ORDER BY sort, date_created DESC';
    $reports = $conn->query($sql, array($id, $_SESSION['user_period_id']));
    if ($conn->num_rows() > 0) {
        if (( $report = $conn->fetch($reports))) {
            $image = $report['image'] ? $report['image'] : 'no_image_lg.png';
            $title = stripslashes($report['title']);
            $description = stripslashes($report['description']);
        }
    }
    // log the activity
    $sql = "INSERT INTO activity_log SET date_created = NOW(), user_id = ?, activity_type_id = 6, reference = ?, ip_address = ?";
    $conn->exec( $sql, array($_SESSION['user_id'], $title . ' - ' . $_SESSION['user_period_title'], $_SERVER['REMOTE_ADDR'] ) );
} else {
    $title = 'Report could not be found.';
    $description = 'The specified report could not not found.';
}
?>
<div class="single-report">
    <div class="pg_banner">
        <div class="container">
            <div class="avo_comm">
                <img src="images/avo_comm_img.png" alt="<?php echo $title; ?>" />
            </div>
            <h2><?php echo $title; ?></h2>
            <p><?php echo $description; ?></p>
        </div>
    </div>
    <div class="clear"></div>
    <div class="report_name_sec">
        <div class="container">
            <div class="report_name_img"><a href="#"><img src="/assets/reports/<?php echo $image; ?>"></a></div>
            <?php if ( $report['doc'] != "" ) { ?>
            <div class="report-name-dl"><a href="/assets/report_docs/<?php echo $report['doc']; ?>" download>download</a></div>
            <?php } ?>
        </div>
    </div>
</div>
<?php
include_once 'footer.php';
