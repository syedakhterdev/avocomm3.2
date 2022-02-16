<?php
$title =  'trades';
$subtitle = 'trade_entries';
require( '../config.php' );

require( '../includes/pdo.php' );
require( '../includes/check_login.php' );
require( '../includes/VendorManager.php' );
$Vendor = new VendorManager($conn);
$msg = '';
if ( !(int)$_SESSION['admin_permission_trade'] ) header( 'Location: ../menu.php' );

// check variables passed in through querystring
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$update = isset($_POST['update']) ? (int) $_POST['update'] : 0;

// if an id is passed in then let's fetch it from the database
if ($id) {
    if (!( $row = $Vendor->getByID($id) )) {
        $msg = "Sorry, a record with the specified ID could not be found!";
    }

    $del_logo = ( isset( $_GET['del_logo'] ) ) ? (int)$_GET['del_logo'] : 0;
    if ( $del_logo && (int)$_SESSION['admin_permission_trade'] ) {
        $Vendor->removeLogo($id);
        $row['logo'] = '';
    }

    $del_doc = ( isset( $_GET['del_doc'] ) ) ? (int)$_GET['del_doc'] : 0;
    if ( $del_doc && (int)$_SESSION['admin_permission_trade'] ) {
      $Vendor->removeDocument( $del_doc, $_SESSION['admin_period_id'] );
    }

    $del_rel = ( isset( $_GET['del_rel'] ) ) ? (int)$_GET['del_rel'] : 0;
    if ( $del_rel && (int)$_SESSION['admin_permission_trade'] ) {
      $Vendor->removeRelated( $del_rel, $_SESSION['admin_period_id'] );
    }
} else if ( $update && (int)$_SESSION['admin_permission_trade'] ) { // if the user submitted a form....
    $token = $_SESSION['upd_token'];
    unset($_SESSION['upd_token']);

    if (!$msg && $token != '' && $token == $_POST['upd_token']) {
        if (!$Vendor->update($_POST['update'], $_POST['title'], $_POST['logo'], $_POST['tier_id'], $_POST['sort'], $_POST['active'])) {
            $msg = "Sorry, an error has occurred, please contact your administrator.<br>Error: " . $Vendor->error() . ";";
        } else {
            header("Location: index.php");
        }
    }
}

// create a token for secure deletion (from this page only and not remote)
$_SESSION['upd_token'] = md5(uniqid());
session_write_close();
?>

<?php require( '../includes/header_new.php' );?>
    <div class="dashboard-sub-menu-sec">
        <div class="container">
            <div class="sub-menu-sec">
                <?php require( '../includes/trade_sub_nav.php' );?>
            </div>
        </div>
    </div>

    <div class="back-btn-sec">
        <div class="container">
            <div class="back-btn">
                <a href="javascript:void(0)" onclick="window.location.href = '<?php echo ADMIN_URL?>/trade_vendor_entries/index.php'">
                    <img src="<?php echo ADMIN_URL?>/images/back-button.png" alt="" />
                </a>
            </div>
        </div>
    </div>


    <div class="edit-entry-sec">
        <div class="container">
            <div class="edit-entry-hdr-sec">
                <div class="entry-img-sec">
                    <?php if ($row['logo'] != '') { ?>
                        <img src="<?php echo ADMIN_URL?>/timThumb.php?src=/assets/vendors/<?php echo $row['logo']; ?>&h=80">
                    <?php } ?>
                </div>
                <div class="entry-cnt-sec">
                    <div class="upper-sec">
                        <div class="right-sec">
                            <h2><bold>EDIT</bold> A TRADE ENTRY</h2>
                            <p><?php echo $row['title']; ?></p>
                        </div>
                        <div class="left-sec">

                            <form action="<?php echo ADMIN_URL?>/trade_vendor_entries/edit.php" role="form" method="POST" onSubmit="return validateForm();">
                                <input type="hidden" name="update" value="<?php echo $row['id']; ?>">
                                <input type="hidden" name="upd_token" value="<?php echo $_SESSION['upd_token']; ?>">
                            <?php
                            if ( isset( $_GET['cpid'] ) && (int)$_GET['cpid'] ) {
                                $cpid = (int)$_GET['cpid'];

                                // copy over the updates
                                $sql = 'INSERT INTO vendor_updates (
                                                    SELECT NULL, NOW(), ?, ?, current_marketing_activities, upcoming_marketing_activities, current_shopper_marketing_activities, upcoming_shopper_marketing_activiites
                                                    FROM vendor_updates WHERE vendor_id = ? AND period_id = ?
                                                  );';
                                $conn->exec( $sql, array( $id, $cpid, $id, $_SESSION['admin_period_id'] ) );

                                // copy over the documentation
                                $sql = 'INSERT INTO vendor_documentation (
                                                    SELECT NULL, NOW(), ?, ?, title, description, image, document, documen_type_id, active, download_count
                                                    FROM vendor_documentation WHERE vendor_id = ? AND period_id = ?
                                                  );';
                                $conn->exec( $sql, array( $id, $cpid, $id, $_SESSION['admin_period_id'] ) );

                                // copy over the related links
                                $sql = 'INSERT INTO vendor_related_links (
                                                    SELECT NULL, ?, ?, title, description, image, url, sort
                                                    FROM vendor_related_links WHERE vendor_id = ? AND period_id = ?
                                                  );';
                                $conn->exec( $sql, array( $cpid, $id, $id, $_SESSION['admin_period_id'] ) );

                                echo '<strong>Your entry was cloned successfully!';

                            } else {
                                $sql = 'SELECT id, title FROM periods WHERE id NOT IN ( SELECT period_id FROM vendor_updates WHERE vendor_id = ? ) ORDER BY year ASC, month ASC';
                                $periods = $conn->query( $sql, array( $id ) );
                                if ( $conn->num_rows() > 0 ) {
                                    echo '<h3></h3>
                                      <select class="entry-options"  name="clone_period_id" onChange="if ( confirm( ' . "'Are you sure you want to clone this entry to another period?'" . ' ) ) { window.location=' . "'edit.php?id=$id&cpid=' + this.value;" . ' } else { this.value = ' . "''" . '; }">';
                                    echo '<option value="">Select a period</option>';
                                    while ( $period = $conn->fetch( $periods ) ) {
                                        echo '<option value="' . $period['id'] . '">' . stripslashes( ucwords( strtolower( $period['title'] ) ) ) . "</option>\n";
                                    }
                                    echo '</select>';
                                }
                            }
                            ?>
                            </form>

                        </div>
                    </div>
                    <div class="lower-sec">
                        <div class="tabs-sec">
                            <ul>
                                <li class="active" data-tab="all-sec">All</li>
                                <li data-tab="for-current-period-sec">For Current Period</li>
                                <li data-tab="documentation-sec">Documentation</li>
                                <li data-tab="related-link-sec">Related Links</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>




            <div class="for-current-period single-sec">



                <?php
                $sql = "SELECT * FROM vendor_updates
                                                      WHERE vendor_id = ? AND period_id = ? ORDER BY id;";
                $result = $conn->query($sql, array($row['id'], $_SESSION['admin_period_id']));

                if ($conn->num_rows() > 0) {
                    $num_results = $conn->num_rows();

                    while ($row_ch = $conn->fetch($result)) { ?>


                        <div class="tab-hdr-sec">
                            <h3>FOR CURRENT PERIOD</h3>
                            <div class="action-sec">
                                <a href="<?php echo ADMIN_URL?>/vendor_updates/edit.php?id=<?php echo $row_ch['id']?>&sid=<?php echo $row['id']?>">
                                    <img src="<?php echo ADMIN_URL?>/images/entry-edit-button.png" alt=""/>
                                </a>
                            </div>
                        </div>

                        <div class="row-sec">
                            <h5 class="open">Current Marketing Activities</h5>
                            <div class="update-cnt-sec">
                                <?php
                                if($row_ch['current_marketing_activities']!=''){
                                    echo stripslashes($row_ch['current_marketing_activities']);
                                }else{
                                    echo 'No updates found.';
                                }
                                ?>
                            </div>
                        </div>


                        <div class="row-sec">
                            <h5 class="open">Upcoming Marketing Activities</h5>
                            <div class="update-cnt-sec">
                                <p>
                                    <?php
                                    if($row_ch['upcoming_marketing_activities']!=''){
                                        echo stripslashes($row_ch['upcoming_marketing_activities']);
                                    }else{
                                        echo 'No updates found.';
                                    }
                                    ?>
                                </p>
                            </div>
                        </div>

                        <div class="row-sec">
                            <h5 class="open">Current Shopper Marketing Activities</h5>
                            <div class="update-cnt-sec">
                                <p>
                                    <?php
                                    if($row_ch['current_shopper_marketing_activities']!=''){
                                        echo stripslashes($row_ch['current_shopper_marketing_activities']);
                                    }else{
                                        echo 'No updates found.';
                                    }
                                    ?>
                                </p>
                            </div>
                        </div>

                        <div class="row-sec">
                            <h5 class="open">Upcoming Shopper Marketing Activities</h5>
                            <div class="update-cnt-sec">
                                <p>
                                    <?php
                                    if($row_ch['upcoming_shopper_marketing_activiites']!=''){
                                        echo stripslashes($row_ch['upcoming_shopper_marketing_activiites']);
                                    }else{
                                        echo 'No updates found.';
                                    }
                                    ?>
                                </p>
                            </div>
                        </div>


                        <?php

                    }
                }else{?>
                    <div class="tab-hdr-sec">
                        <h3>FOR CURRENT PERIOD</h3>
                        <div class="action-sec">
                            <a href="<?php echo ADMIN_URL?>/vendor_updates/add.php?sid=<?php echo $row['id']?>">
                                <img src="<?php echo ADMIN_URL?>/images/entry-add-button.png" alt="" />
                            </a>
                        </div>
                    </div>
                    <div class="row-sec">
                        <h5 class="open">Current Marketing Activities</h5>
                        <div class="update-cnt-sec">
                            <p>
                                No updates found.
                            </p>
                        </div>
                    </div>
                    <div class="row-sec">
                        <h5 class="open">Upcoming Marketing Activities</h5>
                        <div class="update-cnt-sec">
                            <p>
                                No updates found.
                            </p>
                        </div>
                    </div>
                    <div class="row-sec">
                        <h5 class="open">Current Shopper Marketing Activities</h5>
                        <div class="update-cnt-sec">
                            <p>
                                No updates found.
                            </p>
                        </div>
                    </div>
                    <div class="row-sec">
                        <h5 class="open">Upcoming Shopper Marketing Activities</h5>
                        <div class="update-cnt-sec">
                            <p>
                                No updates found.
                            </p>
                        </div>
                    </div>
                <?php }?>

            </div>
                    <?php
                    $sql = 'SELECT a.id, a.period_id, a.vendor_id, b.title FROM vendor_updates a, periods b WHERE a.period_id = b.id AND a.vendor_id = ? AND a.period_id <> ? ORDER BY b.year DESC, month DESC';
                    $upds = $conn->query( $sql, array( $id, $_SESSION['admin_period_id'] ) );
                    if ( $conn->num_rows() > 0 ) {?>

            <div class="prior-updates-sec">
                <div class="prior-hdr-sec">
                    <h3>PRIOR UPDATES <a href="javascript:void(0)">Click below to access.</a></h3>
                </div>
                <div class="prior-list">
                  <?php
                  while ( $upd = $conn->fetch( $upds ) ) {?>
                    <a href="<?php echo ADMIN_URL?>/selPeriod.php?id=<?php echo $upd['period_id']?>&trid=<?php echo $upd['vendor_id']?>"><?php echo stripslashes( $upd['title'] )?></a>
                    <?php }?>
                </div>
            </div>
                <?php
                    }else{?>
                        <div class="prior-updates-sec">
                            <div class="prior-hdr-sec">
                                <h3>PRIOR UPDATES</h3>
                            </div>
                            <div class="prior-list">
                                No Record found
                            </div>
                        </div>
                <?php }
                ?>

            <div class="documentation-sec single-sec">
                <div class="tab-hdr-sec">
                    <h3>DOCUMENTATION</h3>
                    <div class="action-sec">
                        <a href="<?php echo ADMIN_URL?>/vendor_documentation/add.php?sid=<?php echo $row['id']; ?>">
                            <img src="<?php echo ADMIN_URL?>/images/entry-add-button.png" alt="" />
                        </a>
                    </div>
                </div>

                <div class="doc-row-sec">
                    <div class="row-sec">
                        <div class="update-cnt-sec">
                            <table style="width: 100%;">
                                <?php
                                $sql = "SELECT * FROM vendor_documentation
											                  WHERE vendor_id = ? AND period_id = ? ORDER BY date_created;";
                                $result = $conn->query($sql, array($row['id'], $_SESSION['admin_period_id']));

                                if ($conn->num_rows() > 0) {
                                    $num_results = $conn->num_rows();

                                    while ($row_ch = $conn->fetch($result)) {
                                        echo "<tr>";

                                        if ($row_ch['image']) {
                                            echo "<td style=\"width: 90px;\" valign=\"top\"><img src=\"../timThumb.php?src=/assets/documentation_images/" . $row_ch['image'] . "&w=130&h=72&zc=1\" width=\"50\"></td>";
                                        } else {
                                            echo "<td style=\"width: 90px;\"><img src=\"/assets/documentation_images/no_photo.jpg\" width=\"50\"></td>";
                                        }

                                        echo "<td style=\"width: auto;\"><h4 myy-2>" . stripslashes($row_ch['title']) . "</h4>" . stripslashes($row_ch['description']) . "</td>
                                                <td align=\"right\" nowrap>
                                                    <a href=\"../vendor_documentation/edit.php?id=" . $row_ch['id'] . "&sid=" . $row['id'] . "\" title=\"Edit\" class=\"action_btn edit\">EDIT</a>
                                                    <a href=\"edit.php?id=" . $row['id'] . "&del_doc=" . $row_ch['id'] . "\" onClick=\"return confirm( 'Are you sure you want to delete this item?')\" title=\"Delete\" class=\"action_btn delete\">DELETE</a>
                                                </td>";
                                        echo "</tr>\n";
                                    }
                                } else {
                                    echo '<tr><td colspan="3"><p>No documentation found</p></td></tr>';
                                }
                                ?>
                            </table>
                        </div>
                    </div>
                </div>

            </div>

            <div class="related-link-sec single-sec">
                <div class="tab-hdr-sec">
                    <h3>RELATED LINKS</h3>
                    <div class="action-sec">
                        <a href="<?php echo ADMIN_URL?>/vendor_related_links/add.php?vid=<?php echo $row['id']; ?>">
                            <img src="<?php echo ADMIN_URL?>/images/entry-add-button.png" alt="" />
                        </a>
                    </div>
                </div>

                <div class="doc-row-sec related-link-list">

                    <?php
                    $sql = "SELECT * FROM vendor_related_links WHERE vendor_id = ? AND period_id = ? ORDER BY sort;";
                    $result = $conn->query($sql, array($row['id'], $_SESSION['admin_period_id']));

                    if ($conn->num_rows() > 0) {
                        $num_results = $conn->num_rows();

                        while ($row_ch = $conn->fetch($result)) { ?>


                            <div class="single-doc-row-sec">
                                <div class="doc-cnt">
                                    <?php if ( $row_ch['image'] ) {?>
                                        <img src="<?php echo ADMIN_URL?>/timThumb.php?src=/assets/vendor_related_links/<?php echo $row_ch['image']?>&w=130&h=72&zc=1" width="50">
                                    <?php }else{?>
                                        <img src="<?php echo SITE_URL?>/assets/documentation_images/no_photo.jpg" width="50">
                                <?php }?>
                                    <h4><?php echo stripslashes($row_ch['title'])?></h4>
                                    <p><?php echo stripslashes($row_ch['description'])?></p>

                                    <p>
                                        <a href="<?php echo $row_ch['url']?>">
                                            <?php echo $row_ch['url']?>
                                        </a>
                                    </p>
                                </div>
                                <div class="doc-action">
                                    <a href="<?php echo ADMIN_URL?>/vendor_related_links/edit.php?id=<?php echo $row_ch['id']?>&vid=<?php echo $row['id']?>">
                                        <img src="<?php echo ADMIN_URL?>/images/edit-btn.png" alt=""/>
                                    </a>
                                    <a href="<?php echo ADMIN_URL?>/trade_vendor_entries/edit.php?id=<?php echo $row['id']?>&del_rel=<?php echo $row_ch['id']?>" onClick="return confirm( 'Are you sure you want to delete this item?')">
                                        <img src="<?php echo ADMIN_URL?>/images/delete-btn.png" alt=""/>
                                    </a>
                                </div>
                            </div>

                        <?php }
                    }else{?>
                        <div class="single-doc-row-sec">
                            <p>No related links found</p>
                        </div>
                    <?php }?>
                </div>

            </div>

        </div>
    </div>
    <script>
    $(document).ready(function () {
        $('form:first *:input[type!=hidden]:first').focus();
    });
    </script>


    <script type="text/javascript">
        $().ready(function () {
            //updateCountdown('#title', 65, '#title_lbl');
        });

        function updateCountdown(input, limit, lbl) {
            var remaining = limit - $(input).val().length;
            $(lbl).text(remaining + ' characters remaining.');
        }
    </script>
    <script>
        $(document).ready(function () {

            $('#submit').click(function () {
                if (!hasHtml5Validation())
                    return validateForm();
            });
        });
        function validateForm() {

            if ($('#title').val() == '')
                return createError('title', 'Please enter a valid title');
        }
        function hasHtml5Validation() {
            return typeof document.createElement('input').checkValidity === 'function';
        }
    </script>
    <script>
        $(document).ready(function () {

            $('.single-sec h5').click(function () {
                if ($(this).hasClass('open')) {
                    $(this).removeClass('open');
                    $('+ .update-cnt-sec', this).toggle('slow');
                } else {
                    $(this).addClass('open');
                    $('+ .update-cnt-sec', this).toggle('slow');
                }
            });

            $('.tabs-sec ul li').click(function() {
                if($(this).hasClass('active')) {
                    return false;
                }
                $('.tabs-sec ul li').removeClass('active');
                $(this).addClass('active');
            });

            $('li[data-tab="all-sec"]').click(function() {
                $('.single-sec').show();
                $('.prior-updates-sec').show();
            });
            $('li[data-tab="for-current-period-sec"]').click(function() {
                $('.single-sec').hide();
                $('.prior-updates-sec').hide();
                $('.for-current-period.single-sec').show();
            });
            $('li[data-tab="documentation-sec"]').click(function() {
                $('.single-sec').hide();
                $('.prior-updates-sec').hide();
                $('.documentation-sec.single-sec').show();
            });
            $('li[data-tab="related-link-sec"]').click(function() {
                $('.single-sec').hide();
                $('.prior-updates-sec').hide();
                $('.related-link-sec.single-sec').show();
            });

        });
    </script>
<?php $conn->close(); ?>
<?php include('../includes/footer_new.php');?>