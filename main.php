<?php
session_start();
include_once 'header-new.php';
?>

<!-- banner sec start -->
<section class="banner-wrap">
    <div class="category-banner banner">
        <div class="container">
            <p>Stay up to date with the latest news happenning at Avocados From Mexico</p>
            <div class="detail-row">
                <a href="<?php echo SITE_URL?>/trademonthlyreport.php" class="detail-card card1">
                    <div class="thumbnail">
                        <img src="<?php echo SITE_URL?>/images/trade.png" alt="trade">
                    </div>
                    <div class="card-title">
                        <h5>trade</h5>
                    </div>
                </a>
                <?php
                $sql_shop = 'SELECT s.*,u.shopper_program_id,u.period_id FROM shopper_programs as s INNER JOIN shopper_program_updates as u ON s.id=u.shopper_program_id   WHERE active = 1 and u.period_id = '.$_SESSION['user_period_id'].' ORDER BY start_date, title';
                $progs = $conn->query($sql_shop, array());
                if ($conn->num_rows() > 0) {?>
                <a href="<?php echo SITE_URL?>/shopperhubpage.php" class="detail-card card2">
                    <div class="thumbnail">
                        <img src="<?php echo SITE_URL?>/images/shopper.png" alt="shopper">
                    </div>
                    <div class="card-title">
                        <h5>shopper</h5>
                    </div>
                </a>
                <?php }?>
                <a href="<?php echo SITE_URL?>/reports.php" class="detail-card card3">
                    <div class="thumbnail">
                        <img src="<?php echo SITE_URL?>/images/report.png" alt="report">
                    </div>
                    <div class="card-title">
                        <h5>reports</h5>
                    </div>
                </a>
                <a href="#" class="detail-card card4">
                    <div class="thumbnail">
                        <img src="<?php echo SITE_URL?>/images/knowledge.png" alt="knowledge">
                    </div>
                    <div class="card-title">
                        <h5>Knowledge</h5>
                        <span>hub</span>
                    </div>
                </a>
            </div>
        </div>
    </div>
</section>
<!-- banner sec end -->


    <!-- avo-industry sec start -->
<?php
$sql = 'SELECT * FROM news WHERE active = 1 AND period_id = ? ORDER BY date_created DESC LIMIT 4';
$news = $conn->query($sql, array($_SESSION['user_period_id']));
if ($conn->num_rows() > 0) {?>
    <section class="avo-industry">
        <div class="container">
            <img class="avn-arrow-left" src="<?php echo SITE_URL?>/images/avn-arrow-left.png" alt="">
            <div class="title-row">
                <h2>AVOCADO <span>INDUSTRY NEWS</span></h2>
                <p>Explore the latest Avocado Industry News here!</p>
            </div>
            <div class="avo-industry-detail">

            <?php
            while ($new = $conn->fetch($news)) {
                $image = !empty($new['image']) ? SITE_URL.'/timThumb.php?src='.SITE_URL.'/assets/news/' . $new['image'] . '&w=221&h=178&zc=1' : SITE_URL.'/assets/news/no_photo.png';
                $title = stripslashes($new['title']);
                $pos = strpos($title, ' ', 25);
                if ($pos == '') {
                    $trim_title = $title;
                } else {
                    $trim_title = substr($title, 0, $pos) . '...';
                }
                $content = stripslashes($new['description']);
                $pos = strpos($content, ' ', 40);
                if ($pos == '') {
                    $trim_cnt = $content;
                } else {
                    $trim_cnt = substr($content, 0, $pos) . '...';
                }?>

                <div class="avn-industry-card">
                    <div class="thumbnail">
                        <img src="<?php echo $image?>" alt="">
                    </div>
                    <div class="avn-card-detail">
                        <h5><?php $trim_title?></h5>
                        <p><?php echo $trim_cnt?></p>
                        <a href="<?php echo $new['url']?>">
                            <img src="<?php echo SITE_URL?>/images/avn-btn.png" onmouseover="this.src='<?php echo SITE_URL?>/images/avn-hvr-btn.png'" onmouseout="this.src='<?php echo SITE_URL?>/images/avn-btn.png'" alt="avn-btn" />
                        </a>
                    </div>
                </div>
                <?php }?>
            </div>
            <img class="avn-arrow-right" src="images/avn-arrow-right.png" alt="">
        </div>

    </section>
<?php }else{?>
    <!-- white-bg-arrow sec start -->
    <div class="white-bg-arrow">
        <img src="images/white-bg-arrow.png" alt="">
    </div>
    <!-- white-bg-arrow sec end -->
    <!-- avo-industry sec end -->
    <?php }?>

<?php

$sql = 'SELECT a.*, b.category FROM events a, event_categories b
                    WHERE a.active = 1 AND a.category_id = b.id AND event_date >= NOW() AND featured = 1
                    ORDER BY event_date ASC LIMIT 0,1';
            $events = $conn->query($sql, array());
if ($conn->num_rows() > 0) {?>
<div class="calender_events">
    <div class="container">
        <h2>CALENDAR OF EVENTS</h2>
        <div class="cal_events">
            <?php

            if ($conn->num_rows() > 0) {
                if ($event = $conn->fetch($events)) {
                    $image = $event['image'] ? '/timThumb.php?src=/assets/events/' . $event['image'] . '&w=541&h272&zc=1' : '/assets/events/no_image.png';
                    echo '
                    <div class="recent_upcoming_event">
                        <div class="recent_upcoming_event_container">
                            <div class="recent_upcoming_event_img">
                                <a href="javascript:void(0)">
                                    <img src="' . $image . '" alt="' . stripslashes($event['title']) . '" />
                                </a>
                            </div>
                            <div class="event_date_cal">
                                <h3><strong class="event_day notranslate">' . date('d', strtotime($event['event_date'])) . '</strong> <strong class="event_month">' . date('F', strtotime($event['event_date'])) . '</strong> <strong class="event_year notranslate">' . date('Y', strtotime($event['event_date'])) . '</strong></h3>
                                <h4><a href="/calendar.php?id=' . $event['id'] . '">Add To <br>Calendar</a></h4>
                                <div class="clear"></div>
                            </div>
                            <div class="event_type"></div>
                            <div class="event_cnt">
                                <h2><a href="events.php">' . stripslashes($event['title']) . '</a></h2>
                                <p>' . stripslashes($event['description']) . '</p>
                            </div>
                        </div>
                    </div>
                    ';
                }
            }
            ?>
            <div class="upcoming_events">
                <?php
                $sql = 'SELECT a.*, b.category FROM events a, event_categories b
                        WHERE a.active = 1 AND a.category_id = b.id AND event_date >= NOW() AND featured = 1
                        ORDER BY event_date ASC LIMIT 1,3';
                $events = $conn->query($sql, array());
                if ($conn->num_rows() > 0) {
                    while ($event = $conn->fetch($events)) {
                        echo '
                        <div class="upcomimg_event">
                            <div class="upcom_ent_dt">
                                <strong class="upcom_ent_day notranslate">' . date('d', strtotime($event['event_date'])) . '</strong>
                                <strong class="upcom_ent_month">' . date('F', strtotime($event['event_date'])) . '</strong>
                                <strong class="upcom_ent_year notranslate">' . date('Y', strtotime($event['event_date'])) . '</strong>
                            </div>
                            <div class="upcoming_event_cnt">
                                <div class="upcoming_event_type trade"></div>
                                <h2><a href="events.php">' . stripslashes($event['title']) . '</a></h2>
                                <p>' . stripslashes($event['description']) . '</p>
                            </div>
                            <div class="clear"></div>
                        </div>
                        ';
                    }?>
                    <div class="view_all_event">
                    <a href="/events.php" class="view_more_btn">View All Events</a>
                    </div>
                <?php }
                ?>

            </div>
            <div class="clear"></div>
        </div>
    </div>
</div>
<?php }?>


<script src="<?php echo SITE_URL?>/js/category.js"></script>

<?php
include_once 'footer-new.php';
