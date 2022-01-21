<?php
require( '/var/www/avocomm/manager/includes/pdo.php' );
$sql = "UPDATE users SET last_verify = NULL WHERE last_verify IS NOT NULL AND DATEDIFF( NOW(), last_verify ) >= 60";
$conn->exec( $sql, array() );

// disable users who have been incactive longer than 90 days
$sql = "UPDATE users SET last_login = NULL, active = 0 WHERE last_login IS NOT NULL AND  DATEDIFF( CURDATE(), last_login ) >= 90";
$conn->exec( $sql, array() );
?>