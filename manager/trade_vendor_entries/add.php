<?php
$title =  'trades';
$subtitle = 'trade_entries';
require( '../config.php' );
require( '../includes/pdo.php' );
require( '../includes/check_login.php' );
require( '../includes/VendorManager.php' );
$Vendor = new VendorManager($conn);
$msg = '';
?>
<?php require( '../includes/header_new.php' );?>

    <div class="dashboard-sub-menu-sec">
        <div class="container">
            <div class="sub-menu-sec">
                <?php require( '../includes/trade_sub_nav.php' );?>
            </div>
        </div>
    </div>

    <div class="latest_activities hd-grid">
        <div class="container">
            <div class="heading_sec">
                <h2><bold>Add</bold> A TRADE entry</h2>
                <div class="back-btn">
                    <a href="javascript:void(0)" onclick="window.location.href = '<?php echo ADMIN_URL?>/trade_vendor_entries/index.php'">
                        <img src="<?php echo ADMIN_URL?>/images/back-button.png" alt="" />
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="entry-section shopper-partner add-entry-sec">
        <div class="container">
            <?php if ($msg) echo "<div class=\"alert alert-danger\">$msg</div>"; ?>
            <form action="<?php echo ADMIN_URL?>/trade_vendor_entries/edit.php" role="form" method="GET" onSubmit="return validateForm();">
                <div class="option-sec">
                    <select name="id" id="id" class="entry-options" required>
                        <option value="">Select an option...</option>
                        <?php echo $Vendor->getVendorDropdown($_SESSION['admin_period_id'], ( $msg ) ? $_POST['id'] : 0 ); ?>
                    </select>
                </div>
                <div class="action-sec">
                    <button type="submit" class="create-btn">
                        <img src="<?php echo ADMIN_URL?>/images/create-button.png"
                    </button>
                    <button onClick="window.location.href = '<?php echo ADMIN_URL?>/trade_vendor_entries/index.php';" type="button" id="cancel" name="cancel" class="cancel-btn">
                        <img src="<?php echo ADMIN_URL?>/images/cancel-button.png"
                    </button>

                </div>
            </form>

        </div>
    </div>
    <script>
        $(document).ready(function () {
            $('form:first *:input[type!=hidden]:first').focus();
        });
    </script>
    <script type="text/javascript" src="../includes/tinymce/tinymce.min.js"></script>
    <script type="text/javascript">
        $().ready(function () {
            updateCountdown('#title', 65, '#title_lbl');
        });

        function updateCountdown(input, limit, lbl) {
            var remaining = limit - $(input).val().length;
            $(lbl).text(remaining + ' characters remaining.');
        }
    </script>
<?php $conn->close(); ?>
<?php include('../includes/footer_new.php');?>