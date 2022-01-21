<?php
session_start();

require( '../includes/pdo.php' );
require( '../includes/check_login.php' );
require( '../includes/AdminLogManager.php' );
$Activity = new AdminLogManager($conn);

$msg = '';
$error = '';
$filters = '';
$page = ( empty($_GET['page']) ) ? 1 : (int) $_GET['page'];
$activity_type_id = ( isset($_GET['aid']) ) ? (int) $_GET['aid'] : '';
$user_id = ( isset($_GET['uid']) ) ? (int) $_GET['uid'] : '';
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
        <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300' rel='stylesheet' type='text/css'>
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
                        <li><strong>Admin Activity Log</strong></li>
                    </ol>

                    <div class="row my-4 mgr_body add_entry activity_log index">
                        <div class="col-lg-10 col-md-8">

                            <div class="row">
                                <div class="col-lg-12">
                                    <form action="index.php" method="GET">
                                        <select name="aid" id="aid">
                                            <option value="">Activity Type...</option>
                                            <?php
                                            $sql = "SELECT id, activity_type FROM admin_activity_type ORDER BY activity_type";
                                            $acts = $conn->query($sql, array());
                                            if ($conn->num_rows() > 0) {
                                                while ($act = $conn->fetch($acts)) {
                                                    if ((int) $activity_type_id == (int) $act['id'])
                                                        echo '<option SELECTED value="' . $act['id'] . '">' . $act['activity_type'] . '</option>' . "\n";
                                                    else
                                                        echo '<option value="' . $act['id'] . '">' . $act['activity_type'] . '</option>' . "\n";
                                                }
                                            }
                                            ?>
                                        </select>

                                        <select name="uid" id="uid">
                                            <option value="">User...</option>
                                            <?php
                                            $sql = "SELECT id, CONCAT( last_name, ', ', first_name ) AS full_name FROM Admins ORDER BY last_name";
                                            $uids = $conn->query($sql, array());
                                            if ($conn->num_rows() > 0) {
                                                while ($uid = $conn->fetch($uids)) {
                                                    if ((int) $user_id == (int) $uid['id'])
                                                        echo '<option SELECTED value="' . $uid['id'] . '">' . $uid['full_name'] . '</option>' . "\n";
                                                    else
                                                        echo '<option value="' . $uid['id'] . '">' . $uid['full_name'] . '</option>' . "\n";
                                                }
                                            }
                                            ?>
                                        </select>

                                        <input type="submit" name="search" id="search" value="Go" class="btn btn-primary">
                                        <a href="download.php?aid=<?php echo $activity_type_id; ?>&uid=<?php echo $user_id; ?>" name="download" id="download" class="btn btn-primary download_button">Download</button></a>
                                    </form>
                                </div>
                            </div>

                            <?php
                            //$sql = "SELECT COUNT(*) FROM events WHERE id > 0;";
                            $rowsPerPage = 10;
                            if ($activity_type_id)
                                $filters .= ' AND a.admin_activity_type_id = ' . $activity_type_id;
                            if ($user_id)
                                $filters .= ' AND a.user_id = ' . $user_id;
                            $total_count = $Activity->getActivityCount($filters);
                            $conn->getPaging($total_count, $page, $rowsPerPage, "&uid=$user_id&aid=$activity_type_id");
                            ?>

                            <div class="table-responsive">

                                <table class="table table-striped table-sm">
                                    <thead class="thead-inverse">
                                        <tr>
                                            <th style="width: 12%;">Date</th>
                                            <th>Activity</th>
                                            <th style="width: 15%;">User</th>
                                            <th style="width: 30%;">Note</th>
                                            <th style="">IP Address</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $result = $Activity->getActivity($conn->offset, $rowsPerPage, $filters);


                                        if ($conn->num_rows() > 0) {
                                            while ($row = $conn->fetch($result)) {
                                                echo '
                                                        <tr>
                                                          <td>' . date('m/d/Y', strtotime($row['date_created'])) . '</td>
                                                          <td>' . $conn->parseOutputString($row['activity_type']) . '</td>
                                                          <td>' . $conn->parseOutputString($row['full_name']) . '</td>
                                                          <td>' . $conn->parseOutputString($row['reference']) . '</td>
                                                          <td>' . $conn->parseOutputString($row['ip_address']) . '</td>
                                                        </tr>';
                                            }
                                        } else {
                                            echo "<td colspan=\"5\">No activity found.</td>";
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