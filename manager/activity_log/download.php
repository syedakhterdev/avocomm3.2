<?php
session_start();

require( '../includes/pdo.php' );
require( '../includes/check_login.php' );
require( '../includes/ActivityLogManager.php' );
$Activity = new ActivityLogManager($conn);

$msg = '';
$error = '';
$filters = '';
$activity_type_id = ( isset($_GET['aid']) ) ? (int) $_GET['aid'] : '';
$user_id = ( isset($_GET['uid']) ) ? (int) $_GET['uid'] : '';

if ( $activity_type_id ) $filters .= ' AND a.activity_type_id = ' . $activity_type_id;
if ( $user_id ) $filters .= ' AND a.user_id = ' . $user_id;
$result = $Activity->getActivity( 0, 1000000000000, $filters );

if ( $conn->num_rows() > 0 ) {
  header("Content-Type: text/csv");
  header("Content-Disposition: attachment; filename=activity_log.csv");

  while ( $row = $conn->fetch( $result ) ) {
    echo '"' . date( 'm/d/Y', strtotime( $row['date_created'] ) ) . '","' . $conn->parseOutputString( $row['activity_type'] ) . '","' . $conn->parseOutputString( $row['full_name'] ) . '","' . $conn->parseOutputString( $row['reference'] ) . '","' . $conn->parseOutputString( $row['ip_address'] ) . "\"\n";
  }
} else {
  echo 'No activity was found.';
}
?>