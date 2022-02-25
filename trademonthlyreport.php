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
        <div class="trade-month-report trade-month-report1">

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
        <div class="slider-counter" id="slider-counter"></div>
    </div>
</section>
<!-- trade-month-report sec end -->

<section class="trade-month-report-wrap-sec">
        <div class="container">
            <div class="trade-month-report trade-month-report2">
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
            <div class="slider-counter" id="slider-counter2"></div>
        </div>
    </section>

<script>
    /*trade slider css start */
    supporting_slider();
    $(window).resize(function() {
        supporting_slider();
    });

    function supporting_slider() {

        scr_width = $(window).width();

        if (scr_width < 601) {
            $('.trade-month-report1').on('initialized.owl.carousel changed.owl.carousel', function(e) {
                if (!e.namespace) {
                    return;
                }
                var carousel = e.relatedTarget;
                $('#slider-counter').text(carousel.relative(carousel.current()) + 1 + '/' + carousel.items().length);
            })
            $('.trade-month-report1').addClass('owl-carousel')
            $('.trade-month-report1').owlCarousel({
                center: true,
                items: 1.15,
                loop: true,
                margin: 0,
            });
        } else {

            $('.trade-month-report1').trigger('destroy.owl.carousel');
            $('.trade-month-report1').removeClass('owl-carousel');
        }

    }




    supporting_slider2();
    $(window).resize(function() {
        supporting_slider2();
    });

    function supporting_slider2() {

        scr_width = $(window).width();

        if (scr_width < 601) {
            $('.trade-month-report2').on('initialized.owl.carousel changed.owl.carousel', function(e) {
                if (!e.namespace) {
                    return;
                }
                var carousel = e.relatedTarget;
                $('#slider-counter2').text(carousel.relative(carousel.current()) + 1 + '/' + carousel.items().length);
            })
            $('.trade-month-report2').addClass('owl-carousel')
            $('.trade-month-report2').owlCarousel({
                center: true,
                items: 1.15,
                loop: true,
                margin: 0,
            });
        } else {

            $('.trade-month-report2').trigger('destroy.owl.carousel');
            $('.trade-month-report2').removeClass('owl-carousel');
        }

    }

    /*trade slider css end */
</script>

<?php
include_once 'footer-new.php';
