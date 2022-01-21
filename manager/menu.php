<?php
session_start();
require( 'includes/pdo.php' );
require( 'includes/check_login.php' );
?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Welcome</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
  <link href="/manager/font-awesome/css/font-awesome.min.css" rel="stylesheet">
  <link href="/manager/css/imagine.css" rel="stylesheet">
	<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300' rel='stylesheet' type='text/css'>
  <link rel="icon" href="/assets/cropped-favicon-150x150.png" sizes="32x32">
</head>

<body>

  <?php include( 'includes/header.php' ); ?>

    <div class="container-fluid" id="main">
      <div class="row row-offcanvas row-offcanvas-left">

        <?php include( 'includes/nav.php' ); ?>

          <div class="col main pt-5 mt-3">

            <div id="menu_heading" class="row pt-3 pl-3 pr-3">
              <h2>Dashboard</h2>
            </div>
            <div class="row mb-3">

            </div>
            <!--/row-->


            <div class="row my-4">

              <div class="col-lg-12 col-md-4">
                  <div class="row">
                      <div class="col-lg-12" style="text-align: right;">
                          <a href="export-log.php" name="download" id="download" class="btn btn-primary download_button">Export Log</button></a>
                      </div>
                  </div>
                  <div class="card card-inverse bg-inverse mt-3">
                      <div class="card-body">
                          <?php
                          require( 'includes/ActivityLogManager.php' );
                          $Activity = new ActivityLogManager($conn);
                          ?>
                          <h5 class="card-title">Latest Activities</h5>
                          <div class="table-responsive">

                              <table class="table table-striped table-sm">
                                  <thead class="thead-inverse">
                                      <tr>
                                          <th style="width: 12%;">Date</th>
                                          <th>Activity</th>
                                          <th style="width: 15%;">User</th>
                                          <th style="width: 30%;">Note</th>
                                      </tr>
                                  </thead>
                                  <tbody>
                                      <?php
                                      //$sql = "SELECT COUNT(*) FROM events WHERE id > 0;";
                                      $rowsPerPage = 15;
                                      $filters = '';
                                      //if ($activity_type_id)
                                      //    $filters .= ' AND a.activity_type_id = ' . $activity_type_id;
                                      //if ($user_id)
                                      //    $filters .= ' AND a.user_id = ' . $user_id;
                                      $total_count = $Activity->getActivityCount($filters);
                                      $conn->getPaging($total_count, 1, $rowsPerPage, "");

                                      $result = $Activity->getActivity( $conn->offset, $rowsPerPage );

                                      if ($conn->num_rows() > 0) {
                                          while ($row = $conn->fetch($result)) {
                                              echo '
                                                      <tr>
                                                          <td>' . date('m/d/Y', strtotime($row['date_created'])) . '</a></td>
                                                          <td>' . $conn->parseOutputString($row['activity_type']) . '</td>
                                                          <td>' . $conn->parseOutputString($row['full_name']) . '</td>
                                                          <td>' . $conn->parseOutputString($row['reference']) . '</td>
                                                      </tr>';
                                          }
                                      } else {
                                          echo "<td colspan=\"4\">No activity found.</td>";
                                      }
                                      ?>
                                  </tbody>
                              </table>
                          </div>

                          <a href="/manager/activity_log/" class="btn btn-outline-secondary">View All</a>
                      </div>
                  </div>
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
</body>
</html>
  <?php $conn->close(); ?>
