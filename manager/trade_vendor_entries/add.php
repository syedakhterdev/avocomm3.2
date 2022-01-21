<?php
session_start();
require( '../includes/pdo.php' );
require( '../includes/check_login.php' );
require( '../includes/VendorManager.php' );
$Vendor = new VendorManager($conn);
$msg = '';
?>
<!DOCTYPE html>
<html>

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Add a Trade Entry</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
        <link href="/manager/font-awesome/css/font-awesome.min.css" rel="stylesheet">
        <link href="/manager/css/imagine.css" rel="stylesheet">
        <link rel="icon" href="/assets/cropped-favicon-150x150.png" sizes="32x32">
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300' rel='stylesheet' type='text/css'>
        <script>$(document).ready(function () {
                $('form:first *:input[type!=hidden]:first').focus();
            });</script>
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
    </head>

    <body>

        <?php include( '../includes/header.php' ); ?>

        <div class="container-fluid" id="main">
            <div class="row row-offcanvas row-offcanvas-left">

                <?php include( '../includes/nav.php' ); ?>

                <div class="col main pt-5 mt-3">
                    <div class="row mgr_heading">
                        <div class="col-lg-10">
                            <h3>Add a Trade Entry</h3>
                        </div>
                    </div>

                    <ol class="breadcrumb bc-3">
                        <li><a href="/manager/menu.php">Dashboard</a></li>
                        <li>&nbsp;/&nbsp;</li>
                        <li><a href="/manager/trade_vendor_entries/">Trade Entries</a></li>
                        <li>&nbsp;/&nbsp;</li>
                        <li><strong>Add a Trade Entry</strong></li>
                    </ol>

                    <div class="row my-4 mgr_body add_entry">
                        <div class="col-lg-10 col-md-8">

                            <form action="edit.php" role="form" method="GET" onSubmit="return validateForm();">

                                <?php if ($msg) echo "<div class=\"alert alert-danger\">$msg</div>"; ?>


                                <div class="form-group row">
                                    <div class="col-sm-12">
                                        <button type="button" id="cancel" name="back" class="btn btn-default back_btn float-right" onclick="window.location.href = 'index.php';">Back</button>
                                    </div>
                                </div>


                                <div class="form-group row">
                                    <!--<label for="shopper_program_id" class="col-sm-4 col-form-label">Program <span class="required_sign">*</span></label>-->

                                    <div class="col-sm-12">
                                        <div class="row">
                                            <div class="col-sm-8">
                                                <select name="id" id="id" class="form-control" required>
                                                    <option value="">Select an option...</option>
                                                    <?php echo $Vendor->getVendorDropdown($_SESSION['admin_period_id'], ( $msg ) ? $_POST['id'] : 0 ); ?>
                                                </select>
                                            </div>
                                            <div class="col-sm-4">
                                                <button type="submit" class="btn btn-primary">Create</button>
                                                <button type="button" id="cancel" name="cancel" class="btn btn-default" onClick="window.location.href = 'index.php';">Cancel</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <!--<div class="form-group row">
                                    <div class="col-sm-12">
                                        <button type="submit" class="btn btn-primary">Create</button>
                                        <button type="button" id="cancel" name="cancel" class="btn btn-default" onClick="window.location.href = 'index.php';">Cancel</button>
                                    </div>
                                </div>-->

                            </form>
                        </div>

                    </div>
                    <!--/row-->

                    <footer class="container-fluid">
                        <p class="text-right small">©2019 All rights reserved.</p>
                    </footer>

                </div>
                <!--/main col-->

            </div>

        </div>
        <!--/.container-->
        <!-- Core Scripts - Include with every page -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
        <script src="/manager/js/imagine.js"></script>

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
                                                return true;
                                            }

                                            function hasHtml5Validation() {
                                                return typeof document.createElement('input').checkValidity === 'function';
                                            }
        </script>

    </body>

</html>
<?php $conn->close(); ?>