<?php
session_start();
require( '../includes/pdo.php' );
require( '../includes/check_login.php' );
require( '../includes/EventManager.php' );
$Event = new EventManager($conn);
$msg = '';

// check variables passed in through querystring
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$update = isset($_POST['update']) ? (int) $_POST['update'] : 0;

// if an id is passed in then let's fetch it from the database
if ($id) {
    if (!( $row = $Event->getByID($id) )) {
        $msg = "Sorry, a record with the specified ID could not be found!";
    }


    $del_image = ( isset($_GET['del_image']) ) ? (int) $_GET['del_image'] : 0;
    if ($del_image) {
        $Event->removeImage($id);
        $row['image'] = '';
    }
} else if ($update) { // if the user submitted a form....
    $token = $_SESSION['upd_token'];
    unset($_SESSION['upd_token']);

    if (!$msg && $token != '' && $token == $_POST['upd_token']) {
        if (!$Event->update($_POST['update'], $_POST['title'], $_POST['description'], $_POST['event_date'], $_POST['image'], $_POST['category_id'], $_POST['featured'], $_POST['active'])) {
            $msg = "Sorry, an error has occurred, please contact your administrator.<br>Error: " . $Event->error() . ";";
        } else {
            header("Location: index.php");
        }
    }
}

// create a token for secure deletion (from this page only and not remote)
$_SESSION['upd_token'] = md5(uniqid());
session_write_close();
?>
<!DOCTYPE html>
<html>

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Edit a <?php echo ENTITY; ?></title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
        <link href="/manager/font-awesome/css/font-awesome.min.css" rel="stylesheet">
        <link href="/manager/css/imagine.css" rel="stylesheet">
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300' rel='stylesheet' type='text/css'>
        <script>$(document).ready(function () {
                $('form:first *:input[type!=hidden]:first').focus();
            });</script>
        <script type="text/javascript" src="../includes/tinymce/tinymce.min.js"></script>
        <link rel="icon" href="/assets/cropped-favicon-150x150.png" sizes="32x32">

        <script type="text/javascript">tinymce.init({
                selector: "textarea#description",
                plugins: ["link image hr fullscreen media table textcolor code filemanager lists advlist"],
                toolbar: "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link code responsivefilemanager table | forecolor backcolor",
                external_filemanager_path: "/manager/includes/tinymce/plugins/filemanager/",
                filemanager_title: "File manager", relative_urls: false, image_advtab: true,
                external_plugins: {"filemanager": "/manager/includes/tinymce/plugins/filemanager/plugin.min.js"}
            });
        </script>
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
                            <h3>Edit an <?php echo ENTITY; ?></h3>
                        </div>
                    </div>

                    <ol class="breadcrumb bc-3">
                        <li><a href="/manager/menu.php">Dashboard</a></li>
                        <li>&nbsp;/&nbsp;</li>
                        <li><a href="/manager/events/">Events</a></li>
                        <li>&nbsp;/&nbsp;</li>
                        <li><strong>Edit an <?php echo ENTITY; ?></strong></li>
                    </ol>

                    <div class="row my-4 mgr_body news news_edit program_edit">
                        <div class="col-lg-10 col-md-8">

                            <form action="edit.php" role="form" method="POST" onSubmit="return validateForm();">
                                <input type="hidden" name="update" value="<?php echo $row['id']; ?>">
                                <input type="hidden" name="upd_token" value="<?php echo $_SESSION['upd_token']; ?>">

                                <?php if ($msg) echo "<div class=\"alert alert-danger\">$msg</div>"; ?>

                                <div class="form-group row">
                                    <div class="col-sm-12">
                                        <button type="button" id="cancel" name="cancel" class="btn btn-default back_btn float-right" onclick="window.location.href = 'index.php';">Back</button>
                                    </div>
                                </div>

                                <table style="width: 100%;" class="add_edit">
                                    <tr>
                                        <td>
                                            <h3>Title *</h3>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="col-sm-12">
                                                <input type="text" class="form-control" id="title" name="title" onKeyUp="updateCountdown('#title', 65, '#title_lbl');" placeholder="" required onKeyDown="updateCountdown('#title', 65, '#title_lbl');" value="<?php echo ( $msg ) ? $_POST['title'] : $row['title']; ?>" maxlength="65">
                                                <span id="title_lbl" class="small"></span>
                                            </div>
                                        </td>
                                    </tr>
                                </table>

                                <!--<div class="form-group row">
                                    <label for="title" class="col-sm-4 col-form-label">Title <span class="required_sign">*</span></label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="title" name="title" onKeyUp="updateCountdown('#title', 65, '#title_lbl');" placeholder="" required onKeyDown="updateCountdown('#title', 65, '#title_lbl');" value="<?php echo ( $msg ) ? $_POST['title'] : $row['title']; ?>" maxlength="65">
                                        <span id="title_lbl" class="small"></span>
                                    </div>
                                </div>-->

                                <table style="width: 100%;" class="add_edit">
                                    <tr>
                                        <td>
                                            <h3>Description *</h3>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="col-sm-12">
                                                <textarea id="description" name="description" class="form-control" rows="3"><?php echo ( $msg ) ? $_POST['description'] : $row['description']; ?></textarea>
                                            </div>
                                        </td>
                                    </tr>
                                </table>

                                <!--<div class="form-group row">
                                    <label for="description" class="col-sm-4 col-form-label">Description <span class="required_sign">*</span></label>
                                    <div class="col-sm-8">
                                        <textarea id="description" name="description" class="form-control" rows="3"><?php echo ( $msg ) ? $_POST['description'] : $row['description']; ?></textarea>
                                    </div>
                                </div>-->

                                <table style="width: 100%;" class="add_edit">
                                    <tr>
                                        <td>
                                            <h3>Event Date *</h3>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="col-sm-12">
                                                <input type="date" id="event_date" name="event_date" class="form-control" placeholder="" required value="<?php echo ( $msg ) ? $_POST['event_date'] : $row['event_date']; ?>">
                                            </div>
                                        </td>
                                    </tr>
                                </table>

                                <!--<div class="form-group row">
                                    <label for="event_date" class="col-sm-4 col-form-label">Event Date <span class="required_sign">*</span></label>
                                    <div class="col-sm-8">
                                        <input type="date" id="event_date" name="event_date" class="form-control" placeholder="" required value="<?php echo ( $msg ) ? $_POST['event_date'] : $row['event_date']; ?>">
                                    </div>
                                </div>-->

                                <?php if ($row['image'] != '') { ?>
                                    <table style="width: 100%;" class="add_edit">
                                        <tr>
                                            <td>
                                                <h3>Image</h3>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="col-sm-12">
                                                    <img src="/manager/timThumb.php?src=/assets/events/<?php echo $row['image']; ?>&w=200" style="border: 1px solid #CCCCCC;padding: 2px;margin: 4px;">
                                                    <br>
                                                    <a href="edit.php?id=<?php echo $id; ?>&del_image=1" class="btn action_btn cancel" style="margin: 4px;width: 206px;">Remove Image</a>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                <?php } else { ?>
                                    <table style="width: 100%;" class="add_edit">
                                        <tr>
                                            <td>
                                                <h3>Image</h3>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="col-sm-12">
                                                    <input type="text" class="form-control" id="image" name="image" placeholder="Click to upload" onfocus="this.blur();" onclick="window.open('../includes/tinymce/plugins/filemanager/dialog.php?type=1&fldr=events&field_id=image&popup=1', '<?php echo time(); ?>', 'width=900,height=550,toolbar=0,menubar=0,location=0,status=1,scrollbars=1,resizable=1,left=0,top=0');return false;" >
                                                </div>
                                                <script>
                                                    function responsive_filemanager_callback(field_id) {
                                                        var url = jQuery('#' + field_id).val();
                                                        url = url.replace("https://<?php echo $_SERVER['HTTP_HOST']; ?>/assets/events/", '');
                                                        if ( url.length > 65 ) {
                                                          alert('The length of your file name is over the limit of 65 characthers. Please rename your file and try again.');
                                                          jQuery('#' + field_id).val('');
                                                        } else {
                                                          jQuery('#' + field_id).val(url);
                                                        }
                                                    }
                                                </script>
                                            </td>
                                        </tr>
                                    </table>
                                <?php } ?>



                                <?php /* if ($row['image'] != '') { ?>
                                  <div class="form-group row">
                                  <label for="image" class="col-sm-4 col-form-label">Image</label>
                                  <div class="col-sm-8">
                                  <img src="/manager/timThumb.php?src=/assets/events/<?php echo $row['image']; ?>&w=200&h=80" style="border: 1px solid #CCCCCC;padding: 2px;margin: 4px;">
                                  <br>
                                  <a href="edit.php?id=<?php echo $id; ?>&del_image=1">Remove Image</a>
                                  </div>
                                  </div>
                                  <?php } else { ?>
                                  <div class="form-group row">
                                  <label for="image" class="col-sm-4 col-form-label">Image</label>
                                  <div class="col-sm-8">
                                  <input type="text" class="form-control" id="image" name="image" placeholder="Click to upload" onfocus="this.blur();" onclick="window.open('../includes/tinymce/plugins/filemanager/dialog.php?type=1&fldr=events&field_id=image&popup=1', '<?php echo time(); ?>', 'width=900,height=550,toolbar=0,menubar=0,location=0,status=1,scrollbars=1,resizable=1,left=0,top=0');return false;" >
                                  </div>
                                  </div>
                                  <script>
                                  function responsive_filemanager_callback(field_id) {
                                  var url = jQuery('#' + field_id).val();
                                  url = url.replace("https://<?php echo $_SERVER['HTTP_HOST']; ?>/assets/events/", '');
                                  jQuery('#' + field_id).val(url);
                                  }
                                  </script>
                                  <?php } */ ?>

                                <table style="width: 100%;" class="add_edit">
                                    <tr>
                                        <td>
                                            <h3>Category</h3>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="col-sm-12">
                                                <select name="category_id" id="category_id" class="form-control" >
                                                    <option value="">Select an option...</option>
                                                    <?php echo $Event->getEvent_categories_CategoryDropdown(( $msg ) ? $_POST['category_id'] : $row['category_id'] ); ?>
                                                </select>
                                            </div>
                                        </td>
                                    </tr>
                                </table>

                                <!--<div class="form-group row">
                                    <label for="category_id" class="col-sm-4 col-form-label">Category</label>
                                    <div class="col-sm-8">
                                        <select name="category_id" id="category_id" class="form-control" >
                                            <option value="">Select an option...</option>
                                <?php // echo $Event->getEvent_categories_CategoryDropdown(( $msg ) ? $_POST['category_id'] : $row['category_id'] ); ?>
                                        </select>
                                    </div>
                                </div>-->

                                <table style="width: 100%;" class="add_edit">
                                    <tr>
                                        <td>
                                            <h3>Featured</h3>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="col-sm-12">
                                                <div class="form-check">
                                                    <input name="featured" id="featured" type="checkbox" value="1" <?php if (isset($_POST['featured']) || (int) $row['featured']) echo "CHECKED"; ?> class="form-check-input"><span>Featured</span>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </table>

                                <!--<div class="form-group row">
                                    <div class="col-sm-4"><label for="featured">Featured</label></div>
                                    <div class="col-sm-8">
                                        <div class="form-check">
                                            <input name="featured" id="featured" type="checkbox" value="1" <?php if (isset($_POST['featured']) || (int) $row['featured']) echo "CHECKED"; ?> class="form-check-input">
                                        </div>
                                    </div>
                                </div>-->

                                <table style="width: 100%;" class="add_edit">
                                    <tr>
                                        <td>
                                            <h3>Status</h3>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="col-sm-12">
                                                <div class="form-check">
                                                    <input name="active" id="active" type="checkbox" value="1" <?php if (isset($_POST['active']) || (int) $row['active']) echo "CHECKED"; ?> class="form-check-input"><span>Active</span>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </table>

                                <!--<div class="form-group row">
                                    <div class="col-sm-4"><label for="active">Active</label></div>
                                    <div class="col-sm-8">
                                        <div class="form-check">
                                            <input name="active" id="active" type="checkbox" value="1" <?php if (isset($_POST['active']) || (int) $row['active']) echo "CHECKED"; ?> class="form-check-input">
                                        </div>
                                    </div>
                                </div>-->


                                <div class="form-group row">
                                    <div class="col-sm-12">
                                        <button type="submit" class="btn action_btn save float-right">Update</button>
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

                                                var datefield = document.createElement("input")
                                                datefield.setAttribute("type", "date")

                                                if (datefield.type != "date") { //if browser doesn't support input type="date", initialize date picker widget:

                                                    $('#event_date').datepicker();
                                                }

                                                $('#submit').click(function () {
                                                    if (!hasHtml5Validation())
                                                        return validateForm();
                                                });
                                            });
                                            function validateForm() {

                                                if ($('#title').val() == '')
                                                    return createError('title', 'Please enter a valid title');
                                                if ($('#event_date').val() == '')
                                                    return createError('event_date', 'Please enter a valid event date');
                                            }
                                            function hasHtml5Validation() {
                                                return typeof document.createElement('input').checkValidity === 'function';
                                            }
        </script>

    </body>

</html>
<?php $conn->close(); ?>
