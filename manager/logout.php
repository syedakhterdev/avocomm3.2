<?php
session_start();

require( 'includes/pdo.php' );
require( 'includes/check_login.php' );

// erase the user's last activity time to force login and new session on next visit
$sql = "UPDATE Admins SET session_id = '', last_activity = 0 WHERE id = ?";
$result = $conn->exec( $sql, array( $_SESSION['admin_id'] ) );

// destroy the entire session
session_unset();
session_destroy();

// close the database connection
$conn->close();

// redirect
header('Location: index.php?logout=1' );
?>
