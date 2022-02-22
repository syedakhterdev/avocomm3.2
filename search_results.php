<?php
session_start();
include_once 'header-new.php';
$criteria = isset( $_GET['search'] ) && $_GET['search'] != '' ? $_GET['search'] : '';
$sql = "INSERT INTO activity_log SET date_created = NOW(), user_id = ?, activity_type_id = 11, reference = ?, ip_address = ?";
$conn->exec( $sql, array( $_SESSION['user_id'], $criteria, $_SERVER['REMOTE_ADDR'] ) );

?>

<!-- banner sec start -->
<section class="search-banner banner">
    <div class="container">
        <h1>SEARCH <span>RESULTS</span></h1>
        <p>Here are the search results that match your criteria.</p>
        <a href="<?php echo SITE_URL?>/main.php"><img src="<?php echo SITE_URL?>/images/back-button.png" alt="back-btn"></a>
    </div>
</section>
<!-- banner sec end -->

<section class="search-tabs-wrap">
        <div class="search-tabs">
            <div class="container">
                <ul class="tabs">
                    <li>
                        <a class="active" href="#" data-rel="tab-1">All</a>
                    </li>
                    <li>
                        <a href="#" data-rel="tab-2">News</a>
                    </li>
                    <li>
                        <a href="#" data-rel="tab-3">TRADE</a>
                    </li>
                    <li>
                        <a href="#" data-rel="tab-4">EVENTS</a>
                    </li>
                    <li>
                        <a href="#" data-rel="tab-5">SHOPPER PROGRAMS</a>
                    </li>
                    <li>
                        <a href="#" data-rel="tab-6">REPORTS</a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="container">
            <div class="search-tabs-content">
                <div class="stc-inner" id="tab-1">
                    <div class="news" style="margin-bottom: 48px;">
                        <h2>News</h2>
                        <?php
                        $sql = 'SELECT id, date_created, title, url FROM news WHERE ( title LIKE ? OR description LIKE ? ) AND active = 1 ORDER BY date_created DESC LIMIT 25;';
                        $results = $conn->query( $sql, array( '%' . $criteria . '%', '%' . $criteria . '%' ) );

                        if ( $conn->num_rows() > 0 ) {
                            while ($result = $conn->fetch($results)) {
                                $title = stripslashes($result['title']); ?>
                                <div class="stc-result">
                                    <h5><?php echo $title?></h5>
                                    <span><?php echo date('m/d/Y', strtotime( $result['date_created'] ) )?></span>
                                    <p>
                                        <a class="link"
                                           href="<?php echo stripslashes( $result['url'] )?>"><?php echo stripslashes( $result['url'] )?></a>

                                        <a href="javascript:void(0);" data-url="<?php echo stripslashes( $result['url'] )?>" class="copy-link"><img src="<?php echo SITE_URL?>/images/copy-link-btn.png" alt=""></a>
                                    </p>
                                </div>
                            <?php }
                        }else{?>
                            <div class="stc-result">
                                <p class="no-match">
                                    No news matched your criteria.
                                </p>
                            </div>
                        <?php }?>

                    </div>
                    <div class="events" style="margin-bottom: 48px;">
                        <h2>Events</h2>
                        <?php
                        $sql = 'SELECT id, title, description, event_date FROM events WHERE ( title LIKE ? OR description LIKE ? ) AND active = 1 ORDER BY date_created DESC LIMIT 25;';
                        $results = $conn->query( $sql, array( '%' . $criteria . '%', '%' . $criteria . '%' ) );

                        if ( $conn->num_rows() > 0 ) {
                            while ($result = $conn->fetch($results)) {
                                $title = stripslashes($result['title']);
                                ?>
                                <div class="stc-result">
                                    <div class="col-left">
                                        <h5><?php echo $title?></h5>
                                        <!--<h4>Miami, FL</h4>-->
                                        <span><?php echo date('m/d/Y', strtotime( $result['event_date'] ) )?></span>
                                    </div>
                                    <div class="col-right">
                                        <p><?php echo stripslashes($result['description'])?></p>
                                    </div>
                                </div>

                            <?php }
                        }else{?>
                            <div class="stc-result">
                                <p class="no-match">No events matched your criteria.</p>
                            </div>
                        <?php }?>
                    </div>
                    <div class="trade" style="margin-bottom: 48px;">
                        <h2>Trade</h2>
                        <?php
                        $sql = 'SELECT a.id, a.vendor_id, b.title, b.month, b.year, c.title AS vendor FROM vendor_updates a, periods b, vendors c
                        WHERE ( a.current_marketing_activities LIKE ? OR a.upcoming_marketing_activities LIKE ? OR a.current_shopper_marketing_activities LIKE ? OR a.upcoming_shopper_marketing_activiites LIKE ? OR c.title LIKE ? )
                        AND b.active = 1 AND a.period_id = ? AND a.vendor_id = c.id AND a.period_id = b.id
                        ORDER BY date_created DESC LIMIT 25;';
                        $results = $conn->query( $sql, array( '%' . $criteria . '%', '%' . $criteria . '%', '%' . $criteria . '%', '%' . $criteria . '%', '%' . $criteria . '%', $_SESSION['user_period_id'] ) );
                    if ( $conn->num_rows() > 0 ) {
                        while ($result = $conn->fetch($results)) {
                            $title = stripslashes($result['title']);
                            ?>

                            <div class="stc-result">
                                <span><?php echo $result['month'] . '/' . $result['year']?></span>
                                <p><h4><a href="<?php echo SITE_URL?>/trade-partner-single.php?id=<?php echo $result['vendor_id']?>"><?php echo stripslashes( $result['vendor'] )?></a></h4></p>
                            </div>

                        <?php }
                    }else{?>
                        <div class="stc-result">
                            <p class="no-match">No Trade matched your criteria.</p>
                        </div>
                    <?php }?>
                    </div>
                    <div class="shopper-program" style="margin-bottom: 48px;">
                        <h2>Shopper Programs</h2>
                        <div class="stc-result">
                            <div class="shopper-program-inner">

                                <?php
                                $sql = 'SELECT a.id, a.shopper_program_id, b.title, b.month, b.year, c.title AS shopper_program FROM shopper_program_updates a, periods b, shopper_programs c
                                WHERE ( c.title LIKE ? OR a.description LIKE ? OR a.updates LIKE ? ) AND b.active = 1 AND a.period_id = ? AND a.shopper_program_id = c.id AND a.period_id = b.id
                                ORDER BY c.title DESC LIMIT 25;';
                                $results = $conn->query( $sql, array( '%' . $criteria . '%','%' . $criteria . '%', '%' . $criteria . '%', $_SESSION['user_period_id'] ) );
                                if ( $conn->num_rows() > 0 ) {
                                    while ($result = $conn->fetch($results)) {
                                        $title = stripslashes($result['shopper_program']);
                                        ?>

                                        <div class="spi-card">
                                            <h3><a href="<?php echo SITE_URL?>/shopper-partner-single.php?id=<?php echo $result['shopper_program_id']?>"><?php echo $title?></a></h3>
                                        </div>

                                    <?php }
                                }else{?>

                                        <p class="no-match">No shopper program updates matched your criteria.</p>

                                <?php }?>
                            </div>
                        </div>
                    </div>
                    <div class="reports">
                        <h2>Reports</h2>
                        <?php
                        $sql = 'SELECT id, title, description, date_created FROM reports WHERE ( title LIKE ? OR description LIKE ? ) AND period_id = ? AND active = 1 ORDER BY date_created DESC LIMIT 25;';
                        $results = $conn->query( $sql, array( '%' . $criteria . '%', '%' . $criteria . '%', $_SESSION['user_period_id'] ) );
                        if ( $conn->num_rows() > 0 ) {
                            while ($result = $conn->fetch($results)) {
                                $title = stripslashes($result['title']);
                                ?>
                                <div class="stc-result">
                                    <p><?php echo date('m/d/Y', strtotime($result['date_created'])) ?></p>
                                    <p><h4>
                                        <a href="<?php echo SITE_URL ?>/report-single.php?id=<?php echo $result['id'] ?>"><?php echo $title ?></a></p>
                                </div>
                            <?php }
                        }else{?>
                            <div class="stc-result">
                                <p class="no-match">No reports matched your criteria.</p>
                            </div>

                        <?php }?>
                    </div>
                </div>
                <div class="stc-inner" id="tab-2" style="display: none;">
                    <div class="news" style="margin-bottom: 48px;">
                        <h2>News</h2>
                        <?php
                        $sql = 'SELECT id, date_created, title, url FROM news WHERE ( title LIKE ? OR description LIKE ? ) AND active = 1 ORDER BY date_created DESC LIMIT 25;';
                        $results = $conn->query( $sql, array( '%' . $criteria . '%', '%' . $criteria . '%' ) );

                        if ( $conn->num_rows() > 0 ) {
                            while ($result = $conn->fetch($results)) {
                                $title = stripslashes($result['title']); ?>
                                <div class="stc-result">
                                    <h5><?php echo $title?></h5>
                                    <span><?php echo date('m/d/Y', strtotime( $result['date_created'] ) )?></span>
                                    <p>
                                        <a class="link"
                                           href="<?php echo stripslashes( $result['url'] )?>"><?php echo stripslashes( $result['url'] )?></a>

                                        <a href="javascript:void(0);" data-url="<?php echo stripslashes( $result['url'] )?>" class="copy-link"><img src="images/copy-link-btn.png" alt=""></a>
                                    </p>
                                </div>
                            <?php }
                        }else{?>
                            <div class="stc-result">
                                <p class="no-match">
                                    No news matched your criteria.
                                </p>
                            </div>
                        <?php }?>

                    </div>
                </div>
                <div class="stc-inner" id="tab-3" style="display: none;">
                    <div class="trade">
                        <h2>Trade</h2>
                        <?php
                        $sql = 'SELECT a.id, a.vendor_id, b.title, b.month, b.year, c.title AS vendor FROM vendor_updates a, periods b, vendors c
                        WHERE ( a.current_marketing_activities LIKE ? OR a.upcoming_marketing_activities LIKE ? OR a.current_shopper_marketing_activities LIKE ? OR a.upcoming_shopper_marketing_activiites LIKE ? OR c.title LIKE ? )
                        AND b.active = 1 AND a.period_id = ? AND a.vendor_id = c.id AND a.period_id = b.id
                        ORDER BY date_created DESC LIMIT 25;';
                        $results = $conn->query( $sql, array( '%' . $criteria . '%', '%' . $criteria . '%', '%' . $criteria . '%', '%' . $criteria . '%', '%' . $criteria . '%', $_SESSION['user_period_id'] ) );
                        if ( $conn->num_rows() > 0 ) {
                            while ($result = $conn->fetch($results)) {
                                $title = stripslashes($result['title']);
                                ?>

                                <div class="stc-result">
                                    <span><?php echo $result['month'] . '/' . $result['year']?></span>
                                    <p><h4><a href="<?php echo SITE_URL?>/trade-partner-single.php?id=<?php echo $result['vendor_id']?>"><?php echo stripslashes( $result['vendor'] )?></a></h4></p>
                                </div>

                            <?php }
                        }else{?>
                            <div class="stc-result">
                                <p class="no-match">No Trade matched your criteria.</p>
                            </div>
                        <?php }?>
                    </div>
                </div>
                <div class="stc-inner" id="tab-4" style="display: none;">
                    <div class="events">
                        <h2>Events</h2>
                        <?php
                        $sql = 'SELECT id, title, description, event_date FROM events WHERE ( title LIKE ? OR description LIKE ? ) AND active = 1 ORDER BY date_created DESC LIMIT 25;';
                        $results = $conn->query( $sql, array( '%' . $criteria . '%', '%' . $criteria . '%' ) );

                        if ( $conn->num_rows() > 0 ) {
                            while ($result = $conn->fetch($results)) {
                                $title = stripslashes($result['title']);
                                ?>
                                <div class="stc-result">
                                    <div class="col-left">
                                        <h5><?php echo $title?></h5>
                                        <!--<h4>Miami, FL</h4>-->
                                        <span><?php echo date('m/d/Y', strtotime( $result['event_date'] ) )?></span>
                                    </div>
                                    <div class="col-right">
                                        <p><?php echo stripslashes($result['description'])?></p>
                                    </div>
                                </div>

                            <?php }
                        }else{?>
                            <div class="stc-result">
                                <p class="no-match">No events matched your criteria.</p>
                            </div>
                        <?php }?>
                    </div>
                </div>
                <div class="stc-inner" id="tab-5" style="display: none;">
                    <div class="shopper-program">
                        <h2>Shopper Programs</h2>
                        <div class="stc-result">
                            <div class="shopper-program-inner">
                            <?php
                            $sql = 'SELECT a.id, a.shopper_program_id, b.title, b.month, b.year, c.title AS shopper_program FROM shopper_program_updates a, periods b, shopper_programs c
                                    WHERE ( c.title LIKE ? OR a.description LIKE ? OR a.updates LIKE ? ) AND b.active = 1 AND a.period_id = ? AND a.shopper_program_id = c.id AND a.period_id = b.id
                                    ORDER BY c.title DESC LIMIT 25;';
                            $results = $conn->query( $sql, array( '%' . $criteria . '%','%' . $criteria . '%', '%' . $criteria . '%', $_SESSION['user_period_id'] ) );
                            if ( $conn->num_rows() > 0 ) {
                                while ($result = $conn->fetch($results)) {
                                    $title = stripslashes($result['shopper_program']);
                                    ?>

                                    <div class="spi-card">
                                        <h3><a href="<?php echo SITE_URL?>/shopper-partner-single.php?id=<?php echo $result['shopper_program_id']?>"><?php echo $title?></a></h3>
                                    </div>

                                <?php }
                            }else{?>
                                <p class="no-match">No shopper program updates matched your criteria.</p>
                            <?php }?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="stc-inner" id="tab-6" style="display: none;">
                    <div class="reports">
                        <h2>Reports</h2>
                        <?php
                        $sql = 'SELECT id, title, description, date_created FROM reports WHERE ( title LIKE ? OR description LIKE ? ) AND period_id = ? AND active = 1 ORDER BY date_created DESC LIMIT 25;';
                        $results = $conn->query( $sql, array( '%' . $criteria . '%', '%' . $criteria . '%', $_SESSION['user_period_id'] ) );
                        if ( $conn->num_rows() > 0 ) {
                            while ($result = $conn->fetch($results)) {
                                $title = stripslashes($result['title']);
                                ?>
                                <div class="stc-result">
                                    <p><?php echo date('m/d/Y', strtotime($result['date_created'])) ?></p>
                                    <p><h4>
                                        <a href="<?php echo SITE_URL ?>/report-single.php?id=<?php echo $result['id'] ?>"><?php echo $title ?></a></p>
                                </div>
                            <?php }
                        }else{?>
                            <div class="stc-result">
                                <p class="no-match">No reports matched your criteria.</p>
                            </div>

                        <?php }?>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script src="<?php echo SITE_URL?>/js/search.js"></script>
    <script>
        $( ".copy-link" ).click(function() {
            var sampleTextarea = document.createElement("textarea");
            document.body.appendChild(sampleTextarea);
            var copyText = $(this).data('url');
            sampleTextarea.value = copyText; //save main text in it
            sampleTextarea.select(); //select textarea contenrs
            document.execCommand("copy");
            document.body.removeChild(sampleTextarea);

            /* Alert the copied text */
            alert("Copied the text: " + copyText);
        });
    </script>
<?php
include_once 'footer-new.php';
