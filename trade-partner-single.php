<?php
session_start();
include_once 'header-new.php';

function url_exists($url) {
    if (!$fp = curl_init($url)) return false;
    return true;
}

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

    <!-- banner sec start -->
    <section class="supporting-doc-banner banner">
        <div class="container">
            <div class="sdb-inner">
                <div class="sdb-col-left">
                    <img src="<?php echo SITE_URL?>/timThumb.php?src=<?php echo SITE_URL?>/assets/vendors/<?php echo $vendor['logo']; ?>&w=182" alt="albertsons-safeway">
                    <a href="<?php echo SITE_URL?>/trademonthlyreport.php" class="trade-btn">trade</a>
                </div>
                <div class="sdb-col-right">
                    <a href="<?php echo SITE_URL?>/trademonthlyreport.php" class="back-btn"><img src="images/back-button.png" alt=""></a>
                    <p>Below you will find trade and shopper marketing activities at <?php echo stripslashes( $vendor['title'] ); ?>. You will also find supporting documentation and links.</p>
                    <a href="javascript:window.print();" class="sdb-print-btn">
                        <img src="<?php echo SITE_URL?>/images/print-btn.png" onmouseover="this.src='<?php echo SITE_URL?>/images/print-hvr-btn.png'" onmouseout="this.src='<?php echo SITE_URL?>/images/print-btn.png'" alt="print-btn" />
                    </a>
                </div>
            </div>
        </div>
    </section>
    <!-- banner sec end -->

    <!-- current-trade-marketing sec start -->
<?php if ( $vendor['current_marketing_activities'] ) { ?>
    <section class="current-trade-marketing-wrap">
        <div class="container">
            <h2>CURRENT TRADE <span>MARKETING ACTIVITIES</span></h2>
            <?php echo stripslashes( $vendor['current_marketing_activities'] ); ?>
        </div>
    </section>
    <!-- current-trade-marketing sec end -->
<?php } ?>

<?php if ( $vendor['upcoming_marketing_activities'] ) { ?>
    <section class="current-trade-marketing-wrap">
        <div class="container">
            <h2>UPCOMING TRADE <span>MARKETING ACTIVITIES</span></h2>
            <?php echo stripslashes( $vendor['upcoming_marketing_activities'] ); ?>
        </div>
    </section>
    <!-- current-trade-marketing sec end -->
<?php } ?>

<?php if ( $vendor['current_shopper_marketing_activities'] ) { ?>
    <section class="current-trade-marketing-wrap">
        <div class="container">
            <h2>CURRENT SHOPPER <span>MARKETING ACTIVITIES</span></h2>
            <?php echo stripslashes( $vendor['current_shopper_marketing_activities'] ); ?>
        </div>
    </section>
    <!-- current-trade-marketing sec end -->
<?php } ?>

<?php if ( $vendor['upcoming_shopper_marketing_activiites'] ) { ?>
    <section class="current-trade-marketing-wrap">
        <div class="container">
            <h2>UPCOMING SHOPPER <span>MARKETING ACTIVITIES</span></h2>
            <?php echo stripslashes( $vendor['upcoming_shopper_marketing_activiites'] ); ?>
        </div>
    </section>
    <!-- current-trade-marketing sec end -->
<?php } ?>



<?php
if ( $vendor ) {
    $sql = 'SELECT a.*, c.icon FROM vendor_documentation a, vendors b, document_types c
                  WHERE a.vendor_id = ? AND a.period_id = ? AND a.vendor_id = b.id AND a.documen_type_id = c.id ORDER BY a.date_created DESC, title';
    $docs = $conn->query( $sql, array( $vendor['id'], $_SESSION['user_period_id'] ) );
    if ( $conn->num_rows() > 0 ) {
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
                                <a href="#" data-rel="tab-5">Audios</a>
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

                                        $doc_image  =   $doc['image'];
                                        $image_name   =   '';
                                        $folder_name  =   '';

                                        if($doc['image']!='' && url_exists("https://avocomm.avocadosfrommexico.com/assets/documentation_images/'. $doc_image")){
                                            $image = $doc['image'];
                                        }else{
                                            $image = 'no_image.jpg';
                                        }
                                        //$image = $doc['image'] ? $doc['image'] : 'no_image.jpg';
                                        $icon = $doc['icon'] ? $doc['icon'] : 'no_image.jpg';
                                        if ( $doc['documen_type_id'] ==1 || $doc['documen_type_id']==2 ) {
                                            if($doc['documen_type_id'] ==1){
                                                if($doc['document']==''){
                                                    $ext = pathinfo( $doc['image'], PATHINFO_EXTENSION );
                                                }else{
                                                    $ext = pathinfo( $doc['document'], PATHINFO_EXTENSION );
                                                }

                                            }else{
                                                $ext = pathinfo( $doc['document'], PATHINFO_EXTENSION );
                                            }

                                            echo '
                                    <div class="cont">
                                        <div class="thumbnail">
                                            <a href="javascript:void(0)" data-id="' . $doc['id'] . '"><img src="'.SITE_URL.'/timThumb.php?src=/assets/documentation_images/' . $image . '&w=181&h=130&zc=1" alt="avo_dou_img"></a>
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
                                        <div class="detail">';
                                            if($doc['documen_type_id'] ==1 && $doc['document'] ==''){
                                                $image_name = $doc['image'];
                                                $folder_name = 'documentation_images';
                                                echo '<h5><a href="'.SITE_URL.'/assets/documentation_images/' . $doc['image'] . '" download>' . stripslashes( $doc['title'] ) . '</a></h5>';
                                            }else {
                                                $image_name = $doc['document'];
                                                $folder_name = 'documentation_docs';
                                                echo '<h5><a href="'.SITE_URL.'/assets/documentation_docs/' . $doc['document'] . '" download>' . stripslashes( $doc['title'] ) . '</a></h5>';
                                            }


                                            echo '<p>' . $doc['description'] . '</p>
                              
                                            <a class="download-btn" href="'.SITE_URL.'/assets/'.$folder_name.'/' . $image_name . '" download><img src="'.SITE_URL.'/images/download-btn.png" onmouseover="this.src=\''.SITE_URL.'/images/download-hvr-btn.png\'" onmouseout="this.src=\''.SITE_URL.'/images/download-btn.png\'" alt="print-btn" /></a>
                                        </div>';
                                    echo '</div>';

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
                                                        <a href="'.SITE_URL.'/assets/'.$folder_name.'/' . $image_name . '" download>Download</a>
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
                                                          <source src="'.SITE_URL.'/assets/documentation_docs/' . $doc['document'] . '" type="video/mp4">
                                                          Your browser does not support the video tag.
                                                        </video>
                                                      </div>
        
                                                  </div>
                                                  ';
                                            } elseif ( $is_audio ) {
                                                $popups .= '
                                                  <div class="pop_vdo_other" style="text-align: center;">
                                                      <a href="javascript:void(0)">
                                                          <img src="'.SITE_URL.'/assets/documentation_images/' . $image . '" alt="' . htmlspecialchars( stripslashes( $doc['title'] ) ) . '" />
                                                          <br>
                                                          <audio controls>
                                                           <source src="'.SITE_URL.'/assets/documentation_docs/' . $doc['document'] . '" type="audio/mpeg">
                                                           Your browser does not support the audio element.
                                                         </audio>
                                                      </a>
                                                  </div>
                                                  ';
                                            } else {
                                                $popups .= '
                                                  <div class="pop_vdo_sec_other">
                                                      <a href="javascript:void(0)">
                                                          <img src="'.SITE_URL.'/assets/documentation_images/' . $image . '" alt="' . htmlspecialchars( stripslashes( $doc['title'] ) ) . '" />
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
                        <div class="tabs-content-inner" id="tab-2" style="display:none">

                                <?php
                                $sql = 'SELECT a.*, c.icon FROM vendor_documentation a, vendors b, document_types c
                                      WHERE a.vendor_id = ? AND a.period_id = ? AND a.vendor_id = b.id AND a.documen_type_id = c.id AND a.documen_type_id = 1 ORDER BY a.date_created DESC';
                                $docs = $conn->query( $sql, array( $vendor['id'], $_SESSION['user_period_id'] ) );
                                if ( $conn->num_rows() > 0 ) {
                                    while ( $doc = $conn->fetch( $docs ) ) {
                                        $doc_image  =   $doc['image'];

                                        if($doc['image']!='' && url_exists("https://avocomm.avocadosfrommexico.com/assets/documentation_images/'. $doc_image")){
                                            $image = $doc['image'];
                                        }else{
                                            $image = 'no_image.jpg';
                                        }
                                        $icon = $doc['icon'] ? $doc['icon'] : 'no_image.jpg';

                                        if ( $doc['documen_type_id'] ==1 ) {
                                            if($doc['document']==''){
                                                $ext = pathinfo( $doc['image'], PATHINFO_EXTENSION );
                                            }else{
                                                $ext = pathinfo( $doc['document'], PATHINFO_EXTENSION );
                                            }
                                            echo '
                                    <div class="cont">
                                        <div class="thumbnail">
                                            <a href="javascript:void(0)" data-id="' . $doc['id'] . '"><img src="'.SITE_URL.'/assets/documentation_images/' . $image . '" alt="avo_dou_img"></a>
                                            ';

                                            if ( $ext == 'mp4' || $ext == 'mpg' || $ext == 'mov' ) {
                                                echo '
                                            <a href="javascript:void(0)" data-id="' . $doc['id'] . '" class="shps_douar"></a>
                                          ';
                                            }

                                            echo '
                                            <a href="javascript:void(0)" data-id="' . $doc['id'] . '" class="shps_douserc"></a>
                                        </div>
                                        
                                        <div class="detail">';
                                            if($doc['document'] ==''){
                                                $image_name = $doc['image'];
                                                $folder_name = 'documentation_images';
                                                echo '<h5><a href="'.SITE_URL.'/assets/documentation_images/' . $doc['image'] . '" download>' . stripslashes( $doc['title'] ) . '</a></h5>';
                                            }else {
                                                $image_name = $doc['document'];
                                                $folder_name = 'documentation_docs';
                                                echo '<h5><a href="'.SITE_URL.'/assets/documentation_docs/' . $doc['document'] . '" download>' . stripslashes( $doc['title'] ) . '</a></h5>';
                                            }


                                            echo '<p>' . $doc['description'] . '</p>
                                            <a class="download-btn" href="'.SITE_URL.'/assets/'.$folder_name.'/' . $image_name . '" download><img src="'.SITE_URL.'/images/download-btn.png" onmouseover="this.src=\''.SITE_URL.'/images/download-hvr-btn.png\'" onmouseout="this.src=\''.SITE_URL.'/images/download-btn.png\'" alt="print-btn" /></a>            
                                        </div>                                                            
                     
                                    </div>
                                    ';
                                        }
                                    }
                                } else {
                                    echo '<p class="no_docs">No images have been uploaded.</p>';
                                }
                                ?>


                        </div>
                        <div class="tabs-content-inner" id="tab-3" style="display:none;">
                                <?php
                                $sql = 'SELECT a.*, c.icon FROM vendor_documentation a, vendors b, document_types c
                                      WHERE a.vendor_id = ? AND a.period_id = ? AND a.vendor_id = b.id AND a.documen_type_id = c.id AND a.documen_type_id IN( 2 ) ORDER BY a.date_created DESC';
                                $docs = $conn->query( $sql, array( $vendor['id'], $_SESSION['user_period_id'] ) );
                                if ( $conn->num_rows() > 0 ) {
                                    while ( $doc = $conn->fetch( $docs ) ) {
                                        $doc_image  =   $doc['image'];

                                        if($doc['image']!='' && url_exists("https://avocomm.avocadosfrommexico.com/assets/documentation_images/'. $doc_image")){
                                            $image = $doc['image'];
                                        }else{
                                            $image = 'no_image.jpg';
                                        }
                                        $icon = $doc['icon'] ? $doc['icon'] : 'no_image.jpg';

                                        if ( $doc['document'] ) {
                                            $ext = pathinfo( $doc['document'], PATHINFO_EXTENSION );
                                            echo '
                                    <div class="cont">
                                        <div class="thumbnail">
                                            <a href="javascript:void(0)" data-id="' . $doc['id'] . '"><img src="'.SITE_URL.'/assets/documentation_images/' . $image . '" alt="avo_dou_img"></a>
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
                                            <h5><a href="'.SITE_URL.'/assets/documentation_docs/' . $doc['document'] . '" download>' . stripslashes( $doc['title'] ) . '</a></h5>
                                            <p>' . $doc['description'] . '</p>
                                            <a class="download-btn" href="'.SITE_URL.'/assets/documentation_docs/' . $doc['document'] . '" download><img src="'.SITE_URL.'/images/download-btn.png" onmouseover="this.src=\''.SITE_URL.'/images/download-hvr-btn.png\'" onmouseout="this.src=\''.SITE_URL.'/images/download-btn.png\'" alt="print-btn" /></a>
                                           
                                        </div>
                                      
                                    </div>
                                    ';
                                        }
                                    }
                                } else {
                                    echo '<p class="no_docs">No documents have been uploaded.</p>';
                                }
                                ?>

                        </div>
                        <div class="tabs-content-inner" id="tab-4" style="display:none">
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
                                    <div class="cont">
                                        <div class="thumbnail">
                                            <a href="javascript:void(0)" data-id="' . $doc['id'] . '"><img src="'.SITE_URL.'/assets/documentation_images/' . $image . '" alt="avo_dou_img"></a>
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
                                            <h5><a href="'.SITE_URL.'/assets/documentation_docs/' . $doc['document'] . '" download>' . stripslashes( $doc['title'] ) . '</a></h5>
                                            <p>' . $doc['description'] . '</p>
                                            <a class="download-btn" href="'.SITE_URL.'/assets/documentation_docs/' . $doc['document'] . '" download><img src="'.SITE_URL.'/images/download-btn.png" onmouseover="this.src=\''.SITE_URL.'/images/download-hvr-btn.png\'" onmouseout="this.src=\''.SITE_URL.'/images/download-btn.png\'" alt="print-btn" /></a>
                                         
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
                        <div class="tabs-content-inner" id="tab-5" style="display:none">
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
                                    <div class="cont">
                                        <div class="thumbnail">
                                            <a href="javascript:void(0)" data-id="' . $doc['id'] . '"><img src="'.SITE_URL.'/assets/documentation_images/' . $image . '" alt="avo_dou_img"></a>
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
                                            <h5><a href="'.SITE_URL.'/assets/documentation_docs/' . $doc['document'] . '" download>' . stripslashes( $doc['title'] ) . '</a></h5>
                                            <p>' . $doc['description'] . '</p>
                                            <a class="download-btn" href="'.SITE_URL.'/assets/documentation_docs/' . $doc['document'] . '" download><img src="'.SITE_URL.'/images/download-btn.png" onmouseover="this.src=\''.SITE_URL.'/images/download-hvr-btn.png\'" onmouseout="this.src=\''.SITE_URL.'/images/download-btn.png\'" alt="print-btn" /></a>
                                           
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
                        </div>
                        <div class="clear"></div>
                    </div>
                </div>
        </section>
    <?php } ?>
<?php } ?>

    <script src="<?php echo SITE_URL?>/js/summary-report.js"></script>

<?php
echo $popups;

include_once 'footer-new.php';
