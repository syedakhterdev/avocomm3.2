<?php
session_start();
require( 'manager/includes/pdo.php' );

$period_id = isset( $_GET['id'] ) ? (int)$_GET['id'] : 0;
if ( $period_id && isset( $_SESSION['user_id'] ) && (int)$_SESSION['user_id'] ) {
  $sql = 'SELECT title FROM periods WHERE id = ?';
  $pds = $conn->query( $sql, array( $period_id ) );
  if ( $conn->num_rows() > 0 ) {
    $pd = $conn->fetch( $pds );
    $_SESSION['user_period_title'] = stripslashes( $pd['title'] );
  }
  $_SESSION['user_period_id'] = $period_id;

  // log the activity
  $sql = "INSERT INTO activity_log SET date_created = NOW(), user_id = ?, activity_type_id = 8, ip_address = ?";
  $conn->exec( $sql, array( $_SESSION['user_id'], $_SERVER['REMOTE_ADDR'] ) );

  header( 'Location: '.SITE_URL.'/main.php' );
} else {
  header( 'Location: '.SITE_URL.'/index.php' );
}
?>