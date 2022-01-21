<?php
session_start();

require( '../includes/pdo.php' );
require( '../includes/check_login.php' );
require( '../includes/NewManager.php' );
$New = new NewManager($conn);

$msg = '';
$error = '';
$criteria = '';
$page = ( empty($_GET['page']) ) ? 1 : (int) $_GET['page'];

$del_id = ( isset($_POST['del']) ) ? (int) $_POST['del'] : '';
$update = ( isset($_GET['update']) ) ? (int) $_GET['update'] : '';
$add = ( isset($_GET['add']) ) ? (int) $_GET['add'] : '';
$active = ( isset($_GET['active']) ) ? (int) $_GET['active'] : '';
$sort = ( isset($_GET['sort']) ) ? $_GET['sort'] : '';
$mo = ( isset($_GET['mo']) ) ? $_GET['mo'] : '';
$yr = ( isset($_GET['yr']) ) ? $_GET['yr'] : '';

if ($del_id) {
    // get the delete token that was set previously
    $token = $_SESSION['del_token'];
    unset($_SESSION['del_token']);

    if ($token != '' && $_POST['token'] == $token) {
        if (!$New->delete($_SESSION['admin_period_id'], $del_id)) {
            $msg = "Sorry, an error has occurred, please contact your administrator!<br>Error:" . $conn->error();
        } else {
            $msg = "The specified record was deleted successfully!";
        }
    }
} else if ($update) {
    $msg = "The specified record was updated successfully!";
} else if ($add) {
    $msg = "Your item was added successfully!";
} else if ($active) {
    $cur = ( isset($_GET['cur']) ) ? (int) $_GET['cur'] : '';
    $sql = 'UPDATE news SET active = ? WHERE id = ?';
    $conn->exec( $sql, array( $cur ? 0 : 1, $active ) );
    $msg = "Active status was changed successfully!";
}

// create a token for secure deletion (from this page only and not remote)
$_SESSION['del_token'] = md5(uniqid());
session_write_close();
?>
<!DOCTYPE html>
<html>

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo SECTION_TITLE; ?></title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
        <link href="/manager/font-awesome/css/font-awesome.min.css" rel="stylesheet">
        <link rel="icon" href="/assets/cropped-favicon-150x150.png" sizes="32x32">

        <link href="/manager/css/imagine.css" rel="stylesheet">
        <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300' rel='stylesheet' type='text/css'>
        <style>

        </style>
    </head>

    <body>

        <?php include( '../includes/header.php' ); ?>

        <div class="container-fluid" id="main">
            <div class="row row-offcanvas row-offcanvas-left">

                <?php include( '../includes/nav.php' ); ?>

                <div class="col main pt-5 mt-3">
                    <div class="row mgr_heading">
                        <div class="col-lg-10">
                            <h3 class="float-left"><?php echo SECTION_TITLE; ?></h3>
                        </div>
                    </div>

                    <ol class="breadcrumb bc-3">
                        <li><a href="/manager/menu.php">Dashboard</a></li>
                        <li>&nbsp;/&nbsp;</li>
                        <li><strong>News</strong></li>
                    </ol>

                    <div class="row my-4 mgr_body program_edit program_entries index news">
                        <div class="col-lg-10 col-md-8">

                            <?php if ($msg) echo '<div class="alert alert-success" role="alert">' . $msg . '</div>'; ?>
                            <?php if ($error) echo '<div class="alert alert-error" role="alert">' . $error . '</div>'; ?>

                            <?php
                            $rowsPerPage = 25;
                            $total_count = $New->getNewsCount($_SESSION['admin_period_id'], $mo, $yr);
                            $conn->getPaging($total_count, $page, $rowsPerPage, '&mo=' . $mo . '&yr=' . $yr);
                            ?>

                            <div class="container px-0 mx-0">
                              <div class="row">
                                <div class="col-lg-12">
                                  <table>
                                    <form action="index.php" method="GET">
                                    <tr>
                                      <td>
                                        <select name="mo" class="form-control form-control-sm">
                                          <option value="">Month</option>
                                          <option value="01" <?php if ( $mo == '01' ) echo 'SELECTED'; ?>>January</option>
                                          <option value="02" <?php if ( $mo == '02' ) echo 'SELECTED'; ?>>February</option>
                                          <option value="03" <?php if ( $mo == '03' ) echo 'SELECTED'; ?>>March</option>
                                          <option value="04" <?php if ( $mo == '04' ) echo 'SELECTED'; ?>>April</option>
                                          <option value="05" <?php if ( $mo == '05' ) echo 'SELECTED'; ?>>May</option>
                                          <option value="06" <?php if ( $mo == '06' ) echo 'SELECTED'; ?>>June</option>
                                          <option value="07" <?php if ( $mo == '07' ) echo 'SELECTED'; ?>>July</option>
                                          <option value="08" <?php if ( $mo == '08' ) echo 'SELECTED'; ?>>August</option>
                                          <option value="09" <?php if ( $mo == '09' ) echo 'SELECTED'; ?>>September</option>
                                          <option value="10" <?php if ( $mo == '10' ) echo 'SELECTED'; ?>>October</option>
                                          <option value="11" <?php if ( $mo == '11' ) echo 'SELECTED'; ?>>November</option>
                                          <option value="12" <?php if ( $mo == '12' ) echo 'SELECTED'; ?>>December</option>
                                        </select>
                                      </td>
                                      <td>
                                        <select name="yr" class="form-control form-control-sm">
                                          <option value="">Year</option>
                                          <?php
                                          for ( $i = 2019; $i <= date( 'Y' ); $i ++ ) {
                                            if ( $i == $yr )
                                              echo '<option SELECTED value="' . $i . '">' . $i . '</option>' . "\n";
                                            else
                                              echo '<option value="' . $i . '">' . $i . '</option>' . "\n";
                                          }
                                          ?>
                                        </select>
                                      </td>
                                      <td>
                                        <button class="btn btn-sm" style="margin: 0px 0px 0px 4px;" type="submit">Go!</button>
                                      </td>
                                    </tr>
                                    </form>
                                  </table>
                                </div>
                              </div>
                            </div>



                            <div class="table-responsive">

                                <div class="add_button">
                                    <a href="add.php" data-fancybox data-type="iframe"><button type="button" class="btn btn-primary btn-sm float-right">ADD NEW ENTRY</button></a>
                                </div>

                                <table class="table table-striped table-sm">
                                    <thead class="thead-inverse">
                                        <tr>
                                            <!--<th style="width: 50px;">&nbsp;</th>-->
                                            <th class="title_sort"><a href="index.php?page=<?php echo $page; ?>&sort=title">Title</a></th>
                                            <th class="date_sort"><a href="index.php?page=<?php echo $page; ?>&sort=date_created">Date</a></th>
                                            <th class="text-center" style="width: 10%;">Active</th>
                                            <th style="width: 10%;"></th>
                                            <th style="width: 10%;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $result = $New->getNews($_SESSION['admin_period_id'], $sort, $conn->offset, $rowsPerPage, $mo, $yr);

                                        if ($conn->num_rows() > 0) {
                                            while ($row = $conn->fetch($result)) {
                                                echo '
                                                        <tr>
                                                            <td>' . ( ( $row['image'] ) ? "<img src=\"/manager/timThumb.php?src=/assets/news/" . $row['image'] . "&w=28&h=28&zc=1\" width=\"28\" height=\"28\" class=\"listing_image\">" : '' ) . '<a href="edit.php?id=' . $row['id'] . '">' . $conn->parseOutputString($row['title']) . '</a></td>
                                                            <td>' . date( 'm/d/Y', strtotime( $row['date_created'] ) ) . '</td>
                                                            <td align="center" class="listing_icons">
                                                                <a href="index.php?active=' . $row['id'] . '&cur=' . (int)$row['active'] . '" class="action_btn ' . (($row['active'] == '1' ) ? 'active' : 'deactive') . '" onClick="return confirm(' . "'Are you sure you want to change the active status of this item?'" . ');">' . (($row['active'] == '1' ) ? 'ON' : 'OFF') . '</a>
                                                            </td>
                                                            <td align="center" class="listing_icons" width="16">
                                                                <a href="edit.php?id=' . $row['id'] . '" title="Edit" class="edit action_btn">Edit</a>
                                                            </td>
                                                            <td align="center" class="listing_icons" width="16">
                                                                <form action="index.php?page=' . $page . '&criteria=' . $criteria . '" method="POST" onSubmit="return confirm(' . "'Are you sure you want to delete this item?'" . ');">
                                                                    <input type="hidden" name="del" value="' . $row['id'] . '">
                                                                    <input type="hidden" name="token" value="' . $_SESSION['del_token'] . '">
                                                                    <input type="submit" class="action_btn delete" value="DELETE">
                                                                </form>
                                                            </td>
                                                        </tr>';
                                            }
                                        } else {
                                            echo "<td colspan=\"5\">No news found.</td>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>

                            <?php echo $conn->paging(); ?>

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
        <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
        <script src="/manager/js/imagine.js"></script>
        <script>
            $(document).ready(function () {
                window.setTimeout(function () {
                    $(".alert").fadeTo(500, 0).slideUp(500, function () {
                        $(this).remove();
                    });
                }, 2000);
            });
        </script>
    </body>
</html>
<?php $conn->close(); ?>