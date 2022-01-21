<?php
session_start();
include_once 'header.php';
$vendor_id = isset( $_GET['id'] ) ? (int)$_GET['id'] : 0;
$popups = '';

$sql = 'SELECT a.*, b.id, b.title, b.logo FROM vendor_updates a, vendors b WHERE a.period_id = ? AND a.vendor_id = ? AND a.vendor_id = b.id';
if ( $vendor_id ) {
  $vendors = $conn->query( $sql, array( $_SESSION['user_period_id'], $vendor_id ) );
  if ( $conn->num_rows() > 0 ) {
    $vendor = $conn->fetch( $vendors );

    // log the activity
    $sql = "INSERT INTO activity_log SET date_created = NOW(), user_id = ?, activity_type_id = 7, reference = ?, ip_address = ?";
    $conn->exec( $sql, array( $_SESSION['user_id'], $vendor['title'] . ' - ' . $_SESSION['user_period_title'], $_SERVER['REMOTE_ADDR'] ) );
  }
} else {
  $err = "Please specify a vendor ID.";
}
?>

<div class="pg_banner avo_shps_banner">
    <div class="container">
        <div class="avo_shpshopp"><a href="https://avocomm.avocadosfrommexico.com/trademonthlyreport.php">Trade</a></div>
        <div class="avo_comm">
            <img src="images/avo_comm_img.png" alt="" />
        </div>
        <div class="avo_sopimg">
            <div class="">
                <img src="/timThumb.php?src=/assets/vendors/<?php echo $vendor['logo']; ?>&w=182" width="182" alt="shopper_logo">
            </div>
            <!--<h4>LOREM IPSUM IS LOREM IPSUM</h4>-->
        </div>
        <div class="avo_sopcnt">
            <p>Below you will find trade and shopper marketing activities at <?php echo stripslashes( $vendor['title'] ); ?>. You'll also find supporting documentation and links.</p>
            <div class="avp_sopbtn trd_prt"><a href="javascript:window.print();">PRINT</a></div>
        </div>
        <div class="clear"></div>
    </div>
</div>

<div class="shopper_partner_sec trd_prt_sec">
    <div class="container">

        <?php if ( $vendor['current_marketing_activities'] ) { ?>
        <div class="shps_customer org">
            <h2>CURRENT TRADE MARKETING ACTIVITIES</h2>
            <div class="shps_cuscnt">
                <p><?php echo stripslashes( $vendor['current_marketing_activities'] ); ?></p>
            </div>
        </div>
        <?php } ?>

        <?php if ( $vendor['upcoming_marketing_activities'] ) { ?>
        <div class="shps_updates org">
            <h2>UPCOMING TRADE MARKETING ACTIVITIES</h2>
            <div class="shps_updatecnt">
              <p><?php echo stripslashes( $vendor['upcoming_marketing_activities'] ); ?></p>
            </div>
        </div>
        <?php } ?>

        <?php if ( $vendor['current_shopper_marketing_activities'] ) { ?>
        <div class="shps_customer blue">
            <h2>CURRENT SHOPPER MARKETING ACTIVITIES</h2>
            <div class="shps_cuscnt">
              <p><?php echo stripslashes( $vendor['current_shopper_marketing_activities'] ); ?></p>
            </div>
        </div>
        <?php } ?>

        <?php if ( $vendor['upcoming_shopper_marketing_activiites'] ) { ?>
        <div class="shps_updates blue">
            <h2>UPCOMING SHOPPER MARKETING ACTIVITIES</h2>
            <div class="shps_updatecnt">
              <p><?php echo stripslashes( $vendor['upcoming_shopper_marketing_activiites'] ); ?></p>
            </div>
        </div>
        <?php } ?>

        <?php
        if ( $vendor ) {
          $sql = 'SELECT a.*, c.icon FROM vendor_documentation a, vendors b, document_types c
                  WHERE a.vendor_id = ? AND a.period_id = ? AND a.vendor_id = b.id AND a.documen_type_id = c.id ORDER BY a.date_created DESC, title';
          $docs = $conn->query( $sql, array( $vendor['id'], $_SESSION['user_period_id'] ) );

          if ( $conn->num_rows() > 0 ) {
        ?>
        <div class="avo_support_docu">
            <div class="avo_doutop">
                <h2>SUPPORTING DOCUMENTATION</h2>
                <ul class="tabs">
                    <li class="selected" id="tab-1"><a> ALL  </a></li>
                    <li id="tab-2"><a>images</a></li>
                    <li id="tab-3"><a>documents </a></li>
                    <li id="tab-4"><a>videoS</a></li>
                    <li id="tab-5"><a>AUDIO</a></li>
                </ul>
                <div class="clear"></div>
            </div>
            <div class="tab-content">
                <div class="cont tab-1">
                    <div class="">
                      <?php

                      if ( $conn->num_rows() > 0 ) {

                        while ( $doc = $conn->fetch( $docs ) ) {

                            if(!empty($doc['image']) && empty($doc['document'])){
                                $image = $doc['image'] ? $doc['image'] : 'no_image.jpg';
                                $image_link =   '/timThumb.php?src=/assets/documentation_images/' . $image . '&w=181&h=130&zc=1';
                                $download_links =   '/assets/documentation_images/' . $doc['image'] . '';
                            }
                            elseif (empty($doc['image']) && !empty($doc['document'])){
                                $image = $doc['document'] ? $doc['document'] : 'no_image.jpg';
                                $image_link =   '/timThumb.php?src=/assets/documentation_docs/' . $image . '&w=181&h=130&zc=1';
                                $download_links =   '/assets/documentation_docs/' . $doc['document'] . '';
                            }
                            elseif (!empty($doc['image']) && !empty($doc['document'])){
                                $image = $doc['document'] ? $doc['document'] : 'no_image.jpg';
                                $image_link =   '/timThumb.php?src=/assets/documentation_docs/' . $image . '&w=181&h=130&zc=1';
                                $download_links =   '/assets/documentation_docs/' . $doc['document'] . '';
                            }
                            else{

                                $image = 'no_image.jpg';
                                $image_link =   '/timThumb.php?src=/assets/documentation_docs/' . $image . '&w=181&h=130&zc=1';
                                $download_links =   '/assets/documentation_docs/' . $doc['document'] . '';
                            }

                          $icon = $doc['icon'] ? $doc['icon'] : 'no_image.jpg';
                          if ( !empty($doc['document']) || !empty($doc['image']) ) {
                            $ext = pathinfo( $doc['document'], PATHINFO_EXTENSION );
                            echo '
                            <div class="avo_shpsdoc">
                                <div class="avo_shpsdou_img">
                                
                                    <a href="javascript:void(0)" data-id="' . $doc['id'] . '"><img src="'.$image_link.'" alt="avo_dou_img"></a>
                                    ';

                            if ( $ext == 'mp4' || $ext == 'mpg' || $ext == 'mov' ) {
                              echo '
                                    <a href="javascript:void(0)" data-id="' . $doc['id'] . '" class="shps_douar"></a>
                                  ';
                            }

                            echo '
                                    <a href="javascript:void(0)" data-id="' . $doc['id'] . '" class="shps_douserc"></a>
                                </div>';
                            echo '
                                <div class="avo_shpsdou_cnt">
                                    <h4><a href="'.$download_links.'" download>' . stripslashes( $doc['title'] ) . '</a></h4>
                                    <p>' . $doc['description'] . '</p>
                                    <img src="/assets/icons/ico_' . $ext . '.png" alt="' . strtoupper( $ext ) . '" width="36" height="48"><a href="'.$download_links.'" download>DOWNLOAD</a>
                                </div>';

                            echo '
                                <div class="clear"></div>
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
                                                <img src="/assets/icons/ico_' . $ext . '.png" alt="' . $ext . '" width="36" height="48" />
                                            </div>
                                            <div class="download">
                                                <a href="'.$download_links.'" download>Download</a>
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
                                              <!--a href="javascript:void(0)">
                                                  <img src="/assets/documentation_images/' . $image . '" alt="' . htmlspecialchars( stripslashes( $doc['title'] ) ) . '" />
                                              </a-->
                                              <div style="margin: 0px auto;width: 450px;background: #000">
                                                <video width="450" height="340" controls style="margin: 0px auto;">
                                                  <source src="/assets/documentation_docs/' . $doc['document'] . '" type="video/mp4">
                                                  Your browser does not support the video tag.
                                                </video>
                                              </div>

                                          </div>
                                          ';
                            } elseif ( $is_audio ) {
                              $popups .= '
                                          <div class="pop_vdo_other" style="text-align: center;">
                                              <a href="javascript:void(0)">
                                                  <img src="/assets/documentation_images/' . $image . '" alt="' . htmlspecialchars( stripslashes( $doc['title'] ) ) . '" />
                                                  <br>
                                                  <audio controls>
                                                   <source src="/assets/documentation_docs/' . $doc['document'] . '" type="audio/mpeg">
                                                   Your browser does not support the audio element.
                                                 </audio>
                                              </a>
                                          </div>
                                          ';
                            } else {
                              $popups .= '
                                          <div class="pop_vdo_sec_other">
                                              <a href="javascript:void(0)">
                                                  <img src="/assets/documentation_images/' . $image . '" alt="' . htmlspecialchars( stripslashes( $doc['title'] ) ) . '" />
                                              </a>
                                          </div>
                                          ';
                            }

                            $popups .= '
                                        <div class="pop_vdo_cnt">
                                            <h2>' . htmlspecialchars( stripslashes( $doc['title'] ) ) . '</h2>
                                            <p>' . htmlspecialchars( stripslashes( $doc['description'] ) ) . '</p>
                                        </div>
                                        <!--div class="pop_vdo_nav">
                                            <a href="javascript:void(0)" class="prev">Previous</a>
                                            <a href="javascript:void(0)" class="next">Next</a>
                                        </div-->
                                    </div>
                                </div>
                            </div>
                            ';
                          }




                        }
                      } else {
                        echo '<p class="no_docs">No media has been uploaded.</p>';
                      }
                      ?>
                        <div class="clear"></div>
                    </div>
                </div>
                <div class="cont tab-2" style="display:none">

                    <div class="">
                      <?php
                      $sql = 'SELECT a.*, c.icon FROM vendor_documentation a, vendors b, document_types c
                              WHERE a.vendor_id = ? AND a.period_id = ? AND a.vendor_id = b.id AND a.documen_type_id = c.id AND a.documen_type_id = 1 ORDER BY a.date_created DESC';
                      $docs = $conn->query( $sql, array( $vendor['id'], $_SESSION['user_period_id'] ) );
                      if ( $conn->num_rows() > 0 ) {
                        while ( $doc = $conn->fetch( $docs ) ) {
                            if(!empty($doc['image']) && empty($doc['document'])){
                                $image = $doc['image'] ? $doc['image'] : 'no_image.jpg';
                                $image_link =   '/timThumb.php?src=/assets/documentation_images/' . $image . '&w=181&h=130&zc=1';
                                $download_links =   '/assets/documentation_images/' . $doc['image'] . '';
                            }
                            elseif (empty($doc['image']) && !empty($doc['document'])){
                                $image = $doc['document'] ? $doc['document'] : 'no_image.jpg';
                                $image_link =   '/timThumb.php?src=/assets/documentation_docs/' . $image . '&w=181&h=130&zc=1';
                                $download_links =   '/assets/documentation_docs/' . $doc['document'] . '';
                            }
                            elseif (!empty($doc['image']) && !empty($doc['document'])){
                                $image = $doc['document'] ? $doc['document'] : 'no_image.jpg';
                                $image_link =   '/timThumb.php?src=/assets/documentation_docs/' . $image . '&w=181&h=130&zc=1';
                                $download_links =   '/assets/documentation_docs/' . $doc['document'] . '';
                            }
                            else{

                                $image = 'no_image.jpg';
                                $image_link =   '/timThumb.php?src=/assets/documentation_docs/' . $image . '&w=181&h=130&zc=1';
                                $download_links =   '/assets/documentation_docs/' . $doc['document'] . '';
                            }
                          $icon = $doc['icon'] ? $doc['icon'] : 'no_image.jpg';

                          if ( !empty($doc['document']) || !empty($doc['image']) ) {
                            $ext = pathinfo( $doc['document'], PATHINFO_EXTENSION );
                            echo '
                            <div class="avo_shpsdoc">
                                <div class="avo_shpsdou_img">
                                    <a href="javascript:void(0)" data-id="' . $doc['id'] . '"><img src="'.$image_link.'" alt="avo_dou_img"></a>
                                    ';

                            if ( $ext == 'mp4' || $ext == 'mpg' || $ext == 'mov' ) {
                              echo '
                                    <a href="javascript:void(0)" data-id="' . $doc['id'] . '" class="shps_douar"></a>
                                  ';
                            }

                            echo '
                                    <a href="javascript:void(0)" data-id="' . $doc['id'] . '" class="shps_douserc"></a>
                                </div>
                                <div class="avo_shpsdou_cnt">
                                    <h4><a href="'.$download_links.'" download>' . stripslashes( $doc['title'] ) . '</a></h4>
                                    <p>' . $doc['description'] . '</p>
                                    <img src="/assets/icons/ico_' . $ext . '.png" alt="' . strtoupper( $ext ) . '" width="36" height="48"><a href="'.$download_links.'" download>DOWNLOAD</a>
                                </div>
                                <div class="clear"></div>
                            </div>
                            ';
                          }
                        }
                      } else {
                        echo '<p class="no_docs">No images have been uploaded.</p>';
                      }
                      ?>

                      <div class="clear"></div>
                    </div>

                </div>
                <div class="cont tab-3" style="display:none;">

                    <div class="">
                      <?php
                      $sql = 'SELECT a.*, c.icon FROM vendor_documentation a, vendors b, document_types c
                              WHERE a.vendor_id = ? AND a.period_id = ? AND a.vendor_id = b.id AND a.documen_type_id = c.id AND a.documen_type_id IN( 2 ) ORDER BY a.date_created DESC';
                      $docs = $conn->query( $sql, array( $vendor['id'], $_SESSION['user_period_id'] ) );

                      if ( $conn->num_rows() > 0 ) {
                        while ( $doc = $conn->fetch( $docs ) ) {
                          $image = $doc['image'] ? $doc['image'] : 'no_image.jpg';
                          $icon = $doc['icon'] ? $doc['icon'] : 'no_image.jpg';

                          if ( $doc['document'] ) {
                            $ext = pathinfo( $doc['document'], PATHINFO_EXTENSION );
                            echo '
                            <div class="avo_shpsdoc">
                                <div class="avo_shpsdou_img">
                                    <a href="javascript:void(0)" data-id="' . $doc['id'] . '"><img src="/assets/documentation_images/' . $image . '" alt="avo_dou_img"></a>
                                    ';

                            if ( $ext == 'mp4' || $ext == 'mpg' || $ext == 'mov' ) {
                              echo '
                                    <a href="javascript:void(0)" data-id="' . $doc['id'] . '" class="shps_douar"></a>
                                  ';
                            }

                            echo '
                                    <a href="javascript:void(0)" data-id="' . $doc['id'] . '" class="shps_douserc"></a>
                                </div>
                                <div class="avo_shpsdou_cnt">
                                    <h4><a href="/assets/documentation_docs/' . $doc['document'] . '" download>' . stripslashes( $doc['title'] ) . '</a></h4>
                                    <p>' . $doc['description'] . '</p>
                                    <img src="/assets/icons/ico_' . $ext . '.png" alt="' . strtoupper( $ext ) . '" width="36" height="48"><a href="/assets/documentation_docs/' . $doc['document'] . '" download>DOWNLOAD</a>
                                </div>
                                <div class="clear"></div>
                            </div>
                            ';
                          }
                        }
                      } else {
                        echo '<p class="no_docs">No documents have been uploaded.</p>';
                      }
                      ?>
                      <div class="clear"></div>
                    </div>
                </div>
                <div class="cont tab-4" style="display:none">

                    <div class="">
                      <?php
                      $sql = 'SELECT a.*, c.icon FROM vendor_documentation a, vendors b, document_types c
                              WHERE a.vendor_id = ? AND a.period_id = ? AND a.vendor_id = b.id AND a.documen_type_id = c.id AND a.documen_type_id = 3 ORDER BY a.date_created DESC';
                      $docs = $conn->query( $sql, array( $vendor['id'], $_SESSION['user_period_id'] ) );
                      if ( $conn->num_rows() > 0 ) {
                        while ( $doc = $conn->fetch( $docs ) ) {
                          $image = $doc['image'] ? $doc['image'] : 'no_image.jpg';
                          $icon = $doc['icon'] ? $doc['icon'] : 'no_image.jpg';

                          if ( $doc['document'] ) {
                            $ext = pathinfo( $doc['document'], PATHINFO_EXTENSION );
                            echo '
                            <div class="avo_shpsdoc">
                                <div class="avo_shpsdou_img">
                                    <a href="javascript:void(0)" data-id="' . $doc['id'] . '"><img src="/assets/documentation_images/' . $image . '" alt="avo_dou_img"></a>
                                    ';

                            if ( $ext == 'mp4' || $ext == 'mpg' || $ext == 'mov' ) {
                              echo '
                                    <a href="javascript:void(0)" data-id="' . $doc['id'] . '" class="shps_douar"></a>
                                  ';
                            }

                            echo '
                                    <a href="javascript:void(0)" data-id="' . $doc['id'] . '" class="shps_douserc"></a>
                                </div>
                                <div class="avo_shpsdou_cnt">
                                    <h4><a href="/assets/documentation_docs/' . $doc['document'] . '" download>' . stripslashes( $doc['title'] ) . '</a></h4>
                                    <p>' . $doc['description'] . '</p>
                                    <img src="/assets/icons/ico_' . $ext . '.png" alt="' . strtoupper( $ext ) . '" width="36" height="48"><a href="/assets/documentation_docs/' . $doc['document'] . '" download>DOWNLOAD</a>
                                </div>
                                <div class="clear"></div>
                            </div>
                            ';
                          }
                        }
                      } else {
                        echo '<p class="no_docs">No videos have been uploaded.</p>';
                      }
                      ?>
                      <div class="clear"></div>

                    </div>
                </div>
                <div class="cont tab-5" style="display:none">

                    <div class="">
                      <?php
                      $sql = 'SELECT a.*, c.icon FROM vendor_documentation a, vendors b, document_types c
                              WHERE a.vendor_id = ? AND a.period_id = ? AND a.vendor_id = b.id AND a.documen_type_id = c.id AND a.documen_type_id = 4 ORDER BY a.date_created DESC';
                      $docs = $conn->query( $sql, array( $vendor['id'], $_SESSION['user_period_id'] ) );
                      if ( $conn->num_rows() > 0 ) {
                        while ( $doc = $conn->fetch( $docs ) ) {
                          $image = $doc['image'] ? $doc['image'] : 'no_image.jpg';
                          $icon = $doc['icon'] ? $doc['icon'] : 'no_image.jpg';

                          if ( $doc['document'] ) {
                            $ext = pathinfo( $doc['document'], PATHINFO_EXTENSION );
                            echo '
                            <div class="avo_shpsdoc">
                                <div class="avo_shpsdou_img">
                                    <a href="javascript:void(0)" data-id="' . $doc['id'] . '"><img src="/assets/documentation_images/' . $image . '" alt="avo_dou_img"></a>
                                    ';

                            if ( $ext == 'mp4' || $ext == 'mpg' || $ext == 'mov' ) {
                              echo '
                                    <a href="javascript:void(0)" data-id="' . $doc['id'] . '" class="shps_douar"></a>
                                  ';
                            }

                            echo '
                                    <a href="javascript:void(0)" data-id="' . $doc['id'] . '" class="shps_douserc"></a>
                                </div>
                                <div class="avo_shpsdou_cnt">
                                    <h4><a href="/assets/documentation_docs/' . $doc['document'] . '" download>' . stripslashes( $doc['title'] ) . '</a></h4>
                                    <p>' . $doc['description'] . '</p>
                                    <img src="/assets/icons/ico_' . $ext . '.png" alt="' . strtoupper( $ext ) . '" width="36" height="48"><a href="/assets/documentation_docs/' . $doc['document'] . '" download>DOWNLOAD</a>
                                </div>
                                <div class="clear"></div>
                            </div>
                            ';
                          }
                        }
                      } else {
                        echo '<p class="no_docs">No audio has been uploaded.</p>';
                      }
                      ?>
                      <div class="clear"></div>

                    </div>
                </div>
                <div class="clear"></div>
            </div>
            <div class="clear"></div>
        </div>
      <?php } ?>
    <?php } ?>


    </div>
</div>

<?php
$sql = 'SELECT * FROM vendor_related_links WHERE vendor_id = ? AND period_id = ? ORDER BY sort';
$links = $conn->query( $sql, array( $vendor['id'], $_SESSION['user_period_id'] ) );
if ( $conn->num_rows() > 0 ) {
?>
<div class="shopper_partner_related">
    <div class="container">
        <h2>RELATED LINKS</h2>
        <?php
        if ( $conn->num_rows() > 0 ) {
          while ( $link = $conn->fetch( $links ) ) {
            $image = $link['image'] ? '/timThumb.php?src=/assets/vendor_related_links/' . $link['image'] . '&w=181&h=113' : '/assets/vendor_related_links/no_image.png';
            echo '
            <div class="avo_shpsrel">
                <div class="avo_shpsrel_img">
                    <a href="' . stripslashes( $link['url'] ) . '">
                        <img src="' . $image . '" alt="avo_relimg" width="181" height="113">
                    </a>
                </div>
                <div class="avo_shpsrel_cnt">
                    <h4><a href="' . stripslashes( $link['url'] ) . '">' . stripslashes( $link['title'] ) . '</a></h4>
                    <p>' . stripslashes( $link['description'] ) . '</p>
                </div>
                <div class="clear"></div>
            </div>
            ';
          }
        } else {
          echo '<p class="no_related_links">No related links have been added.</p>';
        }
        ?>
        <div class="clear"></div>
    </div>
    <div class="clear"></div>
</div>
<?php } ?>


<script type="text/javascript">
  jQuery(document).ready(function () {
    jQuery('.vdo_popup .pop_hdr .close strong').click(function () {
      jQuery('#doc_' + $(this).attr( 'data-id' ) ).fadeOut();
      return false;
    });
    jQuery('.avo_shpsdou_img a ').click(function () {
      jQuery( '#doc_' + $(this).attr( 'data-id' ) ).fadeIn();
      jQuery("html, body").animate({scrollTop: 0}, "slow");
      return false;
    });

    jQuery('.tabs a').click(
      function () {
        var parentId = $(this).closest('li').prop('id');
        jQuery('ul.tabs li').removeClass('selected');
        jQuery('.cont').css('display', 'none');
        jQuery('#' + parentId).addClass('selected');
        jQuery('.' + parentId).css('display', 'block');
      }
    );
  });
</script>

<?php
echo $popups;

include_once 'footer.php';
