<?php
@define( 'MAX_LOGIN', 3600 ); // login expires after 10 minutes

$script = explode('/',$_SERVER['SCRIPT_NAME']);
$script = $script[ sizeof( $script ) - 1 ];
$dest = ( $script == "menu.php" || $script == "logout.php" ) ? "index.php" : "../index.php";

// Check for a cookie, if none got to login page
if( !session_id() || !isset( $_SESSION['admin_id'] ) )
	header( "Location: $dest?action=nosession" );
else
	$session_id = session_id();

$sql = "SELECT id FROM Admins WHERE session_id = ? AND last_activity > ?";

if ( $stmt = $conn->query( $sql, array( $session_id, time() - MAX_LOGIN ) ) ) {
	if ( !( $row = $stmt->fetch() ) ) {
		header( "Location: $dest?action=expired" );
	} else {
		$sql = "UPDATE Admins SET session_id = ?, last_activity = ? WHERE id = ?";
		$result = $conn->exec( $sql, array( $session_id, time(), (int)$_SESSION['admin_id'] ) );
	}
}
?>