<?php
include_once 'header.php';
?>

<div class="pg_banner">
    <div class="container">
        <div class="avo_shpshopp"><a href="/main.php">Back</a></div>
        <div class="avo_comm">
            <img src="images/avo_comm_img.png" alt="" />
        </div>
        <h2>Reports</h2>
        <p>Check out the reports on all the efforts from Avocados From Mexico in the market.</p>
    </div>
</div>
<div class="clear"></div>
<div class="avo_reports_sec">
    <div class="container">
        <div class="avo_reports_cnts">
          <?php
          $sql = 'SELECT * FROM reports WHERE period_id = ? ORDER BY sort, date_created DESC';
          $reports = $conn->query( $sql, array( $_SESSION['user_period_id'] ) );
          if ( $conn->num_rows() > 0 ) {
            while ( ( $report = $conn->fetch( $reports ) ) ) {
              $image = $report['image'] ? $report['image'] : 'no_image.png';
              echo '
              <div class="avo_reports_cnt">
                  <div class="avo_reports_img"><a href="report-single.php?id=' . $report['id'] . '" title="' . stripslashes( $report['title'] ) . '"><img src="/timThumb.php?src=/assets/reports/' . $image . '&w=290&h=165&zc=1" width="290" height="165" alt="' . stripslashes( $report['title'] ) . '"></a></div>
                  <div class="avo_reports_title_cnt-sec">
                      <div class="avo_reports-title"><h4><a href="report-single.php?id=' . $report['id'] . '" title="' . stripslashes( $report['title'] ) . '">' . stripslashes( $report['title'] ) . '</a></h4></div>
                  </div>
                  <div class="cal_action"><a href="report-single.php?id=' . $report['id'] . '" title="' . stripslashes( $report['title'] ) . '">Learn More</a></div>
              </div>
              ';
            }
          } else {
            echo '<p class="not-found">No reports found.</p>';
          }
          ?>
        </div>
        <div class="clear"></div>
    </div>
</div>
<div class="clear"></div>
<?php
include_once 'footer.php';

