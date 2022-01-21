<?php
include_once 'header.php';
?>

<div class="pg_banner">
    <div class="container">
        <div class="avo_comm">
            <img src="images/avo_comm_img.png" alt="" />
        </div>
        <h3>FOODSERVICE</h3>
        <p>Check out the latest news for our biggest operators, the 5 Cs, and more.</p>
    </div>
</div>
<div class="clear"></div>
<div class="fdhubpage">
    <div class="call_actions">
        <div class="container">
            <div class="tabs">
                <div class="call_action" data-tab='operator'>
                    <div class="operator">
                        <a href="javascript:void(0)" class="inner">Operators</a>
                    </div>
                    <?php /* ?>
                    <p>Lorem Ipsum is simply dummy text of the printing and type setting industry. the printing and type setting industry.</p>
                    <?php */ ?>
                    <div class="clear"></div>
                </div>
                <div class="call_action" data-tab='five_c'>
                    <div class="five_c">
                        <a href="javascript:void(0)" class="inner">5C's</a>
                    </div>
                    <?php /* ?>
                    <p>Lorem Ipsum is simply dummy text of the printing and type setting industry. the printing and type setting industry.</p>
                    <?php */ ?>
                    <div class="clear"></div>
                </div>
                <div class="call_action" data-tab='others'>
                    <div class="others">
                        <a href="javascript:void(0)" class="inner">Others</a>
                    </div>
                    <?php /* ?>
                    <p>Lorem Ipsum is simply dummy text of the printing and type setting industry. the printing and type setting industry.</p>
                    <?php */ ?>
                    <div class="clear"></div>
                </div>
            </div>
            <div class="clear"></div>

            <div id="operator" class="fdhubpg-logos-sec">
              <?php
              $sql = 'SELECT * FROM fs_programs WHERE active = 1 ORDER BY sort, title';
              $fss = $conn->query( $sql, array() );
              if ( $conn->num_rows() > 0 ) {
                while ( $fs = $conn->fetch( $fss ) ) {
                  $image = $fs['image'] ? '/timThumb.php?src=/assets/fs_programs/' . $fs['image'] . '&w=235&h150&zc=1' : '/assets/fs_programs/no_image.png';
                  echo '
                  <div class="fdhubpg-logos"><a href="foodservice-partner-single.php?id=' . $fs['id'] . '"><img src="' . $image . '" alt="' . stripcslashes( $fs['title'] ) . '" /></a></div>
                  ';
                }
              }
              ?>
            </div>

            <div class="clear"></div>
        </div>
    </div>
    <div class="clear"></div>
</div>
<?php
include_once 'footer.php';
