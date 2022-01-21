<?php
session_start();
include_once 'header.php';
?>
<div class="pg_banner">
    <div class="container">
        <div class="avo_shpshopp"><a href="/main.php">Back</a></div>
        <div class="avo_comm">
            <img src="images/avo_comm_img.png" alt="" />
        </div>
        <h2>TRADE MONTHLY SUMMARY REPORT</h2>
        <p>Check out the latest activities in our top accounts.</p>
    </div>
</div>
<div class="clear"></div>
<div class="trd-mnth-rpt-sec">
    <div class="container">
        <div class="trd-mnth-rpt">
          <div class="shps_customer org">
            <!--<h2>Tier 1</h2>-->
          </div>
          <?php
          $found = false;
          $sql = 'SELECT a.*, ( SELECT COUNT(*) FROM vendor_updates WHERE vendor_id = a.id AND period_id = ?) AS report_count FROM vendors a WHERE a.tier_id = 1 AND a.active = 1 ORDER BY a.sort, a.title';
          $vendors = $conn->query( $sql, array( $_SESSION['user_period_id'] ) );
          if ( $conn->num_rows() > 0 ) {
            while ( $vendor = $conn->fetch( $vendors ) ) {
              if ( (int)$vendor['report_count'] > 0 ) {
                echo '
                <div class="trd-mnth-rpt-img"><a href="trade-partner-single.php?id=' . $vendor['id'] . '"><img src="/timThumb.php?src=/assets/vendors/' . $vendor['logo'] . '&w=287&h=178&zc=1"/></a></div>
                ';
                $found = true;
              }
              //else
              //  echo '
              //  <div class="trd-mnth-rpt-img"><a href="trade-partner-single.php?id=' . $vendor['id'] . '"><img src="/timThumb.php?src=/assets/vendors/' . $vendor['logo'] . '&w=287&h=178&zc=1" width="287" height="178"/></a><div class="no-activity"><a href="javascript:void(0)">no activity</a></div></div>
              //  ';
            }
          }
          if ( !$found )
            echo '<p class="no_vendors_added">No vendor information has been added.</p>';
          ?>
        </div>
    </div>
</div>
<div class="clear"></div>
<!--<div class="trd-mth-rpt-logo-sec">-->
<div class="trd-mnth-rpt-sec logo-sec">
    <div class="container">
        <div class="trd-mnth-rpt">
            <div class="shps_customer org">
              <!--<h2>Tier 2</h2>-->
            </div>
            <?php
            $sql = 'SELECT a.*, ( SELECT COUNT(*) FROM vendor_updates WHERE vendor_id = a.id AND period_id = ?) AS report_count FROM vendors a WHERE a.tier_id = 2 AND a.active = 1 ORDER BY a.sort, a.title';
            $vendors = $conn->query( $sql, array( $_SESSION['user_period_id'] ) );
            if ( $conn->num_rows() > 0 ) {
              while ( $vendor = $conn->fetch( $vendors ) ) {
                if ( (int)$vendor['report_count'] > 0 )
                  echo '
                  <div class="trd-mnth-rpt-img"><a href="trade-partner-single.php?id=' . $vendor['id'] . '"><img src="/timThumb.php?src=/assets/vendors/' . $vendor['logo'] . '&w=230&h=143&zc=1"/></a></div>
                  ';
                //else
                //  echo '
                //  <div class="trd-mnth-rpt-img"><a href="trade-partner-single.php?id=' . $vendor['id'] . '"><img src="/timThumb.php?src=/assets/vendors/' . $vendor['logo'] . '&w=230&h=143&zc=1" width="230" height="143"/></a><div class="no-activity"><a href="javascript:void(0)">no activity</a></div></div>
                //  ';
              }
            }
            ?>
        </div>
    </div>
    <div class="clear"></div>
</div>
<div class="clear"></div>
<?php
include_once 'footer.php';
