<?php
@define( 'MAX_LOGIN', 3600000 ); // login expires after 10 minutes

$script = explode('/',$_SERVER['SCRIPT_NAME']);
$script = $script[ sizeof( $script ) - 1 ];
$dest = ( $script == "menu.php" || $script == "logout.php" ) ? "index.php" : "../index.php";

// Check for a cookie, if none got to login page
if( !session_id() || !isset( $_SESSION['user_id'] ) )
	header( "Location: $dest?action=nosession" );
else
	$session_id = session_id();

/*$sql = "SELECT id FROM users WHERE session_id = ? AND last_activity > ?";

if ( $stmt = $conn->query( $sql, array( $session_id, time() - MAX_LOGIN ) ) ) {
	if ( !( $row = $stmt->fetch() ) ) {
		header( "Location: /index.php?action=expired" );
	} else {
		$sql = "UPDATE users SET session_id = ?, last_activity = ? WHERE id = ?";
		$result = $conn->exec( $sql, array( $session_id, time(), (int)$_SESSION['user_id'] ) );
	}
}*/
?>