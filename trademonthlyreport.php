<?php
session_start();
include_once 'header-new.php';
?>

<!-- banner sec start -->
<section class="summary-banner banner">
    <div class="container">
        <h1>TRADE MONTHLY <span>Summary Report</span></h1>
        <p>Check out the latest activities in our top accounts.</p>
        <a href="<?php echo SITE_URL?>/main.php"><img src="images/back-button.png" alt=""></a>
    </div>
</section>
<!-- banner sec end -->

<!-- trade-month-report sec start -->
<section class="trade-month-report-wrap">
    <div class="container">
        <div class="trade-month-report">

            <?php
              $found = false;
              $sql = 'SELECT a.*, ( SELECT COUNT(*) FROM vendor_updates WHERE vendor_id = a.id AND period_id = ?) AS report_count FROM vendors a WHERE a.tier_id = 1 AND a.active = 1 ORDER BY a.sort, a.title';
              $vendors = $conn->query( $sql, array( $_SESSION['user_period_id'] ) );
              if ( $conn->num_rows() > 0 ) {
                while ( $vendor = $conn->fetch( $vendors ) ) {
                    if ( (int)$vendor['report_count'] > 0 ) {?>


            <div class="trade-month-report-img">
                <a href="trade-partner-single.php?id=<?php echo $vendor['id']?>">
                    <img src="<?php echo SITE_URL?>/timThumb.php?src=<?php echo SITE_URL?>/assets/vendors/<?php echo $vendor['logo']?>&w=287&h=178&zc=1" alt="safeway">
                </a>
            </div>

           <?php $found = true;
            }
            }
            }
            if ( !$found ){?>
                <div class="trade-month-report-img">
                    <a href="#">
                        No vendor information has been added.
                    </a>
                </div>
            <?php }?>


        </div>
    </div>
</section>
<!-- trade-month-report sec end -->

<section class="trade-month-report-wrap-sec">
        <div class="container">
            <div class="trade-month-report">
                <?php
                $sql = 'SELECT a.*, ( SELECT COUNT(*) FROM vendor_updates WHERE vendor_id = a.id AND period_id = ?) AS report_count FROM vendors a WHERE a.tier_id = 2 AND a.active = 1 ORDER BY a.sort, a.title';
                $vendors = $conn->query( $sql, array( $_SESSION['user_period_id'] ) );
                if ( $conn->num_rows() > 0 ) {
                  while ( $vendor = $conn->fetch( $vendors ) ) {
                      if ((int)$vendor['report_count'] > 0) {
                          ?>
                          <div class="trade-month-report-img">
                              <a href="trade-partner-single.php?id=<?php echo $vendor['id'] ?>">
                                  <img src="<?php echo SITE_URL ?>/timThumb.php?src=<?php echo SITE_URL ?>/assets/vendors/<?php echo $vendor['logo'] ?>&w=230&h=143&zc=1"
                                       alt="safeway">
                              </a>
                          </div>
                 <?php
                      }
                  }
                  }
                    ?>
            </div>
        </div>
    </section>
<?php
include_once 'footer-new.php';
