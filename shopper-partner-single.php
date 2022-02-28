<?php
session_start();
include_once 'header-new.php';
$id = isset( $_GET['id'] ) && (int)$_GET['id'] ? (int)$_GET['id'] : 0;
if ( !$id ) die( 'Please provide a valid id!' );
$popups = '';

$sql = 'SELECT * FROM shopper_programs WHERE id = ? AND active = 1';
$progs = $conn->query( $sql, array( $id ) );
if ( $conn->num_rows() > 0 ) {
  $row = $conn->fetch( $progs );

  // log the activity
  $sql = "INSERT INTO activity_log SET date_created = NOW(), user_id = ?, activity_type_id = 3, reference = ?, ip_address = ?";
  $conn->exec( $sql, array( $_SESSION['user_id'], $row['title'] . ' - ' . $_SESSION['user_period_title'], $_SERVER['REMOTE_ADDR'] ) );

} else {
  $err = 'The specified program was not found.';
}
?>


<!-- banner sec start -->
<section class="supporting-doc-banner banner">
    <div class="container">
        <div class="sdb-inner">
            <div class="sdb-col-left">
                <?php
                if ( $row['image'] )
                    $image = SITE_URL.'/timThumb.php?src=/assets/shopper_programs/' . $row['image'] . '&w=160&h=146&zc=1';
                else
                    $image = SITE_URL.'/assets/shopper_programs/no_photo.jpg';
                ?>
                <img src="<?php echo $image; ?>" alt="<?php echo stripslashes( $row['title'] ); ?>">
                <a href="<?php echo SITE_URL?>/shopperhubpage.php" class="trade-btn">Shopper</a>
            </div>
            <div class="sdb-col-right">
                <a href="<?php echo SITE_URL?>/shopperhubpage.php" class="back-btn"><img src="<?php echo SITE_URL?>/images/back-button.png" alt=""></a>
                <p><?php echo stripslashes( $row['intro'] ); ?></p>
                <a href="javascript:window.print();" class="sdb-print-btn">
                    <img src="<?php echo SITE_URL?>/images/print-btn.png" onmouseover="this.src='<?php echo SITE_URL?>/images/print-hvr-btn.png'" onmouseout="this.src='<?php echo SITE_URL?>/images/print-btn.png'" alt="print-btn" />
                </a>
            </div>
        </div>
    </div>
</section>
<!-- banner sec end -->

<!-- date sec start -->
<section class="date">
    <div class="container">
        <div class="date-inner">
            <div class="start-date">
                <span><img src="<?php echo SITE_URL?>/images/flag.png" alt=""></span>
                <p>Start Date: <?php echo date( 'M j, Y', strtotime( $row['start_date'] ) ); ?></p>
            </div>
            <div class="end-date">
                <span><img src="<?php echo SITE_URL?>/images/cross.png" alt=""></span>
                <p>End Date: <?php echo date( 'M j, Y', strtotime( $row['end_date'] ) ); ?></p>
            </div>
        </div>
    </div>
</section>
<!-- date sec end -->
<?php
$sql = 'SELECT a.shopper_program_id, b.title, b.logo FROM shopper_programs_and_partners a, shopper_partners b
                WHERE a.shopper_partner_id = b.id AND b.active = 1 AND a.shopper_program_id = ? ORDER BY b.title';

$partners = $conn->query( $sql, array( $row['id'] ) );
if ( $conn->num_rows() > 0 ) {
?>
<!-- partner sec start -->
<section class="partner">
    <div class="container">
        <h2>PARTNERSHIP <span>WITH:</span></h2>
        <div class="partner-detail">
            <?php

            while ( $partner = $conn->fetch( $partners ) ) {?>
            <div class="partner-card">
                <img src="<?php echo SITE_URL?>/assets/shopper_partners/<?php echo $partner['logo']?>" alt="<?php echo stripslashes( $partner['title'] )?>">
            </div>
            <?php }?>

        </div>
    </div>
</section>
<!-- partner sec end -->
<?php } ?>


<section class="current-trade-marketing-wrap">
    <div class="container">
        <img class="ctm-arrow-left" src="<?php echo SITE_URL?>/images/ctm-arrow-left.png" alt="ctm-arrow-left">
        <img class="ctm-arrow-right" src="<?php echo SITE_URL?>/images/ctm-arrow-right.png" alt="ctm-arrow-right">
        <?php
        $sql = 'SELECT * FROM shopper_program_updates WHERE shopper_program_id = ? AND period_id = ? LIMIT 1';
        $upds = $conn->query( $sql, array( $row['id'], $_SESSION['user_period_id'] ) );
        $update_row =   $conn->num_rows();
        if ( $conn->num_rows() > 0 ) {
        if ( $upd = $conn->fetch( $upds ) ) {?>
        <?php if($upd['description']!=''){?>
        <div class="title-row">
            <h2>PARTNER <span>DESCRIPTION</span></h2>
            <?php echo stripslashes( $upd['description'] )?>
        </div>
        <?php }?>
        <?php if($upd['updates']!=''){?>
            <div class="title-row">
                <h2>UPDATES</h2>
                <?php echo stripslashes( $upd['updates'] )?>
            </div>
        <?php }}
        } else {?>
        <div class="title-row">
            <h2>PARTNER <span>DESCRIPTION</span></h2>
            <p>No description has been added.</p>
        </div>
        <div class="title-row">
            <h2>UPDATES</h2>
            <p>No updates have been added.</p>
        </div>
        <?php }?>
    </div>
</section>

    <?php
    $sql = 'SELECT a.*, c.icon FROM shopper_documentation a, shopper_programs b, document_types c
            WHERE a.shopper_program_id = ? AND a.period_id = ? AND a.shopper_program_id = b.id AND a.document_type_id = c.id ORDER BY a.date_created DESC, title';
    $docs = $conn->query( $sql, array( $row['id'], $_SESSION['user_period_id'] ) );
    if ( $conn->num_rows() > 0  && $update_row>0) {
    ?>
    <!-- supporting-doc-sec start -->
    <section class="supporting-doc-wrap">
        <div class="container">
            <h2>SUPPORTING <span>DOCUMENTATION</span></h2>
            <p>Below, you can select what document you are looking for.</p>
        </div>
    </section>
            <!-- supporting-doc-sec end -->
        <section class="supporting-tabs-wrap">
            <div class="supporting-tabs">
                <div class="container">
                    <ul class="tabs">
                        <li>
                            <a class="active" href="#" data-rel="tab-1">All</a>
                        </li>
                        <li>
                            <a href="#" data-rel="tab-2">Images</a>
                        </li>
                        <li>
                            <a href="#" data-rel="tab-3">Documents</a>
                        </li>
                        <li>
                            <a href="#" data-rel="tab-4">Videos</a>
                        </li>
                        <li>
                            <a href="#" data-rel="tab-5">Audio</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="container">
                <div class="tabs-content">
                <div class="tabs-content-inner" id="tab-1">
                        <?php

                        if ( $conn->num_rows() > 0 ) {
                          while ( $doc = $conn->fetch( $docs ) ) {
                            $image = $doc['image'] ? $doc['image'] : 'no_image.jpg';
                            $icon = $doc['icon'] ? $doc['icon'] : 'no_image.jpg';
                            $ext = pathinfo( $doc['document'], PATHINFO_EXTENSION );
                            echo '
                            <div class="cont">
                                <div class="thumbnail">
                                    <a href="javascript:void(0)" data-id="' . $doc['id'] . '"><img src="'.SITE_URL.'/timThumb.php?src=/assets/shopper_documentation/' . $image . '&w=181&h=130&zc=1" alt="avo_dou_img"></a>
                                    ';

                            if ( $ext == 'mp4' || $ext == 'mpg' || $ext == 'mov' ) {
                              echo '
                                    <a href="javascript:void(0)" data-id="' . $doc['id'] . '" class="shps_douar"></a>
                                  ';
                            }

                            echo '
                                    <a href="javascript:void(0)" data-id="' . $doc['id'] . '" class="shps_douserc"></a>
                                </div>
                                <div class="detail">
                                    <h5><a href="'.SITE_URL.'/assets/shopper_documentation_docs/' . $doc['document'] . '" download>' . stripslashes( $doc['title'] ) . '</a></h5>
                                    <p>' . $doc['description'] . '</p>
                                    <a class="download-btn" href="'.SITE_URL.'/assets/shopper_documentation_docs/'.$doc['document'].'" download><img src="'.SITE_URL.'/images/download-btn.png" onmouseover="this.src=\''.SITE_URL.'/images/download-hvr-btn.png\'" onmouseout="this.src=\''.SITE_URL.'/images/download-btn.png\'" alt="print-btn" /></a>
                                 
                                </div>
        
                            </div>
                            ';

                            $is_video = $ext == 'mov' || $ext == 'mp4' || $ext == 'mpg';
                            $is_audio = $ext == 'mp3' || $ext == 'ogg' || $ext == 'wav';

                            $popups .= '
                            <div id="doc_' . $doc['id'] . '" class="popup_bg" style="display: none;">
                                <div class="container">
                                    <div class="vdo_popup">
                                        <div class="pop_hdr">
                                            <div class="icon">
                                                <img src="'.SITE_URL.'/assets/icons/ico_' . $ext . '.png" alt="' . $ext . '" width="36" height="48" />
                                            </div>
                                            <div class="download">
                                                <a href="'.SITE_URL.'/assets/shopper_documentation_docs/' . $doc['document'] . '" download>Download</a>
                                            </div>
                                            <div class="close">
                                                <strong data-id="' . $doc['id'] . '">Close X</strong>
                                            </div>
                                            <div class="clear"></div>
                                        </div>
                                        ';

                            if ( $is_video ) {
                              $popups .= '
                                          <div class="pop_vdo_sec">
                                            
                                              <div style="margin: 0px auto;width: 450px;background: #000">
                                                <video width="450" height="340" controls style="margin: 0px auto;">
                                                  <source src="'.SITE_URL.'/assets/shopper_documentation_docs/' . $doc['document'] . '" type="video/mp4">
                                                  Your browser does not support the video tag.
                                                </video>
                                              </div>

                                          </div>
                                          ';
                            } elseif ( $is_audio ) {
                              $popups .= '
                                          <div class="pop_vdo_other" style="text-align: center;">
                                              <a href="javascript:void(0)">
                                                  <img src="'.SITE_URL.'/assets/shopper_documentation/' . $image . '" alt="' . htmlspecialchars( stripslashes( $doc['title'] ) ) . '" />
                                                  <br>
                                                  <audio controls>
                                                   <source src="'.SITE_URL.'/assets/shopper_documentation_docs/' . $doc['document'] . '" type="audio/mpeg">
                                                   Your browser does not support the audio element.
                                                 </audio>
                                              </a>
                                          </div>
                                          ';
                            } else {
                              $popups .= '
                                          <div class="pop_vdo_sec_other">
                                              <a href="javascript:void(0)">
                                                  <img src="'.SITE_URL.'/assets/shopper_documentation/' . $image . '" alt="' . htmlspecialchars( stripslashes( $doc['title'] ) ) . '" />
                                              </a>
                                          </div>
                                          ';
                            }

                            $popups .= '
                                        <div class="pop_vdo_cnt">
                                            <h2>' . htmlspecialchars( stripslashes( $doc['title'] ) ) . '</h2>
                                            <p>' . htmlspecialchars( stripslashes( $doc['description'] ) ) . '</p>
                                        </div>
                                   
                                    </div>
                                </div>
                            </div>
                            ';




                          }
                        } else {
                          echo '<p class="no_docs">No media has been uploaded.</p>';
                        }
                        ?>

                </div>

                <div class="tabs-content-inner" id="tab-2" style="display:none">

                    <?php
                      $sql = 'SELECT a.*, c.icon FROM shopper_documentation a, shopper_programs b, document_types c
                              WHERE a.shopper_program_id = ? AND a.period_id = ? AND a.shopper_program_id = b.id AND a.document_type_id = c.id AND a.document_type_id = 1 ORDER BY a.date_created DESC';
                      $docs = $conn->query( $sql, array( $row['id'], $_SESSION['user_period_id'] ) );
                      if ( $conn->num_rows() > 0 ) {
                        while ( $doc = $conn->fetch( $docs ) ) {
                          $image = $doc['image'] ? $doc['image'] : 'no_image.jpg';
                          $icon = $doc['icon'] ? $doc['icon'] : 'no_image.jpg';
                          $ext = pathinfo( $doc['document'], PATHINFO_EXTENSION );
                          echo '
                          <div class="cont">
                              <div class="thumbnail">
                                  <a href="javascript:void(0)" data-id="' . $doc['id'] . '"><img src="'.SITE_URL.'/assets/shopper_documentation/' . $image . '" alt="avo_dou_img"></a>
                                  ';

                          if ( $ext == 'mp4' || $ext == 'mpg' || $ext == 'mov' ) {
                            echo '
                                  <a href="javascript:void(0)" data-id="' . $doc['id'] . '" class="shps_douar"></a>
                                ';
                          }

                          echo '
                                  <a href="javascript:void(0)" data-id="' . $doc['id'] . '" class="shps_douserc"></a>
                              </div>
                              <div class="detail">
                                  <h5><a href="'.SITE_URL.'/assets/shopper_documentation_docs/' . $doc['document'] . '" download>' . stripslashes( $doc['title'] ) . '</a></h5>
                                  <p>' . $doc['description'] . '</p>
                                  <a class="download-btn" href="'.SITE_URL.'/assets/shopper_documentation_docs/'.$doc['document'].'" download><img src="'.SITE_URL.'/images/download-btn.png" onmouseover="this.src=\''.SITE_URL.'/images/download-hvr-btn.png\'" onmouseout="this.src=\''.SITE_URL.'/images/download-btn.png\'" alt="print-btn" /></a>                             
                              </div>
                            
                          </div>
                          ';
                        }
                      } else {
                        echo '<p class="no_docs">No images have been uploaded.</p>';
                      }
                      ?>

                </div>
                <div class="tabs-content-inner" id="tab-3" style="display:none;">
                      <?php
                      $sql = 'SELECT a.*, c.icon FROM shopper_documentation a, shopper_programs b, document_types c
                              WHERE a.shopper_program_id = ? AND a.period_id = ? AND a.shopper_program_id = b.id AND a.document_type_id = c.id AND a.document_type_id IN( 2 ) ORDER BY a.date_created DESC';
                      $docs = $conn->query( $sql, array( $row['id'], $_SESSION['user_period_id'] ) );
                      if ( $conn->num_rows() > 0 ) {
                        while ( $doc = $conn->fetch( $docs ) ) {
                          $image = $doc['image'] ? $doc['image'] : 'no_image.jpg';
                          $icon = $doc['icon'] ? $doc['icon'] : 'no_image.jpg';
                          $ext = pathinfo( $doc['document'], PATHINFO_EXTENSION );
                          echo '
                          <div class="cont">
                              <div class="thumbnail">
                                  <a href="javascript:void(0)" data-id="' . $doc['id'] . '"><img src="'.SITE_URL.'/assets/shopper_documentation/' . $image . '" alt="avo_dou_img"></a>
                                  ';

                          if ( $ext == 'mp4' || $ext == 'mpg' || $ext == 'mov' ) {
                            echo '
                                  <a href="javascript:void(0)" data-id="' . $doc['id'] . '" class="shps_douar"></a>
                                ';
                          }

                          echo '
                                  <a href="javascript:void(0)" data-id="' . $doc['id'] . '" class="shps_douserc"></a>
                              </div>
                              <div class="detail">
                                  <h5><a href="'.SITE_URL.'/assets/shopper_documentation_docs/' . $doc['document'] . '" download>' . stripslashes( $doc['title'] ) . '</a></h5>
                                  <p>' . $doc['description'] . '</p>
                                  <a class="download-btn" href="'.SITE_URL.'/assets/shopper_documentation_docs/'.$doc['document'].'" download><img src="'.SITE_URL.'/images/download-btn.png" onmouseover="this.src=\''.SITE_URL.'/images/download-hvr-btn.png\'" onmouseout="this.src=\''.SITE_URL.'/images/download-btn.png\'" alt="print-btn" /></a>                                
                              </div>
                             
                          </div>
                          ';
                        }
                      } else {
                        echo '<p class="no_docs">No documents have been uploaded.</p>';
                      }
                      ?>
                </div>
                <div class="tabs-content-inner" id="tab-4" style="display:none">
                      <?php
                      $sql = 'SELECT a.*, c.icon FROM shopper_documentation a, shopper_programs b, document_types c
                              WHERE a.shopper_program_id = ? AND a.period_id = ? AND a.shopper_program_id = b.id AND a.document_type_id = c.id AND a.document_type_id = 3 ORDER BY a.date_created DESC';
                      $docs = $conn->query( $sql, array( $row['id'], $_SESSION['user_period_id'] ) );
                      if ( $conn->num_rows() > 0 ) {
                        while ( $doc = $conn->fetch( $docs ) ) {
                          $image = $doc['image'] ? $doc['image'] : 'no_image.jpg';
                          $icon = $doc['icon'] ? $doc['icon'] : 'no_image.jpg';
                          $ext = pathinfo( $doc['document'], PATHINFO_EXTENSION );
                          echo '
                          <div class="cont">
                              <div class="thumbnail">
                                  <a href="javascript:void(0)" data-id="' . $doc['id'] . '"><img src="'.SITE_URL.'/assets/shopper_documentation/' . $image . '" alt="avo_dou_img"></a>
                                  ';

                          if ( $ext == 'mp4' || $ext == 'mpg' || $ext == 'mov' ) {
                            echo '
                                  <a href="javascript:void(0)" data-id="' . $doc['id'] . '" class="shps_douar"></a>
                                ';
                          }

                          echo '
                                  <a href="javascript:void(0)" data-id="' . $doc['id'] . '" class="shps_douserc"></a>
                              </div>
                              <div class="detail">
                                  <h5><a href="'.SITE_URL.'/assets/shopper_documentation_docs/' . $doc['document'] . '" download>' . stripslashes( $doc['title'] ) . '</a></h5>
                                  <p>' . $doc['description'] . '</p>
                                  <a class="download-btn" href="'.SITE_URL.'/assets/shopper_documentation_docs/'.$doc['document'].'" download><img src="'.SITE_URL.'/images/download-btn.png" onmouseover="this.src=\''.SITE_URL.'/images/download-hvr-btn.png\'" onmouseout="this.src=\''.SITE_URL.'/images/download-btn.png\'" alt="print-btn" /></a>
                              </div>
                             
                          </div>
                          ';
                        }
                      } else {
                        echo '<p class="no_docs">No videos have been uploaded.</p>';
                      }
                      ?>

                </div>
                <div class="tabs-content-inner" id="tab-5" style="display:none">
                      <?php
                      $sql = 'SELECT a.*, c.icon FROM shopper_documentation a, shopper_programs b, document_types c
                              WHERE a.shopper_program_id = ? AND a.period_id = ? AND a.shopper_program_id = b.id AND a.document_type_id = c.id AND a.document_type_id = 4 ORDER BY a.date_created DESC';
                      $docs = $conn->query( $sql, array( $row['id'], $_SESSION['user_period_id'] ) );
                      if ( $conn->num_rows() > 0 ) {
                        while ( $doc = $conn->fetch( $docs ) ) {
                          $image = $doc['image'] ? $doc['image'] : 'no_image.jpg';
                          $icon = $doc['icon'] ? $doc['icon'] : 'no_image.jpg';
                          $ext = pathinfo( $doc['document'], PATHINFO_EXTENSION );
                          echo '
                          <div class="cont">
                              <div class="thumbnail">
                                  <a href="javascript:void(0)" data-id="' . $doc['id'] . '"><img src="'.SITE_URL.'/assets/shopper_documentation/' . $image . '" alt="avo_dou_img"></a>
                                  ';

                          if ( $ext == 'mp4' || $ext == 'mpg' || $ext == 'mov' ) {
                            echo '
                                  <a href="javascript:void(0)" data-id="' . $doc['id'] . '" class="shps_douar"></a>
                                ';
                          }

                          echo '
                                  <a href="javascript:void(0)" data-id="' . $doc['id'] . '" class="shps_douserc"></a>
                              </div>
                              <div class="detail">
                                  <h5><a href="'.SITE_URL.'/assets/shopper_documentation_docs/' . $doc['document'] . '" download>' . stripslashes( $doc['title'] ) . '</a></h5>
                                  <p>' . $doc['description'] . '</p>
                                  <a class="download-btn" href="'.SITE_URL.'/assets/shopper_documentation_docs/'.$doc['document'].'" download><img src="'.SITE_URL.'/images/download-btn.png" onmouseover="this.src=\''.SITE_URL.'/images/download-hvr-btn.png\'" onmouseout="this.src=\''.SITE_URL.'/images/download-btn.png\'" alt="print-btn" /></a>
                              </div>
                              
                          </div>
                          ';
                        }
                      } else {
                        echo '<p class="no_docs">No audio has been uploaded.</p>';
                      }
                      ?>
                </div>
        </div>
        </section>
      <?php } ?>

<?php
$sql = 'SELECT SUM( qty ) AS total FROM shopper_program_bin_allocations WHERE period_id = ? AND shopper_program_id = ?';
$sums = $conn->query( $sql, array( $_SESSION['user_period_id'], $row['id'] ) );
if ( $conn->num_rows() > 0 ) {
    if ( $sum = $conn->fetch( $sums ) ) {
        $total = $sum['total'];
    }
} else {
    $total = 0;
}
if($total>0){
?>
<!-- kit option sec start -->
<section class="kit-wrap">
    <div class="container">
        <div class="title-row">
            <h2>KIT <span>OPTIONS</span></h2>
            <p>As Of <?php echo $_SESSION['user_period_title']; ?>- <strong>TOTAL KITS: <?php echo number_format( $total ); ?></strong></p>
        </div>
        <div class="kit-detail">
            <?php
            $sql = 'SELECT a.qty, b.title, b.image FROM shopper_program_bin_allocations a, shopper_program_bins b
                      WHERE a.shopper_program_id = ? AND a.period_id = ? AND a.shopper_program_bin_id = b.id ORDER BY b.sort';
            $bins = $conn->query( $sql, array( $row['id'], $_SESSION['user_period_id'] ) );
            if ( $conn->num_rows() > 0 ) {
            while ( $bin = $conn->fetch( $bins ) ) {
                $image = $bin['image'] ? $bin['image'] : 'no_image.png';
                ?>
            <div class="kit-card">
                <img src="<?php echo SITE_URL?>/timThumb.php?src=<?php echo SITE_URL?>/assets/shopper_program_bins/<?php echo $image?>&w=181&h=113&zc=1" alt="<?php echo stripslashes( $bin['title'] )?>">
                <h5><?php echo stripslashes( $bin['title'] )?></h5>
                <p><?php echo number_format( $bin['qty'], 0 )?><br> <span>ordered</span></p>
            </div>
            <?php }
            } else {?>
            <div class="kit-card">
                <p>No kit options data has been added.</p>
            </div>
            <?php }
            ?>
        </div>
        <div class="slider-counter" id="slider-counter"></div>
    </div>
</section>
<!-- kit option sec end -->
    <?php
}?>

<?php
$sql = 'SELECT * FROM shopper_related_links WHERE shopper_program_id = ? AND period_id = ? ORDER BY sort';
$links = $conn->query( $sql, array( $row['id'], $_SESSION['user_period_id'] ) );
if ( $conn->num_rows() > 0 ) {
?>
<!-- Related link start -->
<section class="partner">
    <div class="container">
        <h2>RELATED LINKS</h2>
        <div class="partner-detail">
            <?php
            if ( $conn->num_rows() > 0 ) {
            while ( $link = $conn->fetch( $links ) ) {
                $image = $link['image'] ? SITE_URL.'/timThumb.php?src=/assets/shopper_related_links/' . $link['image'] . '&w=181&h=113' : SITE_URL.'/assets/shopper_related_links/no_image.png';
            ?>
            <div class="partner-card">
                <img src="<?php echo $image?>" alt="">
            </div>
            <?php }
            } else {?>
                <div class="partner-card">
                    <p class="no_related_links">No related links have been added.</p>
                </div>
            <?php }
            ?>
        </div>
    </div>
</section>
<!-- partner sec end -->
<?php } ?>

<?php
echo $popups;

include_once 'footer-new.php';
?>
<script type="text/javascript">
    jQuery(document).ready(function () {
        $('.vdo_popup .pop_hdr .close strong').click(function () {
            $('#doc_' + $(this).attr( 'data-id' ) ).fadeOut();
            return false;
        });
        $('.thumbnail a ').click(function () {
            console.log($(this).attr( 'data-id' ));
            $( '#doc_' + $(this).attr( 'data-id' ) ).fadeIn();
            $("html, body").animate({scrollTop: 0}, "slow");
            return false;
        });

    });
</script>
<script src="<?php echo SITE_URL?>/js/shopper-summary.js"></script>
