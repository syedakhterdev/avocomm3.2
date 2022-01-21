<?php
session_start();
require( '../includes/pdo.php' );
require( '../includes/check_login.php' );
require( '../includes/FSProgramManager.php' );
$FSProgram = new FSProgramManager($conn);
$msg = '';

// check variables passed in through querystring
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$update = isset($_POST['update']) ? (int) $_POST['update'] : 0;

// if an id is passed in then let's fetch it from the database
if ($id) {
    if (!( $row = $FSProgram->getByID($id) )) {
        $msg = "Sorry, a record with the specified ID could not be found!";
    }

    $del_doc = isset($_GET['del_doc']) ? (int) $_GET['del_doc'] : 0;
    if ($del_doc) {
        if ($FSProgram->removeDocumentation($id, $del_doc, $_SESSION['admin_period_id'])) {
            $child_msg = "Documentation deleted successfully!";
        } else {
            $child_msg = "Documentation could not be deleted!";
        }
    }

    $del_upd = isset($_GET['del_upd']) ? (int) $_GET['del_upd'] : 0;
    if ($del_upd) {
        if ($FSProgram->removeUpdates($id, $_SESSION['admin_period_id'], $del_upd)) {
            $child_msg = "Update deleted successfully!";
        } else {
            $child_msg = "Update could not be deleted!";
        }
    }

    $del_rel = isset($_GET['del_rel']) ? (int) $_GET['del_rel'] : 0;
    if ($del_rel) {
        if ($FSProgram->removeRelatedLinks($id, $_SESSION['admin_period_id'], $del_rel)) {
            $child_msg = "RelatedLink deleted successfully!";
        } else {
            $child_msg = "RelatedLink could not be deleted!";
        }
    }

    $del_image = ( isset($_GET['del_image']) ) ? (int) $_GET['del_image'] : 0;
    if ($del_image) {
        $FSProgram->removeImage($id);
        $row['image'] = '';
    }
} else if ($update) { // if the user submitted a form....
    $token = $_SESSION['upd_token'];
    unset($_SESSION['upd_token']);

    if (!$msg && $token != '' && $token == $_POST['upd_token']) {
        if (!$FSProgram->update($_POST['update'], $_POST['title'], $_POST['image'], $_POST['start_date'], $_POST['end_date'], $_POST['intro'], $_POST['fs_category_id'], $_POST['sort'], $_POST['active'])) {
            $msg = "Sorry, an error has occurred, please contact your administrator.<br>Error: " . $FSProgram->error() . ";";
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
        <link rel="icon" href="/assets/cropped-favicon-150x150.png" sizes="32x32">
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300' rel='stylesheet' type='text/css'>
        <script>$(document).ready(function () {
                $('form:first *:input[type!=hidden]:first').focus();
            });</script>
        <script type="text/javascript" src="../includes/tinymce/tinymce.min.js"></script>

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
    </head>

    <body>
        <?php include( '../includes/header.php' ); ?>

        <div class="container-fluid" id="main">
            <div class="row row-offcanvas row-offcanvas-left">

                <?php include( '../includes/nav.php' ); ?>

                <div class="col main pt-5 mt-3">
                    <div class="row mgr_heading">
                        <div class="col-lg-10">
                            <h3>Edit a Foodservice Entry</h3>
                        </div>
                    </div>

                    <ol class="breadcrumb bc-3">
                        <li><a href="/manager/menu.php">Dashboard</a></li>
                        <li>&nbsp;/&nbsp;</li>
                        <li><a href="/manager/fs_program_entries/">Foodservice Entries</a></li>
                        <li>&nbsp;/&nbsp;</li>
                        <li><strong>Edit A Foodservice Entry</strong></li>
                    </ol>

                    <div class="row my-4 mgr_body foodservice program_edit">
                        <div class="col-lg-10 col-md-8">

                            <form action="edit.php" role="form" method="POST" onSubmit="return validateForm();">
                                <input type="hidden" name="update" value="<?php echo $row['id']; ?>">
                                <input type="hidden" name="upd_token" value="<?php echo $_SESSION['upd_token']; ?>">

                                <div class="form-group row">
                                    <div class="col-sm-12">

                                      <div class="clone_section float-left">
                                        <?php
                                        if ( isset( $_GET['cpid'] ) && (int)$_GET['cpid'] ) {
                                          $cpid = (int)$_GET['cpid'];

                                          // copy over the updates
                                          $sql = 'INSERT INTO fs_program_updates (
                                                    SELECT NULL, NOW(), ?, ?, description, updates
                                                    FROM fs_program_updates WHERE fs_program_id = ? AND period_id = ?
                                                  );';
                                          $conn->exec( $sql, array( $id, $cpid, $id, $_SESSION['admin_period_id'] ) );

                                          // copy over the documentation
                                          $sql = 'INSERT INTO fs_documentation (
                                                    SELECT NULL, NOW(), ?, ?, title, description, image, document, document_type_id, active, download_count
                                                    FROM fs_documentation WHERE fs_program_id = ? AND period_id = ?
                                                  );';
                                          $conn->exec( $sql, array( $id, $cpid, $id, $_SESSION['admin_period_id'] ) );

                                          // copy over the related links
                                          $sql = 'INSERT INTO fs_related_links (
                                                    SELECT NULL, ?, ?, title, description, image, url, sort
                                                    FROM fs_related_links WHERE fs_program_id = ? AND period_id = ?
                                                  );';
                                          $conn->exec( $sql, array( $cpid, $id, $id, $_SESSION['admin_period_id'] ) );

                                          echo '<strong>Your entry was cloned successfully!';

                                        } else {
                                          $sql = 'SELECT id, title FROM periods WHERE id NOT IN ( SELECT period_id FROM fs_program_updates WHERE fs_program_id = ? )
                                                  ORDER BY year ASC, month ASC';
                                          $periods = $conn->query( $sql, array( $id ) );
                                          if ( $conn->num_rows() > 0 ) {
                                            echo '<h3>Clone To </h3><select name="clone_period_id" onChange="if ( confirm( ' . "'Are you sure you want to clone this entry to another period?'" . ' ) ) { window.location=' . "'edit.php?id=$id&cpid=' + this.value;" . ' } else { this.value = ' . "''" . '; }">';
                                            echo '<option value="">Select a period</option>';
                                            while ( $period = $conn->fetch( $periods ) ) {
                                              echo '<option value="' . $period['id'] . '">' . stripslashes( ucwords( strtolower( $period['title'] ) ) ) . "</option>\n";
                                            }
                                            echo '</select>';
                                          }
                                        }
                                        ?>
                                      </div>

                                        <button type="button" id="cancel" name="cancel" class="btn btn-default back_btn float-right" onClick="window.location.href = 'index.php';">Back</button>
                                    </div>
                                </div>

                                <?php if ($msg) echo "<div class=\"alert alert-danger\">$msg</div>"; ?>

                                <div class="form-group row">
                                    <div class="col-sm-8">
                                        <?php if ($row['image'] != '') { ?>
                                            <img src="/manager/timThumb.php?src=/assets/fs_programs/<?php echo $row['image']; ?>&h=60" style="border: 1px solid #CCCCCC;padding: 2px;margin: 4px;float: left;">
                                        <?php } ?>
                                        <h1 class="my-3"><?php echo $row['title']; ?></h1>
                                    </div>
                                </div>


                            </form>

                            <!-- // FOR CURRENT PERIOD -->
                            <!--<h3 style="margin-top: 12px;font-size: 1.55rem;">For Current Period</h3>-->

                            <table style="width: 100%;">
                                <?php
                                $sql = "SELECT id, description, updates FROM fs_program_updates
											WHERE fs_program_id = ? AND period_id = ? ORDER BY id;";
                                $result = $conn->query($sql, array($row['id'], $_SESSION['admin_period_id']));

                                if ($conn->num_rows() > 0) {
                                    $num_results = $conn->num_rows();

                                    while ($row_ch = $conn->fetch($result)) {
                                        echo "
                                                <tr>
                                                    <td align=\"right\">
                                                        <h3 class=\"float-left\">For Current Period</h3>
                                                        <a href=\"../fs_program_updates/edit.php?id=" . $row_ch['id'] . "&sid=" . $row['id'] . "\" title=\"Edit\" class=\"action_btn edit\">EDIT</a>
                                                        <a href=\"edit.php?id=" . $row['id'] . "&del_upd=" . $row_ch['id'] . "\" onClick=\"return confirm( 'Are you sure you want to delete this item?')\" title=\"Delete\" class=\"action_btn delete\">DELETE</a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style=\"width: auto;\"><h4>Description</h4>" . stripslashes($row_ch['description']) . "<h4>Updates</h4>" . stripslashes($row_ch['updates']) . "</td>
                                                </tr>\n";
                                    }
                                } else {
                                    echo "
                                                <tr>
                                                    <td align=\"right\">
                                                        <h3 class=\"float-left\">For Current Period</h3>
                                                        <a href=\"../fs_program_updates/add.php?sid=" . $row['id'] . "\" class=\"float-right action_btn no-icn\">Add</a>
                                                    </td>";
                                    echo '<tr><td colspan="2">No updates found</td></tr>';
                                    echo "</tr>\n";
                                }

                                $sql = 'SELECT a.id, a.period_id, a.fs_program_id, b.title FROM fs_program_updates a, periods b WHERE a.period_id = b.id AND a.fs_program_id = ? AND a.period_id <> ? ORDER BY b.year DESC, month DESC';
                                $upds = $conn->query( $sql, array( $id, $_SESSION['admin_period_id'] ) );

                                if ( $conn->num_rows() > 0 ) {
                                  echo '<tr><td>';
                                  echo '<h3>Prior Updates</h3>';
                                  while ( $upd = $conn->fetch( $upds ) ) {
                                    echo '<a href="/manager/selPeriod.php?id=' . $upd['period_id'] . '&fsid=' . $upd['fs_program_id'] . '">' . stripslashes( $upd['title'] ) . '</a> ';
                                  }
                                  echo '</td></tr>';
                                }

                                /* if ($conn->num_rows() == 0) {
                                  echo '<tr><td style="text-align:right;" colspan="2"><a href="../fs_program_updates/add.php?sid=' . $row['id'] . '">Add</a></td></tr>';
                                  } */
                                ?>
                            </table>

                            <!--<h4 style="margin-top: 20px;">Documentation</h4>-->
                            <table style="width: 100%;">
                                <tr>
                                    <td colspan="2">
                                        <h3>Documentation</h3>
                                    </td>
                                    <td >
                                        <a class="float-right action_btn no-icn" href="../fs_documentation/add.php?sid=<?php echo $row['id']; ?>">Add New</a>
                                    </td>
                                </tr>
                                <?php
                                $sql = "SELECT * FROM fs_documentation
											WHERE fs_program_id = ? AND period_id = ? ORDER BY date_created;";
                                $result = $conn->query($sql, array($row['id'], $_SESSION['admin_period_id']));

                                if ($conn->num_rows() > 0) {
                                    $num_results = $conn->num_rows();

                                    while ($row_ch = $conn->fetch($result)) {
                                        echo "<tr>";

                                        if ($row_ch['image']) {
                                            echo "<td style=\"width: 90px;\" valign=\"top\"><img src=\"../timThumb.php?src=/assets/fs_documentation/" . $row_ch['image'] . "&w=130&h=72&zc=1\" width=\"50\"></td>";
                                        } else {
                                          echo "<td style=\"width: 90px;\"><img src=\"/assets/documentation_images/no_photo.jpg\" width=\"50\"></td>";
                                        }

                                        echo "
                                                <td style=\"width: auto;\"><h4 myy-2>" . stripslashes($row_ch['title']) . "</h4>" . stripslashes($row_ch['description']) . "</td>
                                                <td align=\"right\" nowrap>
                                                    <a href=\"../fs_documentation/edit.php?id=" . $row_ch['id'] . "&sid=" . $row['id'] . "\" title=\"Edit\" class=\"action_btn edit\">EDIT</a>
                                                    <a href=\"edit.php?id=" . $row['id'] . "&del_doc=" . $row_ch['id'] . "\" onClick=\"return confirm( 'Are you sure you want to delete this item?')\" title=\"Delete\" class=\"action_btn delete\">DELETE</a>
                                                </td>";

                                        echo "</tr>\n";
                                    }
                                } else {
                                    echo '<tr><td colspan="3">No documentation found</td></tr>';
                                }
                                ?>
                            </table>

                            <!--<h3 style="margin-top: 20px;">Related Links</h3>-->

                            <table style="width: 100%;">

                                <?php
                                $sql = "SELECT * FROM fs_related_links WHERE fs_program_id = ? AND period_id = ? ORDER BY sort;";
                                $result = $conn->query($sql, array($row['id'], $_SESSION['admin_period_id']));

                                if ($conn->num_rows() > 0) {
                                    $num_results = $conn->num_rows();

                                  echo '
                                    <tr>
                                        <td colspan="2">
                                            <h3 class="float-left">Related Links</h3>
                                        </td>
                                        <td>
                                            <a class="float-right action_btn no-icn" href="../fs_related_links/add.php?sid=' . $row['id'] . '">Add New</a>
                                        </td>
                                    </tr>
                                    ';

                                    while ($row_ch = $conn->fetch($result)) {
                                        echo "<tr>";
                                        if ($row_ch['image']) {
                                            echo "<td style=\"width: 90px;\" valign=\"top\"><img src=\"../timThumb.php?src=/assets/fs_related_links/" . $row_ch['image'] . "&w=130&h=72&zc=1\" width=\"50\"></td>";
                                        } else {
                                          echo "<td style=\"width: 90px;\"><img src=\"/assets/documentation_images/no_photo.jpg\" width=\"50\"></td>";
                                        }

                                        echo "
                                                <td style=\"width: auto;\" valign=\"top\">
                                                    <h4 myy-2>" . stripslashes($row_ch['title']) . "</h4>
                                                        " . stripslashes($row_ch['description']) . "<br>
                                                    URL: <a target=\"_blank\" href=" . $row_ch['url'] . ">" . $row_ch['url'] . "</a>
                                                </td>
                                                <td align=\"right\" nowrap valign=\"top\">
                                                    <a href=\"../fs_related_links/edit.php?id=" . $row_ch['id'] . "&sid=" . $row['id'] . "\" title=\"Edit\" class=\"action_btn edit\">EDIT</a>
                                                    <a href=\"edit.php?id=" . $row['id'] . "&del_rel=" . $row_ch['id'] . "\" onClick=\"return confirm( 'Are you sure you want to delete this item?')\" title=\"Delete\" class=\"action_btn delete\">DELETE</a>
                                                </td>";

                                        echo "</tr>\n";
                                    }
                                } else {
                                    ?>
                                    <tr>
                                        <td colspan="2">
                                            <h3 class="float-left">Related Links</h3>
                                        </td>
                                        <td>
                                            <a class="float-right action_btn no-icn" href="../fs_related_links/add.php?sid=<?php echo $row['id']; ?>">Add New</a>
                                        </td>
                                    </tr>
                                    <?php
                                    echo '<tr><td colspan="3">No related links found</small></tr>';
                                }
                                ?>
                            </table>

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

    </body>

</html>
<?php $conn->close(); ?>