<?php
session_start();
require( 'manager/includes/pdo.php' );
$id = isset( $_GET['id'] ) ? (int)$_GET['id'] : 0;
if ( !$id ) {
  echo 'Please provide a valid event ID';
  exit;
}

$sql = 'INSERT INTO activity_log SET date_created = NOW(), user_id = ?, activity_type_id = 5, ip_address = ?';
$conn->exec( $sql, array( $id, $_SERVER['REMOTE_ADDR'] ) );

$sql = 'SELECT * FROM events WHERE id = ?';
$events = $conn->query( $sql, array( $id ) );
if ( $conn->num_rows() > 0 ) {
  if ( $event = $conn->fetch( $events ) ) {
    //set correct content-type-header
    header('Content-type: text/calendar; charset=utf-8');
    header('Content-Disposition: inline; filename=calendar.ics');

    echo 'BEGIN:VCALENDAR
METHOD:PUBLISH
VERSION:2.0
PRODID:-//AvocadosFromMexico/handcal//NONSGML v1.0//EN
BEGIN:VEVENT
UID:' . md5( uniqid( mt_rand(), true ) ) . '@avocadosfrommexico.com
DTSTART:' . gmdate( 'Ymd', strtotime( $event['event_date'] ) ) . 'T000000Z
DTEND:' . gmdate( 'Ymd', strtotime( $event['event_date'] ) ) . 'T000000Z
SUMMARY:' . stripslashes( $event['title'] ) . '
DESCRIPTION:' . strip_tags( stripslashes( $event['description'] ) ) . '
END:VEVENT
END:VCALENDAR';

  }
}
?>