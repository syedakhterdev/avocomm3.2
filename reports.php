<?php
session_start();
include_once 'header-new.php';
?>

    <!-- banner sec start -->
    <section class="report-banner banner">
        <div class="container">
            <h1>REPORTS</h1>
            <p>Check out the reports on all the efforts from Avocados From Mexico in the market.</p>
            <a href="<?php echo SITE_URL?>/main.php"><img src="<?php echo SITE_URL?>/images/back-button.png" alt="back-btn"></a>
        </div>
    </section>
    <!-- banner sec end -->

    <!-- reports sec start -->
    <section class="report-wrap">
        <div class="container">
            <img class="avn-arrow-left" src="<?php echo SITE_URL?>/images/avn-arrow-left.png" alt="">
            <img class="avn-arrow-right" src="<?php echo SITE_URL?>/images/avn-arrow-right.png" alt="">
            <div class="report-inner">

                 <?php
                  $sql = 'SELECT * FROM reports WHERE period_id = ? ORDER BY sort, date_created DESC';
                  $reports = $conn->query( $sql, array( $_SESSION['user_period_id'] ) );
                  if ( $conn->num_rows() > 0 ) {
                    while ( ( $report = $conn->fetch( $reports ) ) ) {
                      $image = $report['image'] ? $report['image'] : 'no_image.png';
                ?>

                <div class="report-card">
                    <div class="thumbnail">
                        <img src="<?php echo SITE_URL?>/timThumb.php?src=<?php echo SITE_URL?>/assets/reports/<?php echo $image?>&w=290&h=165&zc=1" alt="<?php echo stripslashes( $report['title'] )?>">
                    </div>
                    <div class="report-card-detail">
                        <p><?php echo stripslashes( $report['title'] )?></p>
                        <a title="<?php echo stripslashes( $report['title'] )?>" href="report-single.php?id=<?php echo $report['id']?>" class="learn-more-btn">
                            <img src="<?php echo SITE_URL?>/images/learn-more-btn.png" onmouseover="this.src='<?php echo SITE_URL?>/images/learn-more-hvr-btn.png'" onmouseout="this.src='<?php echo SITE_URL?>/images/learn-more-btn.png'" alt="read-more-btn" />
                        </a>
                    </div>
                </div>
                <?php
                 }
                  } else {?>
                      <div class="report-card">
                          <p class="not-found">No reports found.</p>
                      </div>
                  <?php }
                 ?>
            </div>
            <!--<a href="#" class="load-more-btn">
                <img src="images/load-more-btn.png" onmouseover="this.src='images/load-more-hvr-btn.png'" onmouseout="this.src='images/load-more-btn.png'" alt="load-more-btn" />
            </a>-->
        </div>
    </section>
    <!-- reports sec end -->
    <script src="<?php echo SITE_URL?>/js/report.js"></script>
<?php
include_once 'footer-new.php';

