<?php
session_start();

require( 'includes/pdo.php' );
require( 'includes/check_login.php' );
require( 'includes/ActivityLogManager.php' );
$Activity = new ActivityLogManager($conn);

$filename = "activitylog_" . date('Y-m-d') . ".csv";
$delimiter = ",";

$f = fopen('php://memory', 'w');

$msg = '';
$error = '';
$filters = '';
$activity_type_id = ( isset($_GET['aid']) ) ? (int) $_GET['aid'] : '';
$user_id = ( isset($_GET['uid']) ) ? (int) $_GET['uid'] : '';

if ( $activity_type_id ) $filters .= ' AND a.activity_type_id = ' . $activity_type_id;
if ( $user_id ) $filters .= ' AND a.user_id = ' . $user_id;
$result = $Activity->getActivitylog( 0, 1000000000000, $filters );

if ( $conn->num_rows() > 0 ) {
    $fields = array('Date', 'Activity Type', 'User Name', 'User Email', 'Reference', 'IP Address');
    fputcsv($f, $fields, $delimiter);
  while ( $row = $conn->fetch( $result ) ) {
      $lineData = array(date( 'm/d/Y', strtotime( $row['date_created'] ) ), $conn->parseOutputString( $row['activity_type'] ), $conn->parseOutputString( $row['full_name'] ), $conn->parseOutputString( $row['email'] ), $conn->parseOutputString( $row['reference'] ), $conn->parseOutputString( $row['ip_address'] ));
      fputcsv($f, $lineData, $delimiter);
  }
} else {
  echo 'No activity was found.';
}
fseek($f, 0);
// Set headers to download file rather than displayed
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '";');

// Output all remaining data on a file pointer
fpassthru($f);
exit;
?>