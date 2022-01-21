<?php
include_once 'header.php';
$sql = 'INSERT INTO activity_log SET date_created = NOW(), user_id = ?, activity_type_id = 2, ip_address = ?';
$conn->exec( $sql, array( $_SESSION['user_id'], $_SERVER['REMOTE_ADDR'] ) );

?>

<div class="inner_pg events">

    <div class="pg_banner">
        <div class="container">
            <div class="avo_comm">
                <img src="images/avo_comm_img.png" alt="" />
            </div>
            <h2>EVENTS</h2>
            <p>
                We're always on the move! Check out where we'll be next at an event near you!
            </p>
        </div>
    </div>
    <div class="clear"></div>

    <div class="inner_pg_cnt">
        <div class="container">
            <div class="upcoming_events">
              <?php
              $sql = 'SELECT a.*, b.category FROM events a, event_categories b
                      WHERE a.active = 1 AND a.category_id = b.id AND event_date > NOW() ORDER BY event_date ASC';
              $events = $conn->query( $sql, array() );
              $event_array = array();
              if ( $conn->num_rows() > 0 ) {
                while ( $event = $conn->fetch( $events ) ) {
                  echo '
                    <div class="upcomimg_event">
                        <div class="upcom_ent_dt">
                            <strong class="upcom_ent_day notranslate">' . date( 'd', strtotime( $event['event_date'] ) ) . '</strong>
                            <strong class="upcom_ent_month">' . date( 'F', strtotime( $event['event_date'] ) ) . '</strong>
                            <strong class="upcom_ent_year notranslate">' . date( 'Y', strtotime( $event['event_date'] ) ) . '</strong>
                        </div>
                        <div class="upcoming_event_cnt">
                            <div class="upcoming_event_type trade">&nbsp;</div>
                            <h2><a href="/calendar.php?id=' . $event['id'] . '">' . stripslashes( $event['title'] ) . '</a></h2>
                            <p>' . stripslashes( $event['description'] ) . '</p>
                        </div>
                        <div class="clear"></div>
                    </div>
                    ';
                  $event_array[] = date( 'Y-m-d', strtotime( $event['event_date'] ) );
                  $caption_array[] = stripslashes( str_replace( "'", '&apos;', $event['title'] ) );
                }
              }
              ?>
            </div>

            <div class="event_calendar">
                <div id="event-cal-container" class="calendar-container"></div>
            </div>

            <div class="clear"></div>
        </div>
    </div>

</div>
<div class="clear"></div>
<script src="/js/jquery.simple-calendar.js"></script>
<script>
jQuery(document).ready(function () {
  // Event Demo init
  jQuery("#event-cal-container").simpleCalendar({
      events: ['<?php echo implode( "','", $event_array ); ?>'],
      eventsInfo: ['<?php echo implode( "','", $caption_array ); ?>'],
      days: ['S', 'M', 'T', 'W', 'T', 'F', 'S'],
      fixedStartDay: true,
      selectCallback: function (date) {
          console.log('date selected ' + date);
      }
  });
});
</script>
<?php
include_once 'footer.php';
