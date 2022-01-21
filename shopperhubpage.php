<?php
include_once 'header.php';
?>

<div class="pg_banner">
    <div class="container">

        <div class="avo_comm">
            <a href="/main.php"><img src="images/avo_comm_img.png" alt="" /></a>
        </div>
        <h3>NATIONAL SHOPPER MARKETING ACTIVITIES</h3>
        <p>Learn about all the details and results of our in-store shopper programs.</p>
        <div class="avo_shpshopp"><a href="/main.php">Back</a></div>
    </div>
</div>
<div class="clear"></div>

<div class="shopper_hubpage_sec">
    <div class="container">
        <div class="shper_logo-secs">
            <?php
            //$sql = 'SELECT * FROM shopper_programs WHERE active = 1 ORDER BY start_date, title';
            $sql = 'SELECT s.*,u.shopper_program_id,u.period_id FROM shopper_programs as s INNER JOIN shopper_program_updates as u ON s.id=u.shopper_program_id   WHERE active = 1 and u.period_id = '.$_SESSION['user_period_id'].' ORDER BY start_date, title';
            $progs = $conn->query($sql, array());
            if ($conn->num_rows() > 0) {
                while ($prog = $conn->fetch($progs)) {
                    //$image = $prog['image'] ? '/timThumb.php?src=/assets/shopper_programs/' . $prog['image'] : '/assets/shopper_programs/no_image.png';
                    $image = $prog['image'] ? '/assets/shopper_programs/' . $prog['image'] : '/assets/shopper_programs/no_image.png';
                    echo '
              <div class="shper_logo-sec">
                  <div class="shper_img">
                      <a href="shopper-partner-single.php?id=' . $prog['id'] . '" title="' . stripslashes($prog['title']) . '">
                          <img height="202px" src="' . $image . '" alt="' . stripslashes($prog['title']) . '" />
                      </a>
                  </div>
                  <div class="shper_title">
                      <h5>' . stripslashes($prog['title']) . '</h5>
                  </div>
                  <div class="shper_date">
                      <h5>' . date('M d', strtotime($prog['start_date'])) . ' - ' . date('M d', strtotime($prog['end_date'])) . '</h5>
                  </div>
              </div>
              ';
                }
            } else {
                echo '<div class="no_shopper_programs">No shopper programs have been added.</div>';
            }
            ?>
            <div class="clear"></div>
            <p>Partners  are subject to change.</p>
            <div class="clear"></div>
        </div>
    </div>
</div>
<div class="clear"></div>
<?php
include_once 'footer.php';
