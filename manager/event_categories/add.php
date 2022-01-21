<?php
session_start();
require( '../includes/pdo.php' );
require( '../includes/check_login.php' );
require( '../includes/EventCategoryManager.php' );
$EventCategory = new EventCategoryManager($conn);
$msg = '';

$insert = isset($_POST['insert']) ? (int) $_POST['insert'] : 0;


if ($insert) {
    $token = $_SESSION['add_token'];
    unset($_SESSION['add_token']);

    if (!$msg) {
        if ($token != '' && $token == $_POST['add_token']) {
            if (!$id = $EventCategory->add($_POST['category'])) {
                $msg = "Sorry, an error has occurred, please contact your administrator.<br>Error: " . $EventCategory->error() . ";";
            } else {
                header("Location: index.php");
            }
        } else {
            $msg = 'Sorry, an error has occurred, please go back and try again.';
        }
    }
}

// create a token for secure deletion (from this page only and not remote)
$_SESSION['add_token'] = md5(uniqid());
session_write_close();
?>
<!DOCTYPE html>
<html>

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Add a <?php echo ENTITY; ?></title>
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
                updateCountdown('#category', 65, '#category_lbl');
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
                            <h3>Add an <?php echo ENTITY; ?></h3>
                        </div>
                    </div>

                    <ol class="breadcrumb bc-3">
                        <li><a href="/manager/menu.php">Dashboard</a></li>
                        <li>&nbsp;/&nbsp;</li>
                        <li><a href="/manager/event_categories/">Event Categories</a></li>
                        <li>&nbsp;/&nbsp;</li>
                        <li><strong>Add an Event Category</strong></li>
                    </ol>

                    <div class="row my-4 mgr_body program_edit event_category event_category_add">
                        <div class="col-lg-10 col-md-8">

                            <form action="add.php" role="form" method="POST" onSubmit="return validateForm();">
                                <input type="hidden" name="insert" value="1">
                                <input type="hidden" name="add_token" value="<?php echo $_SESSION['add_token']; ?>">

                                <?php if ($msg) echo "<div class=\"alert alert-danger\">$msg</div>"; ?>

                                <div class="form-group row">
                                    <div class="col-sm-12">
                                        <button type="button" id="cancel" name="cancel" class="btn btn-default back_btn float-right" onclick="window.location.href = 'index.php';">Back</button>
                                    </div>
                                </div>

                                <table style="width: 100%;" class="add_edit">
                                    <tr>
                                        <td><h3>Category *</h3></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="col-sm-12">
                                                <input type="text" class="form-control" id="category" name="category" onKeyUp="updateCountdown('#category', 65, '#category_lbl');" placeholder="" required onKeyDown="updateCountdown('#category', 65, '#category_lbl');" value="<?php echo ( $msg ) ? $_POST['category'] : ''; ?>" maxlength="65">
                                                <span id="category_lbl" class="small"></span>
                                            </div>
                                        </td>
                                    </tr>
                                </table>

                                <!--<div class="form-group row">
                                    <label for="category" class="col-sm-4 col-form-label">Category <span class="required_sign">*</span></label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="category" name="category" onKeyUp="updateCountdown('#category', 65, '#category_lbl');" placeholder="" required onKeyDown="updateCountdown('#category', 65, '#category_lbl');" value="<?php echo ( $msg ) ? $_POST['category'] : ''; ?>" maxlength="65">
                                        <span id="category_lbl" class="small"></span>
                                    </div>
                                </div>-->


                                <div class="form-group row">
                                    <div class="col-sm-12">
                                        <button type="submit" class="btn action_btn save float-right">Create</button>
                                        <button type="button" id="cancel" name="cancel" class="btn action_btn cancel float-right" onClick="window.location.href = 'index.php';">Cancel</button>
                                    </div>
                                </div>

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

                                                if ($('#category').val() == '')
                                                    return createError('category', 'Please enter a valid category');
                                                return true;
                                            }

                                            function hasHtml5Validation() {
                                                return typeof document.createElement('input').checkValidity === 'function';
                                            }
        </script>

    </body>

</html>
<?php $conn->close(); ?>