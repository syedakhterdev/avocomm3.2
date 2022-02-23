<?php
$title =  'shoppers';
$subtitle = 'shopper_entries';
require( '../config.php' );

require( '../includes/pdo.php' );
require( '../includes/check_login.php' );
require( '../includes/ShopperProgramManager.php' );
$ShopperProgram = new ShopperProgramManager($conn);
$msg = '';

// check variables passed in through querystring
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// if an id is passed in then let's fetch it from the database
if ($id) {

    if (!( $row = $ShopperProgram->getByID($id) )) {
        $msg = "Sorry, a record with the specified ID could not be found!";
    }

    $del_doc = isset($_GET['del_doc']) ? (int) $_GET['del_doc'] : 0;
    if ($del_doc) {
        if ($ShopperProgram->removeDocumentation($id, $del_doc, $_SESSION['admin_period_id'])) {
            $child_msg = "Documentation deleted successfully!";
        } else {
            $child_msg = "Documentation could not be deleted!";
        }
    }

    $del_upd = isset($_GET['del_upd']) ? (int) $_GET['del_upd'] : 0;
    if ($del_upd) {
        if ($ShopperProgram->removeUpdates($id, $_SESSION['admin_period_id'], $del_upd)) {
            $child_msg = "Update deleted successfully!";
        } else {
            $child_msg = "Update could not be deleted!";
        }
    }

    $del_rel = isset($_GET['del_rel']) ? (int) $_GET['del_rel'] : 0;
    if ($del_rel) {
        if ($ShopperProgram->removeRelatedLinks($id, $_SESSION['admin_period_id'], $del_rel)) {
            $child_msg = "RelatedLink deleted successfully!";
        } else {
            $child_msg = "RelatedLink could not be deleted!";
        }
    }

    $del_bin = isset($_GET['del_bin']) ? (int) $_GET['del_bin'] : 0;
    if ($del_bin) {
        if ($ShopperProgram->removeBins($id, $del_bin)) {
            $child_msg = "Kit option deleted successfully!";
        } else {
            $child_msg = "Kit option could not be deleted!";
        }
    }

    $del_par = isset($_GET['del_par']) ? (int) $_GET['del_par'] : 0;
    if ($del_par) {
        if ($ShopperProgram->removePartners($id, $del_par)) {
            $child_msg = "Partner deleted successfully!";
        } else {
            $child_msg = "Partner could not be deleted!";
        }
    }

    $add_par = isset($_POST['add_par']) ? (int) $_POST['shopper_partner_id'] : 0;
    if ($add_par) {
        if ($ShopperProgram->addToPartners($id, $add_par)) {
            $child_msg = "Partner added successfully!";
        } else {
            $child_msg = "Partner could not be added!";
        }
    }

    $add_bin_alloc = isset($_POST['add_bin_alloc']) ? (int) $_POST['add_bin_alloc'] : 0;
    if ($add_bin_alloc) {

        if ($ShopperProgram->addBinAllocation($id, $_SESSION['admin_period_id'], $_POST['shopper_bin_id'], $_POST['qty'])) {
            $child_msg = "Kit option added successfully!";
        } else {
            $child_msg = "Kit option could not be added!";
        }
    }

    $del_alloc = isset($_GET['del_alloc']) ? (int) $_GET['del_alloc'] : 0;
    if ($del_alloc) {
        if ($ShopperProgram->removeBinAllocation($id, $_SESSION['admin_period_id'], $del_alloc)) {
            $child_msg = "Kit option deleted successfully!";
        } else {
            $child_msg = "Kit option could not be deleted!";
        }
    }

    $del_image = ( isset($_GET['del_image']) ) ? (int) $_GET['del_image'] : 0;
    if ($del_image) {
        $ShopperProgram->removeImage($id);
        $row['image'] = '';
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
                <?php require( '../includes/shopper_sub_nav.php' );?>
            </div>
        </div>
    </div>

    <div class="back-btn-sec">
        <div class="container">
            <div class="back-btn">
                <a href="<?php echo ADMIN_URL?>/shopper_program_entries/index.php">
                    <img src="<?php echo ADMIN_URL?>/images/back-button.png" alt="" />
                </a>
            </div>
        </div>
    </div>


    <div class="edit-entry-sec">
        <div class="container">
            <div class="edit-entry-hdr-sec">
                <div class="entry-img-sec">
                    <?php if ($row['image'] != '') { ?>
                        <img src="<?php echo ADMIN_URL?>/timThumb.php?src=/assets/shopper_programs/<?php echo $row['image']; ?>&h=80">
                    <?php } ?>
                </div>
                <div class="entry-cnt-sec">
                    <div class="upper-sec">
                        <div class="right-sec">
                            <h2><bold>EDIT</bold> A SHOPPER ENTRY</h2>
                            <p><?php echo $row['title']; ?></p>
                        </div>
                        <div class="left-sec">
                            <form action="<?php echo ADMIN_URL?>/shopper_program_entries/edit.php" role="form" method="POST" onSubmit="return validateForm();">
                                <input type="hidden" name="update" value="<?php echo $row['id']; ?>">
                                <input type="hidden" name="upd_token" value="<?php echo $_SESSION['upd_token']; ?>">
                            <?php
                            if ( isset( $_GET['cpid'] ) && (int)$_GET['cpid'] ) {
                                $cpid = (int)$_GET['cpid'];

                                // copy over the updates
                                $sql = 'INSERT INTO shopper_program_updates (
                                                    SELECT NULL, ?, ?, description, updates FROM shopper_program_updates WHERE shopper_program_id = ? AND period_id = ?
                                                  );';
                                $conn->exec( $sql, array( $id, $cpid, $id, $_SESSION['admin_period_id'] ) );

                                // copy over the documentation
                                $sql = 'INSERT INTO shopper_documentation (
                                                    SELECT NULL, NOW(), ?, ?, title, description, image, document, document_type_id, active, download_count
                                                    FROM shopper_documentation WHERE shopper_program_id = ? AND period_id = ?
                                                  );';
                                $conn->exec( $sql, array( $id, $cpid, $id, $_SESSION['admin_period_id'] ) );

                                // copy over the bin allocations
                                $sql = 'INSERT INTO shopper_program_bin_allocations (
                                                    SELECT ?, ?, shopper_program_bin_id, qty FROM shopper_program_bin_allocations WHERE shopper_program_id = ? AND period_id = ?
                                                  );';
                                $conn->exec( $sql, array( $id, $cpid, $id, $_SESSION['admin_period_id'] ) );

                                // copy over the related links
                                $sql = 'INSERT INTO shopper_related_links (
                                                    SELECT NULL, ?, ?, title, description, image, url, sort
                                                    FROM shopper_related_links WHERE shopper_program_id = ? AND period_id = ?
                                                  );';
                                $conn->exec( $sql, array( $cpid, $id, $id, $_SESSION['admin_period_id'] ) );

                                echo '<strong>Your entry was cloned successfully!';

                            } else {
                                $sql = 'SELECT id, title FROM periods WHERE id NOT IN ( SELECT period_id FROM shopper_program_updates WHERE shopper_program_id = ? ) ORDER BY year ASC, month ASC';
                                $periods = $conn->query( $sql, array( $id ) );
                                if ( $conn->num_rows() > 0 ) {
                                    echo '<h1></h1><select class="entry-options" name="clone_period_id" onChange="if ( confirm( ' . "'Are you sure you want to clone this entry to another period?'" . ' ) ) { window.location=' . "'edit.php?id=$id&cpid=' + this.value;" . ' } else { this.value = ' . "''" . '; }">';
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
                                <li data-tab="kit-options-sec">Kit Options</li>
                                <li data-tab="related-link-sec">Related Links</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>


            <div class="for-current-period single-sec">


                <?php
                $sql = "SELECT id, description, updates FROM shopper_program_updates
											                  WHERE shopper_program_id = ? AND period_id = ? ORDER BY id;";
                $result = $conn->query($sql, array($row['id'], $_SESSION['admin_period_id']));
                if ($conn->num_rows() > 0) {
                $num_results = $conn->num_rows();

                while ($row_ch = $conn->fetch($result)) { ?>

                    <div class="tab-hdr-sec">
                        <h3>FOR CURRENT PERIOD</h3>
                        <div class="action-sec">
                            <a href="<?php echo ADMIN_URL?>/shopper_program_updates/edit.php?id=<?php echo $row_ch['id']?>&sid=<?php echo $row['id']?>">
                                <img src="<?php echo ADMIN_URL?>/images/entry-edit-button.png" alt=""/>
                            </a>
                            <a href="<?php echo ADMIN_URL?>/shopper_program_entries/edit.php?id=<?php echo $row['id']?>&del_upd=<?php echo $row_ch['id']?>">
                                <img src="<?php echo ADMIN_URL?>/images/entry-delete-button.png" alt=""/>
                            </a>
                        </div>
                    </div>

                    <div class="row-sec">
                        <h4>Description</h4>
                        <p><?php echo stripslashes($row_ch['description'])?></p>
                    </div>
                    <div class="row-sec">
                        <h5 class="open">Updates</h5>
                        <div class="update-cnt-sec">
                            <p><?php echo stripslashes($row_ch['updates'])?></p>
                        </div>
                    </div>
                    <?php
                }
                }else{?>
                    <div class="tab-hdr-sec">
                        <h3>FOR CURRENT PERIOD</h3>
                        <div class="action-sec">
                            <a href="<?php echo ADMIN_URL?>/shopper_program_updates/add.php?sid=<?php echo $row['id']?>">
                                <img src="<?php echo ADMIN_URL?>/images/entry-add-button.png" alt=""/>
                            </a>
                        </div>
                    </div>

                    <div class="row-sec">
                        <h4>Description</h4>
                        <p>
                            No updates found
                        </p>
                    </div>
                <?php }?>


            </div>

              <?php
              $sql = 'SELECT a.id, a.period_id, a.shopper_program_id, b.title FROM shopper_program_updates a, periods b WHERE a.period_id = b.id AND a.shopper_program_id = ? AND a.period_id <> ? ORDER BY b.year DESC, month DESC';
              $upds = $conn->query( $sql, array( $id, $_SESSION['admin_period_id'] ) );
              if ( $conn->num_rows() > 0 ) {?>
            <div class="prior-updates-sec">
                <div class="prior-hdr-sec">
                    <h3>PRIOR UPDATES <a href="javascript:void(0)">Click below to access.</a></h3>
                </div>
                <div class="prior-list">
                    <?php while ( $upd = $conn->fetch( $upds ) ) {?>
                    <a href="<?php echo ADMIN_URL?>/selPeriod.php?id=<?php echo $upd['period_id']?>&spid=<?php echo $upd['shopper_program_id']?>"><?php echo stripslashes( $upd['title'] )?></a>
                    <?php }?>
                </div>
            </div>
                <?php }?>

            <div class="documentation-sec single-sec">
                <div class="tab-hdr-sec">
                    <h3>DOCUMENTATION</h3>
                    <div class="action-sec">
                        <a href="<?php echo ADMIN_URL?>/shopper_documentation/add.php?sid=<?php echo $row['id']; ?>">
                            <img src="<?php echo ADMIN_URL?>/images/entry-add-button.png" alt="" />
                        </a>
                    </div>
                </div>

                <div class="doc-row-sec">

                    <?php
                    $sql = "SELECT * FROM shopper_documentation
											                  WHERE shopper_program_id = ? AND period_id = ? ORDER BY date_created;";
                    $result = $conn->query($sql, array($row['id'], $_SESSION['admin_period_id']));

                    if ($conn->num_rows() > 0) {
                        $num_results = $conn->num_rows();

                        while ($row_ch = $conn->fetch($result)) { ?>

                            <div class="single-doc-row-sec">
                                <div class="doc-img">
                                    <?php if ($row_ch['image']) {?>
                                    <img src="<?php echo ADMIN_URL?>/timThumb.php?src=/assets/shopper_documentation/<?php echo $row_ch['image']?>&w=130&h=72&zc=1" alt=""/>
                                    <?php }else{?>
                                        <img src="<?php echo SITE_URL?>/assets/documentation_images/no_photo.jpg" alt=""/>
                                <?php }?>
                                </div>
                                <div class="doc-cnt">
                                    <h4>
                                        <?php echo stripslashes($row_ch['title'])?>
                                    </h4>
                                    <p>
                                        <?php echo stripslashes($row_ch['description'])?>
                                    </p>
                                </div>
                                <div class="doc-action">
                                    <a href="<?php echo ADMIN_URL?>/shopper_documentation/edit.php?id=<?php echo $row_ch['id']?>&sid=<?php echo $row['id']?>">
                                        <img src="<?php echo ADMIN_URL?>/images/edit-btn.svg" alt=""/>
                                    </a>
                                    <a href="<?php echo ADMIN_URL?>/shopper_program_entries/edit.php?id=<?php echo $row['id']?>&del_doc=<?php echo $row_ch['id']?>">
                                        <img src="<?php echo ADMIN_URL?>/images/delete-btn.svg" alt=""/>
                                    </a>
                                </div>
                            </div>
                        <?php }
                    }else{?>

                        <div class="single-doc-row-sec">
                            <p>No documentation found</p>
                        </div>

                    <?php }?>
                </div>

            </div>

            <div class="kit-options-sec single-sec">
                <div class="tab-hdr-sec">
                    <h3>KIT OPTIONS</h3>
                    <div class="action-sec">

                        <form action="<?php echo ADMIN_URL?>/shopper_program_entries/edit.php?id=<?php echo $row['id']; ?>" method="POST">
                            <input type="hidden" name="add_bin_alloc" value="1" class="hidden">

                            <select name="shopper_bin_id" class="entry-options">
                                <option value="">SELECT AN OPTION...</option>
                                <?php
                                $sql = "SELECT a.id, a.title FROM shopper_program_bins a
                          															WHERE a.shopper_program_id = ? AND a.active = 1 AND a.id NOT IN (
                          																SELECT shopper_program_bin_id FROM shopper_program_bin_allocations WHERE shopper_program_id = ? AND period_id = ? ) ORDER BY a.title";
                                $items = $conn->query($sql, array($row['id'], $row['id'], $_SESSION['admin_period_id']));
                                if ($conn->num_rows() > 0) {
                                    while ($item = $conn->fetch($items)) {
                                        echo "<option value=\"" . $item['id'] . "\">" . stripslashes($item['title']) . "</option>\n";
                                    }
                                } else {
                                    echo "<option value=\"\">No available kit options found</option>";
                                }
                                ?>
                            </select>
                            <div class="form-group text-box">
                                <input type="number" name="qty" id="qty" class="form-control form-control-sm" style="width: 60px;" placeholder="Qty">
                            </div>

                            <button type="submit" name="add" class="create-btn">
                                <img src="<?php echo ADMIN_URL?>/images/create-button.png"
                            </button>
                        </form>
                    </div>
                </div>

                <div class="kit-grid-sec">

                    <?php
                    $sql = "SELECT a.shopper_program_bin_id, a.qty, b.title, b.image FROM shopper_program_bin_allocations a, shopper_program_bins b
													              WHERE a.shopper_program_bin_id = b.id AND a.shopper_program_id = ? AND a.period_id = ?
                      		              ORDER BY b.title;";
                    $result = $conn->query($sql, array($row['id'], $_SESSION['admin_period_id']));

                    if ($conn->num_rows() > 0) {
                        $num_results = $conn->num_rows();
                        while ($row_ch = $conn->fetch($result)) { ?>

                            <div class="single-kit">
                                <div class="kit-img">
                                    <?php
                                    if ($row_ch['image']) {?>
                                    <img src="<?php echo ADMIN_URL?>/timThumb.php?src=/assets/shopper_program_bins/<?php echo $row_ch['image']?>&w=130&h=85&zc=1" alt=""/>
                                    <?php }else{?>
                                        <img src="<?php echo SITE_URL?>/assets/documentation_images/no_photo.jpg" alt=""/>
                                    <?php }?>
                                </div>
                                <div class="kit-title">
                                    <h4><?php echo stripslashes($row_ch['title'])?></h4>
                                </div>
                                <div class="kit-actions">
                                    <a href="<?php echo ADMIN_URL?>/shopper_program_entries/edit.php?id=<?php echo $row['id']?>&del_alloc=<?php echo $row_ch['shopper_program_bin_id']?>" onClick="return confirm( 'Are you sure you want to delete this item?')">
                                        <img src="<?php echo ADMIN_URL?>/images/kit-delete-button.png" alt=""/>
                                    </a>
                                </div>
                            </div>
                        <?php }
                    }else{?>
                        <div class="single-kit">
                            <p>No kit option found</p>
                        </div>
                    <?php }?>

                </div>

            </div>

            <div class="related-link-sec single-sec">
                <div class="tab-hdr-sec">
                    <h3>RELATED LINKS</h3>
                    <div class="action-sec">
                        <a href="<?php echo ADMIN_URL?>/shopper_related_links/add.php?sid=<?php echo $row['id']; ?>">
                            <img src="<?php echo ADMIN_URL?>/images/entry-add-button.png" alt="" />
                        </a>
                    </div>
                </div>

                <div class="doc-row-sec related-link-list">


                    <?php
                    $sql = "SELECT * FROM shopper_related_links WHERE shopper_program_id = ? AND period_id = ? ORDER BY sort;";
                    $result = $conn->query($sql, array($row['id'], $_SESSION['admin_period_id']));

                    if ($conn->num_rows() > 0) {
                        $num_results = $conn->num_rows();

                        while ($row_ch = $conn->fetch($result)) { ?>

                            <div class="single-doc-row-sec">
                                <div class="doc-cnt">
                                    <?php
                                    if ($row_ch['image']) {?>
                                        <img src="<?php echo ADMIN_URL?>/timThumb.php?src=/assets/shopper_related_links/<?php echo $row_ch['image']?>&w=130&h=85&zc=1" alt=""/>
                                    <?php }else{?>
                                        <img src="<?php echo SITE_URL?>/assets/documentation_images/no_photo.jpg" alt=""/>
                                    <?php }?>
                                    <h4><?php echo  stripslashes($row_ch['title'])?></h4>
                                    <p><?php echo  stripslashes($row_ch['description'])?></p>

                                    <p>
                                        <a href="<?php echo $row_ch['url']?>">
                                            <?php echo $row_ch['url']?>
                                        </a>
                                    </p>
                                </div>
                                <div class="doc-action">
                                    <a href="<?php echo ADMIN_URL?>/shopper_related_links/edit.php?id=<?php echo $row_ch['id']?>&sid=<?php echo $row['id']?>">
                                        <img src="<?php echo ADMIN_URL?>/images/edit-btn.svg" alt=""/>
                                    </a>
                                    <a href="<?php echo ADMIN_URL?>/shopper_program_entries/edit.php?id=<?php echo $row['id']?>&del_rel=<?php echo $row_ch['id']?>" onClick="return confirm( 'Are you sure you want to delete this item?')">
                                        <img src="<?php echo ADMIN_URL?>/images/delete-btn.svg" alt=""/>
                                    </a>
                                </div>
                            </div>
                        <?php }
                    }else{?>
                        <div class="single-doc-row-sec">
                           No related links found
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
        updateCountdown('#title', 85, '#title_lbl');
        updateCountdown('#intro', 255, '#intro_lbl');
    });

    function updateCountdown(input, limit, lbl) {
        var remaining = limit - $(input).val().length;
        $(lbl).text(remaining + ' characters remaining.');
    }
</script>
<script>
    $(document).ready(function () {

        var datefield = document.createElement("input")
        datefield.setAttribute("type", "date")

        if (datefield.type != "date") { //if browser doesn't support input type="date", initialize date picker widget:

            $('#start_date').datepicker();
            $('#end_date').datepicker();
        }

        $('#submit').click(function () {
            if (!hasHtml5Validation())
                return validateForm();
        });
    });
    function validateForm() {

        if ($('#title').val() == '')
            return createError('title', 'Please enter a valid title');
        if ($('#start_date').val() == '')
            return createError('start_date', 'Please enter a valid start date');
        if ($('#end_date').val() == '')
            return createError('end_date', 'Please enter a valid end date');
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
                    $('+ .update-cnt-sec', this).toggle();
                } else {
                    $(this).addClass('open');
                    $('+ .update-cnt-sec', this).toggle();
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
            $('li[data-tab="kit-options-sec"]').click(function() {
                $('.single-sec').hide();
                $('.prior-updates-sec').hide();
                $('.kit-options-sec.single-sec').show();
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
