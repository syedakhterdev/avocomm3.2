<?php
session_start();
include_once 'header-new.php';
?>

<!-- banner sec start -->
<section class="trade-post-banner banner">
    <div class="container">
        <h1>NATIONAL SHOPPER <span>Marketing ACTIVITIES</span></h1>
        <p>Learn about all the details and results of our in-store shopper programs.</p>
        <a href="<?php echo SITE_URL?>/main.php"><img src="images/back-button.png" alt="back-btn"></a>
    </div>
</section>
<!-- banner sec end -->

<!-- supporting-tabs sec start -->
<section class="ain-wrap">
    <div class="container">
        <img class="avn-arrow-left" src="<?php echo SITE_URL?>/images/avn-arrow-left.png" alt="">
        <img class="avn-arrow-right" src="<?php echo SITE_URL?>/images/avn-arrow-right.png" alt="">
        <div class="ain-inner">
            <?php
            //$sql = 'SELECT * FROM shopper_programs WHERE active = 1 ORDER BY start_date, title';
            $sql = 'SELECT s.*,u.shopper_program_id,u.period_id FROM shopper_programs as s INNER JOIN shopper_program_updates as u ON s.id=u.shopper_program_id   WHERE active = 1 and u.period_id = '.$_SESSION['user_period_id'].' ORDER BY start_date, title';
            $progs = $conn->query($sql, array());
            if ($conn->num_rows() > 0) {
            while ($prog = $conn->fetch($progs)) {

            $image = $prog['image'] ? SITE_URL.'/assets/shopper_programs/' . $prog['image'] : SITE_URL.'/assets/shopper_programs/no_image.png';
            ?>
            <div class="ain-card">
                <div class="thumbnail">
                    <img src="<?php echo $image?>" alt="">
                </div>
                <h5><?php echo date('M d', strtotime($prog['start_date'])) . ' - ' . date('M d', strtotime($prog['end_date']))?></h5>
                <div class="ain-card-detail">
                    <p><?php echo stripslashes($prog['title'])?></p>
                    <a href="<?php echo SITE_URL?>/shopper-partner-single.php?id=<?php echo $prog['id']?>" class="read-more-btn">
                        <img src="<?php echo SITE_URL?>/images/avn-btn.png" onmouseover="this.src='<?php echo SITE_URL?>/images/avn-hvr-btn.png'" onmouseout="this.src='<?php echo SITE_URL?>/images/avn-btn.png'" alt="read-more-btn" />
                    </a>
                </div>
            </div>
            <?php
            }
            } else {?>
                <div class="ain-card">
                    <h5>No shopper programs have been added.</h5>
                </div>
            <?php }
            ?>
        </div>
        <!--<a href="#" class="load-more-btn">
            <img src="images/load-more-btn.png" onmouseover="this.src='images/load-more-hvr-btn.png'" onmouseout="this.src='images/load-more-btn.png'" alt="load-more-btn" />
        </a>-->
    </div>
</section>
<!-- supporting-tabs sec end -->


<script src="<?php echo SITE_URL?>/js/trade-post.js"></script>

<?php
include_once 'footer-new.php';
