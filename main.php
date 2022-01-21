<?php
session_start();
include_once 'header.php';
?>

<div class="pg_banner">
    <div class="container">
        <div class="avo_comm">
            <img src="images/avo_comm_img.png" alt="" />
        </div>
        <p>
            Stay up to date with the latest news happening at<br> Avocados From Mexico.
        </p>
    </div>
</div>
<div class="clear"></div>

<div class="call_actions">
    <div class="container">
        <div class="call_action trade">
            <a href="trademonthlyreport.php" class="inner notranslate">Trade</a>
        </div>
        <?php
        $sql_shop = 'SELECT s.*,u.shopper_program_id,u.period_id FROM shopper_programs as s INNER JOIN shopper_program_updates as u ON s.id=u.shopper_program_id   WHERE active = 1 and u.period_id = '.$_SESSION['user_period_id'].' ORDER BY start_date, title';
            $progs = $conn->query($sql_shop, array());
            if ($conn->num_rows() > 0) {?>
        <div class="call_action shopper">
            <a href="shopperhubpage.php" class="inner notranslate">Shopper</a>
        </div>
         <?php }?>
        <!--div class="call_action foodservice">
            <a href="/fdhubpage.php" class="inner notranslate">FoodService</a>
        </div-->
        <div class="call_action reports">
            <a href="reports.php" class="inner notranslate">Reports</a>
        </div>
        <div class="clear"></div>
    </div>
</div>
<div class="clear"></div>
<?php
$sql = 'SELECT * FROM news WHERE active = 1 AND period_id = ? ORDER BY date_created DESC LIMIT 4';
$news = $conn->query($sql, array($_SESSION['user_period_id']));
if ($conn->num_rows() > 0) {?>
<div class="avo_ind_news">
    <div class="container">
        <h2>Avocado Industry News</h2>
        <p>Explore the latest Avocado Industry news here!</p>
        <div class="avo_news_grid">

            <?php
                while ($new = $conn->fetch($news)) {
                    $image = !empty($new['image']) ? '/timThumb.php?src=/assets/news/' . $new['image'] . '&w=221&h=178&zc=1' : '/assets/news/no_photo.png';
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
                    }
                    
                    echo '
                            <div class="sgl_news">
                                <div class="news_img">
                                    <a href="' . $new['url'] . '" target="main">
                                        <img src="' . $image . '" width="221" height="178" style="object-fit:cover;" />
                                    </a>
                                </div>
                                <div class="news_cnt">
                                    <h2><a href="' . $new['url'] . '" target="main">' . $trim_title . '</a></h2>
                                    <p>' . $trim_cnt . '</p>
                                    <a href="' . $new['url'] . '" target="main" class="news_btn">READ MORE</a>
                                </div>
                                <div class="clear"></div>
                            </div>
                            ';
                }?>

        </div>
        <?php
    $sql_count = 'SELECT * FROM news WHERE active = 1 AND period_id = ? ORDER BY date_created DESC';
    $news_count = $conn->query($sql_count, array($_SESSION['user_period_id']));
    if ($conn->num_rows() > 4) {?>
        <a href="/industrynews.php" class="view_more_btn">view more</a>
    <?php } ?>
    </div>
</div>
<?php }?>
<div class="clear"></div>
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
<div class="clear"></div>

<?php
include_once 'footer.php';
