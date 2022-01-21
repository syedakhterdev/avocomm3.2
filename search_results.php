<?php
session_start();
$criteria = isset( $_GET['search'] ) && $_GET['search'] != '' ? $_GET['search'] : '';
include_once 'header.php';
// log the activity
$sql = "INSERT INTO activity_log SET date_created = NOW(), user_id = ?, activity_type_id = 11, reference = ?, ip_address = ?";
$conn->exec( $sql, array( $_SESSION['user_id'], $criteria, $_SERVER['REMOTE_ADDR'] ) );

?>

<div class="pg_banner">
    <div class="container">
        <div class="avo_comm">
            <img src="/images/avo_comm_img.png" alt="" />
        </div>
        <h2>Search Results</h2>
        <p>
            Here are the search results that match your criteria.
        </p>
    </div>
</div>
<div class="clear"></div>
<div class="avo_indus_news_sec search_result_sec">
    <div class="container">
        <div class="avo_indus_news_cnts">

            <?php
            $sql = 'SELECT id, date_created, title, url FROM news WHERE ( title LIKE ? OR description LIKE ? ) AND active = 1 ORDER BY date_created DESC LIMIT 25;';
            $results = $conn->query( $sql, array( '%' . $criteria . '%', '%' . $criteria . '%' ) );

            echo '
            <div class="avo_search_results">
              <h1>News</h1>';

            if ( $conn->num_rows() > 0 ) {
                if($conn->num_rows() > 1) {
                    echo '<h2 class="avo_result_count">' . $conn->num_rows() . ' Results</h2>';
                } else {
                    echo '<h2 class="avo_result_count">' . $conn->num_rows() . ' Result</h2>';
                }

              while ( $result = $conn->fetch( $results ) ) {
                $title = stripslashes( $result['title'] );

                echo '
                  <div class="avo_search_result">
                    <div class="avo_result_date">' . date('m/d/Y', strtotime( $result['date_created'] ) ) . '</div>
                    <div class="avo_result_title"><h4><a href="' . stripslashes( $result['url'] ) .'">' . $title . '</a></h4></div>
                    <p class="avo_result_url"><a href="' . $result['url'] . '">' . $result['url'] . '</a></p>
                  </div>
                ';
              }

            } else {
              echo '<div class="alert alert-primary">No news matched your criteria.</div>';
            }
            echo '</div>';
            ?>

            <?php
            $sql = 'SELECT id, title, description, event_date FROM events WHERE ( title LIKE ? OR description LIKE ? ) AND active = 1 ORDER BY date_created DESC LIMIT 25;';
            $results = $conn->query( $sql, array( '%' . $criteria . '%', '%' . $criteria . '%' ) );

            echo '
            <div class="avo_search_results">
              <h1>Events</h1>
            ';
            if ( $conn->num_rows() > 0 ) {

                if($conn->num_rows() > 1) {
                    echo '<h2 class="avo_result_count">' . $conn->num_rows() . ' Results</h2>';
                } else {
                    echo '<h2 class="avo_result_count">' . $conn->num_rows() . ' Result</h2>';
                }

              while ( $result = $conn->fetch( $results ) ) {
                $title = stripslashes( $result['title'] );

                echo '
                  <div class="avo_search_result">
                    <div class="avo_result_date">' . date('m/d/Y', strtotime( $result['event_date'] ) ) . '</div>
                    <div class="avo_result_title"><h4><a href="/events.php">' . $title . '</a></h4></div>
                  </div>
                ';
              }

            } else {
              echo '<div class="alert alert-primary">No events matched your criteria.</div>';
            }

            echo '</div>';
            ?>

            <?php
            $sql = 'SELECT a.id, a.vendor_id, b.title, b.month, b.year, c.title AS vendor FROM vendor_updates a, periods b, vendors c
                    WHERE ( a.current_marketing_activities LIKE ? OR a.upcoming_marketing_activities LIKE ? OR a.current_shopper_marketing_activities LIKE ? OR a.upcoming_shopper_marketing_activiites LIKE ? OR c.title LIKE ? )
                      AND b.active = 1 AND a.period_id = ? AND a.vendor_id = c.id AND a.period_id = b.id
                    ORDER BY date_created DESC LIMIT 25;';
            $results = $conn->query( $sql, array( '%' . $criteria . '%', '%' . $criteria . '%', '%' . $criteria . '%', '%' . $criteria . '%', '%' . $criteria . '%', $_SESSION['user_period_id'] ) );

            echo '
            <div class="avo_search_results">
              <h1>Trade</h1>
            ';

            if ( $conn->num_rows() > 0 ) {

                if($conn->num_rows() > 1) {
                    echo '<h2 class="avo_result_count">' . $conn->num_rows() . ' Results</h2>';
                } else {
                    echo '<h2 class="avo_result_count">' . $conn->num_rows() . ' Result</h2>';
                }

              while ( $result = $conn->fetch( $results ) ) {
                $title = stripslashes( $result['title'] );

                echo '
                  <div class="avo_search_result">
                    <div class="avo_result_date">' . $result['month'] . '/' . $result['year'] . '</div>
                    <div class="avo_result_title"><h4><a href="trade-partner-single.php?id=' . $result['vendor_id'] . '">' . stripslashes( $result['vendor'] ) . '</a></h4></div>
                  </div>
                ';
              }

            } else {
              echo '<div class="alert alert-primary">No trade updates matched your criteria.</div>';
            }

            echo '</div>';
            ?>

            <?php
            $sql = 'SELECT a.id, a.shopper_program_id, b.title, b.month, b.year, c.title AS shopper_program FROM shopper_program_updates a, periods b, shopper_programs c
                    WHERE ( c.title LIKE ? OR a.description LIKE ? OR a.updates LIKE ? ) AND b.active = 1 AND a.period_id = ? AND a.shopper_program_id = c.id AND a.period_id = b.id
                    ORDER BY c.title DESC LIMIT 25;';
            $results = $conn->query( $sql, array( '%' . $criteria . '%','%' . $criteria . '%', '%' . $criteria . '%', $_SESSION['user_period_id'] ) );

            echo '
            <div class="avo_search_results">
              <h1>Shopper Programs</h1>
            ';

            if ( $conn->num_rows() > 0 ) {

                if($conn->num_rows() > 1) {
                    echo '<h2 class="avo_result_count">' . $conn->num_rows() . ' Results</h2>';
                } else {
                    echo '<h2 class="avo_result_count">' . $conn->num_rows() . ' Result</h2>';
                }

              while ( $result = $conn->fetch( $results ) ) {
                $title = stripslashes( $result['shopper_program'] );

                echo '
                  <div class="avo_search_result">
                    <div class="avo_result_title"><h4><a href="shopper-partner-single.php?id=' . $result['shopper_program_id'] . '">' . $title . '</a></h4></div>
                  </div>
                ';
              }

            } else {
              echo '<div class="alert alert-primary">No shopper program updates matched your criteria.</div>';
            }

            echo '</div>';
            ?>

            <?php
            $sql = 'SELECT id, title, description, date_created FROM reports WHERE ( title LIKE ? OR description LIKE ? ) AND period_id = ? AND active = 1 ORDER BY date_created DESC LIMIT 25;';
            $results = $conn->query( $sql, array( '%' . $criteria . '%', '%' . $criteria . '%', $_SESSION['user_period_id'] ) );

            echo '
            <div class="avo_search_results">
              <h1>Reports</h1>
            ';
            if ( $conn->num_rows() > 0 ) {

                if($conn->num_rows() > 1) {
                    echo '<h2 class="avo_result_count">' . $conn->num_rows() . ' Results</h2>';
                } else {
                    echo '<h2 class="avo_result_count">' . $conn->num_rows() . ' Result</h2>';
                }

              while ( $result = $conn->fetch( $results ) ) {
                $title = stripslashes( $result['title'] );

                echo '
                  <div class="avo_search_result">
                    <div class="avo_result_date">' . date( 'm/d/Y', strtotime( $result['date_created'] ) ) . '</div>
                    <div class="avo_result_title"><h4><a href="/report-single.php?id=' . $result['id'] . '">' . $title . '</a></h4></div>
                  </div>
                ';
              }

            } else {
              echo '<div class="alert alert-primary">No reports matched your criteria.</div>';
            }

            echo '</div>';
            ?>

            <div class="clear"></div>
        </div>
    </div>
</div>

<div class="clear"></div>
<?php
include_once 'footer.php';
