<?php
include_once 'header.php';
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

<div class="pg_banner avo_shps_banner">
    <div class="container">
        <div class="avo_shpshopp"><a href="https://avocomm.avocadosfrommexico.com/shopperhubpage.php">SHOPPER</a></div>
        <div class="avo_comm">
            <img src="images/avo_comm_img.png" alt="" />
        </div>
        <div class="avo_sopimg">
            <div class="">
              <?php
              if ( $row['image'] )
                $image = '/timThumb.php?src=/assets/shopper_programs/' . $row['image'] . '&w=160&h=146&zc=1';
              else
                $image = '/assets/shopper_programs/no_photo.jpg';
              ?>
                <img src="<?php echo $image; ?>" alt="<?php echo stripslashes( $row['title'] ); ?>" />
            </div>
            <?php /* ?>
            <h4>LOREM IPSUM IS LOREM IPSUM</h4>
            <?php */ ?>
        </div>
        <div class="avo_sopcnt">
          <?php if ( !$err ) { ?>
            <p>
              <?php echo stripslashes( $row['intro'] ); ?>
            </p>
            <div class="avo_sopdt"><strong>Start Date:</strong> <?php echo date( 'M j, Y', strtotime( $row['start_date'] ) ); ?></div> <div class="avo_sopdt avo_en"><strong>End Date: </strong><?php echo date( 'M j, Y', strtotime( $row['end_date'] ) ); ?></div>
            <div class="avp_sopbtn">
                <a class="back-button" href="https://avocomm.avocadosfrommexico.com/shopperhubpage.php">BACK</a>
                <a href="javascript:window.print();">PRINT</a>

            </div>
          <?php
        } else {
          echo "<p>$err</p>";
        }
        ?>

        </div>
        <div class="clear"></div>
    </div>
</div>

<div class="shopper_partner_sec">
    <div class="container">
        <?php
        $sql = 'SELECT a.shopper_program_id, b.title, b.logo FROM shopper_programs_and_partners a, shopper_partners b
                WHERE a.shopper_partner_id = b.id AND b.active = 1 AND a.shopper_program_id = ? ORDER BY b.title';

        $partners = $conn->query( $sql, array( $row['id'] ) );
        if ( $conn->num_rows() > 0 ) {
        ?>
        <div class="shps_partner text_center">
            <h3>PARTNERSHIP WITH</h3>
            <?php

              while ( $partner = $conn->fetch( $partners ) ) {
                echo '
                <div class="shps_partnerlogo"><img src="assets/shopper_partners/' . $partner['logo'] . '" alt="' . stripslashes( $partner['title'] ) . '"></div>
                ';
              }
            ?>
            <div class="clear"></div>
        </div>
        <?php } ?>
        <div class="clear"></div>
        <?php
        $sql = 'SELECT * FROM shopper_program_updates WHERE shopper_program_id = ? AND period_id = ? LIMIT 1';
        $upds = $conn->query( $sql, array( $row['id'], $_SESSION['user_period_id'] ) );
        $update_row =   $conn->num_rows();
        if ( $conn->num_rows() > 0 ) {
          if ( $upd = $conn->fetch( $upds ) ) {?>

            <?php if($upd['description']!=''){?>
                  <div class="shps_customer">
                      <h2>Partner Description</h2>
                      <div class="shps_cuscnt">
                          <?php echo stripslashes( $upd['description'] )?>
                      </div>
                  </div>
           <?php }  ?>

           <?php if($upd['updates']!=''){?>
            <div class="shps_updates">
                <h2>UPDATES</h2>
                <div class="shps_updatecnt">
                   <?php echo stripslashes( $upd['updates'] )?>
                </div>
            </div>

          <?php } }
        } else {
          echo '
          <div class="shps_customer">
              <h2>Partner Description</h2>
              <div class="shps_cuscnt">No description has been added.</div>
          </div>

          <div class="shps_updates">
              <h2>UPDATES</h2>
              <div class="shps_updatecnt">No updates have been added.</div>
          </div>
          ';
        }

        $sql = 'SELECT a.*, c.icon FROM shopper_documentation a, shopper_programs b, document_types c
                WHERE a.shopper_program_id = ? AND a.period_id = ? AND a.shopper_program_id = b.id AND a.document_type_id = c.id ORDER BY a.date_created DESC, title';
        $docs = $conn->query( $sql, array( $row['id'], $_SESSION['user_period_id'] ) );
        if ( $conn->num_rows() > 0  && $update_row>0) {
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
                            $image = $doc['image'] ? $doc['image'] : 'no_image.jpg';
                            $icon = $doc['icon'] ? $doc['icon'] : 'no_image.jpg';
                            $ext = pathinfo( $doc['document'], PATHINFO_EXTENSION );
                            echo '
                            <div class="avo_shpsdoc">
                                <div class="avo_shpsdou_img">
                                    <a href="javascript:void(0)" data-id="' . $doc['id'] . '"><img src="/timThumb.php?src=/assets/shopper_documentation/' . $image . '&w=181&h=130&zc=1" alt="avo_dou_img"></a>
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
                                    <h4><a href="/assets/shopper_documentation_docs/' . $doc['document'] . '" download>' . stripslashes( $doc['title'] ) . '</a></h4>
                                    <p>' . $doc['description'] . '</p>
                                    <img src="/assets/icons/ico_' . $ext . '.png" alt="' . strtoupper( $ext ) . '" width="36" height="48"><a href="/assets/shopper_documentation_docs/' . $doc['document'] . '" download>DOWNLOAD</a>
                                </div>
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
                                                <a href="/assets/shopper_documentation_docs/' . $doc['document'] . '" download>Download</a>
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
                                                  <img src="/assets/shopper_documentation/' . $image . '" alt="' . htmlspecialchars( stripslashes( $doc['title'] ) ) . '" />
                                              </a-->
                                              <div style="margin: 0px auto;width: 450px;background: #000">
                                                <video width="450" height="340" controls style="margin: 0px auto;">
                                                  <source src="/assets/shopper_documentation_docs/' . $doc['document'] . '" type="video/mp4">
                                                  Your browser does not support the video tag.
                                                </video>
                                              </div>

                                          </div>
                                          ';
                            } elseif ( $is_audio ) {
                              $popups .= '
                                          <div class="pop_vdo_other" style="text-align: center;">
                                              <a href="javascript:void(0)">
                                                  <img src="/assets/shopper_documentation/' . $image . '" alt="' . htmlspecialchars( stripslashes( $doc['title'] ) ) . '" />
                                                  <br>
                                                  <audio controls>
                                                   <source src="/assets/shopper_documentation_docs/' . $doc['document'] . '" type="audio/mpeg">
                                                   Your browser does not support the audio element.
                                                 </audio>
                                              </a>
                                          </div>
                                          ';
                            } else {
                              $popups .= '
                                          <div class="pop_vdo_sec_other">
                                              <a href="javascript:void(0)">
                                                  <img src="/assets/shopper_documentation/' . $image . '" alt="' . htmlspecialchars( stripslashes( $doc['title'] ) ) . '" />
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
                      $sql = 'SELECT a.*, c.icon FROM shopper_documentation a, shopper_programs b, document_types c
                              WHERE a.shopper_program_id = ? AND a.period_id = ? AND a.shopper_program_id = b.id AND a.document_type_id = c.id AND a.document_type_id = 1 ORDER BY a.date_created DESC';
                      $docs = $conn->query( $sql, array( $row['id'], $_SESSION['user_period_id'] ) );
                      if ( $conn->num_rows() > 0 ) {
                        while ( $doc = $conn->fetch( $docs ) ) {
                          $image = $doc['image'] ? $doc['image'] : 'no_image.jpg';
                          $icon = $doc['icon'] ? $doc['icon'] : 'no_image.jpg';
                          $ext = pathinfo( $doc['document'], PATHINFO_EXTENSION );
                          echo '
                          <div class="avo_shpsdoc">
                              <div class="avo_shpsdou_img">
                                  <a href="javascript:void(0)" data-id="' . $doc['id'] . '"><img src="/assets/shopper_documentation/' . $image . '" alt="avo_dou_img"></a>
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
                                  <h4><a href="/assets/shopper_documentation_docs/' . $doc['document'] . '" download>' . stripslashes( $doc['title'] ) . '</a></h4>
                                  <p>' . $doc['description'] . '</p>
                                  <img src="/assets/icons/ico_' . $ext . '.png" alt="' . strtoupper( $ext ) . '" width="36" height="48"><a href="/assets/shopper_documentation_docs/' . $doc['document'] . '" download>DOWNLOAD</a>
                              </div>
                              <div class="clear"></div>
                          </div>
                          ';
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
                      $sql = 'SELECT a.*, c.icon FROM shopper_documentation a, shopper_programs b, document_types c
                              WHERE a.shopper_program_id = ? AND a.period_id = ? AND a.shopper_program_id = b.id AND a.document_type_id = c.id AND a.document_type_id IN( 2 ) ORDER BY a.date_created DESC';
                      $docs = $conn->query( $sql, array( $row['id'], $_SESSION['user_period_id'] ) );
                      if ( $conn->num_rows() > 0 ) {
                        while ( $doc = $conn->fetch( $docs ) ) {
                          $image = $doc['image'] ? $doc['image'] : 'no_image.jpg';
                          $icon = $doc['icon'] ? $doc['icon'] : 'no_image.jpg';
                          $ext = pathinfo( $doc['document'], PATHINFO_EXTENSION );
                          echo '
                          <div class="avo_shpsdoc">
                              <div class="avo_shpsdou_img">
                                  <a href="javascript:void(0)" data-id="' . $doc['id'] . '"><img src="/assets/shopper_documentation/' . $image . '" alt="avo_dou_img"></a>
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
                                  <h4><a href="/assets/shopper_documentation_docs/' . $doc['document'] . '" download>' . stripslashes( $doc['title'] ) . '</a></h4>
                                  <p>' . $doc['description'] . '</p>
                                  <img src="/assets/icons/ico_' . $ext . '.png" alt="' . strtoupper( $ext ) . '" width="36" height="48"><a href="/assets/shopper_documentation_docs/' . $doc['document'] . '" download>DOWNLOAD</a>
                              </div>
                              <div class="clear"></div>
                          </div>
                          ';
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
                      $sql = 'SELECT a.*, c.icon FROM shopper_documentation a, shopper_programs b, document_types c
                              WHERE a.shopper_program_id = ? AND a.period_id = ? AND a.shopper_program_id = b.id AND a.document_type_id = c.id AND a.document_type_id = 3 ORDER BY a.date_created DESC';
                      $docs = $conn->query( $sql, array( $row['id'], $_SESSION['user_period_id'] ) );
                      if ( $conn->num_rows() > 0 ) {
                        while ( $doc = $conn->fetch( $docs ) ) {
                          $image = $doc['image'] ? $doc['image'] : 'no_image.jpg';
                          $icon = $doc['icon'] ? $doc['icon'] : 'no_image.jpg';
                          $ext = pathinfo( $doc['document'], PATHINFO_EXTENSION );
                          echo '
                          <div class="avo_shpsdoc">
                              <div class="avo_shpsdou_img">
                                  <a href="javascript:void(0)" data-id="' . $doc['id'] . '"><img src="/assets/shopper_documentation/' . $image . '" alt="avo_dou_img"></a>
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
                                  <h4><a href="/assets/shopper_documentation_docs/' . $doc['document'] . '" download>' . stripslashes( $doc['title'] ) . '</a></h4>
                                  <p>' . $doc['description'] . '</p>
                                  <img src="/assets/icons/ico_' . $ext . '.png" alt="' . strtoupper( $ext ) . '" width="36" height="48"><a href="/assets/shopper_documentation_docs/' . $doc['document'] . '" download>DOWNLOAD</a>
                              </div>
                              <div class="clear"></div>
                          </div>
                          ';
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
                      $sql = 'SELECT a.*, c.icon FROM shopper_documentation a, shopper_programs b, document_types c
                              WHERE a.shopper_program_id = ? AND a.period_id = ? AND a.shopper_program_id = b.id AND a.document_type_id = c.id AND a.document_type_id = 4 ORDER BY a.date_created DESC';
                      $docs = $conn->query( $sql, array( $row['id'], $_SESSION['user_period_id'] ) );
                      if ( $conn->num_rows() > 0 ) {
                        while ( $doc = $conn->fetch( $docs ) ) {
                          $image = $doc['image'] ? $doc['image'] : 'no_image.jpg';
                          $icon = $doc['icon'] ? $doc['icon'] : 'no_image.jpg';
                          $ext = pathinfo( $doc['document'], PATHINFO_EXTENSION );
                          echo '
                          <div class="avo_shpsdoc">
                              <div class="avo_shpsdou_img">
                                  <a href="javascript:void(0)" data-id="' . $doc['id'] . '"><img src="/assets/shopper_documentation/' . $image . '" alt="avo_dou_img"></a>
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
                                  <h4><a href="/assets/shopper_documentation_docs/' . $doc['document'] . '" download>' . stripslashes( $doc['title'] ) . '</a></h4>
                                  <p>' . $doc['description'] . '</p>
                                  <img src="/assets/icons/ico_' . $ext . '.png" alt="' . strtoupper( $ext ) . '" width="36" height="48"><a href="/assets/shopper_documentation_docs/' . $doc['document'] . '" download>DOWNLOAD</a>
                              </div>
                              <div class="clear"></div>
                          </div>
                          ';
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

        <div class="clear"></div>
    </div>
</div>
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
<div class="bin_plcmt">
    <div class="container">
        <h2>KIT OPTIONS</h2>

        <h3>AS OF <?php echo $_SESSION['user_period_title']; ?> - <strong>TOTAL KITS: <?php echo number_format( $total ); ?></strong></h3>
        <div class="cinco_bins">
            <div class="row">
              <?php
              $sql = 'SELECT a.qty, b.title, b.image FROM shopper_program_bin_allocations a, shopper_program_bins b
                      WHERE a.shopper_program_id = ? AND a.period_id = ? AND a.shopper_program_bin_id = b.id ORDER BY b.sort';
              $bins = $conn->query( $sql, array( $row['id'], $_SESSION['user_period_id'] ) );
              if ( $conn->num_rows() > 0 ) {
                while ( $bin = $conn->fetch( $bins ) ) {
                  $image = $bin['image'] ? $bin['image'] : 'no_image.png';
                  echo '
                  <div class="cinco_bin">
                      <div class="cinco_bin_img">
                          <a href="javascript:void(0)">
                              <img src="/timThumb.php?src=/assets/shopper_program_bins/' . $image . '&w=181&h=113&zc=1" alt="' . stripslashes( $bin['title'] ) . '" width="181" height="113"/>
                          </a>
                      </div>
                      <div class="cinco_bin_cnt">
                          <h4><a href="javascript:void(0)">' . stripslashes( $bin['title'] ) . '</a></h4>
                          <p>' . number_format( $bin['qty'], 0 ) . ' ORDERED</p>
                      </div>
                      <div class="clear"></div>
                  </div>
                  ';
                }
              } else {
                echo '<div class="cinco_bin"><p class="no_bin_data">No kit options data has been added.</p></div>';
              }
              ?>
                <div class="clear"></div>
            </div>

        </div>
    </div>
</div>
<?php
}
$sql = 'SELECT * FROM shopper_related_links WHERE shopper_program_id = ? AND period_id = ? ORDER BY sort';
$links = $conn->query( $sql, array( $row['id'], $_SESSION['user_period_id'] ) );
if ( $conn->num_rows() > 0 ) {
?>
<div class="shopper_partner_related">
    <div class="container">
        <h2>RELATED LINKS</h2>

        <?php
        if ( $conn->num_rows() > 0 ) {
          while ( $link = $conn->fetch( $links ) ) {
            $image = $link['image'] ? '/timThumb.php?src=/assets/shopper_related_links/' . $link['image'] . '&w=181&h=113' : '/assets/shopper_related_links/no_image.png';
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

<div class="clear"></div>
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
