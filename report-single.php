<?php
session_start();
include_once 'header-new.php';
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

    <!-- banner sec start -->
    <section class="report-post-banner banner">
        <div class="container">
            <h1><?php echo $title; ?></h1>
            <p><?php echo $description; ?></p>
            <a href="<?php echo SITE_URL?>/reports.php"><img src="<?php echo SITE_URL?>/images/back-button.png" alt="back-btn"></a>
        </div>
    </section>
    <!-- banner sec end -->

    <!-- reports-post sec start -->
    <section class="report-post-wrap ">
        <div class="container">
            <div class="report-post-thumbnail">
                <img src="<?php echo SITE_URL?>/assets/reports/<?php echo $image; ?>" alt="">
            </div>
            <a href="<?php echo SITE_URL?>/assets/report_docs/<?php echo $report['doc']; ?>" class="report-btn download" download>
                <img src="<?php echo SITE_URL?>/images/report-btn.png" onmouseover="this.src='<?php echo SITE_URL?>/images/report-hvr-btn.png'" onmouseout="this.src='<?php echo SITE_URL?>/images/report-btn.png'" alt="load-more-btn" />
            </a>
        </div>
    </section>
    <!-- reports-post sec end -->
<?php
include_once 'footer-new.php';
