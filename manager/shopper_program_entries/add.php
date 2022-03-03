<?php
$title =  'shoppers';
$subtitle = 'shopper_entries';
require( '../config.php' );
require( '../includes/pdo.php' );
require( '../includes/check_login.php' );
require( '../includes/ShopperProgramManager.php' );
$ShopperProgram = new ShopperProgramManager($conn);
$msg = '';
?>

<?php require( '../includes/header_new.php' );?>

    <div class="dashboard-sub-menu-sec shopper-nav">
        <div class="container">
            <div class="sub-menu-sec">
                <?php require( '../includes/shopper_sub_nav.php' );?>
            </div>
        </div>
    </div>

    <div class="latest_activities hd-grid activity_log">
        <div class="container">
            <div class="heading_sec">
                <h2><bold>Add A</bold> SHOPPER ENTRY</h2>
            </div>
            <div class="add-new-entry-sec">
                <button type="button" id="cancel" name="cancel" class="btn btn-primary back-btn" onclick="window.location.href = '<?php echo ADMIN_URL?>/shopper_program_entries/';">
                    <img src="<?php echo ADMIN_URL?>/images/back-button.png" alt="back">
                </button>
            </div>
        </div>
    </div>


    <div class="entry-section shopper-partner add-entry-sec">
        <div class="container">
            <?php if ($msg) echo "<div class=\"alert alert-danger\">$msg</div>"; ?>
            <form action="<?php echo ADMIN_URL?>/shopper_program_entries/edit.php" role="form"  method="GET">

                <div class="option-sec">
                    <select class="entry-options" name="id" id="id" required>
                        <option value="">Select an option...</option>
                        <?php echo $ShopperProgram->getProgramDropDown($_SESSION['admin_period_id'], ( $msg ) ? $_POST['id'] : 0 ); ?>
                    </select>
                </div>
                <div class="action-sec">
                    <button type="submit" class="create-btn">
                        <img src="<?php echo ADMIN_URL?>/images/create-button.png"
                    </button>
                    <button onClick="window.location.href = '<?php echo ADMIN_URL?>/shopper_program_entries/index.php';" type="button" id="cancel" name="cancel" class="cancel-btn">
                        <img src="<?php echo ADMIN_URL?>/images/cancel-button.png"
                    </button>

                </div>
            </form>

        </div>
    </div>
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
            return true;
        }

        function hasHtml5Validation() {
            return typeof document.createElement('input').checkValidity === 'function';
        }
    </script>

<?php $conn->close(); ?>
<?php include('../includes/footer_new.php');?>