<?php
require( 'config.php' );
require( 'includes/pdo.php' );
$sql = 'SELECT id, title FROM periods WHERE id = ? LIMIT 1';
$periods = $conn->query( $sql, array($_SESSION['admin_period_id']) );
if ( $conn->num_rows() > 0 ) {
    $period = $conn->fetch( $periods );
    $_SESSION['user_period_id'] = $period['id'];
    $_SESSION['user_period_title'] = stripslashes( $period['title'] );
    $_SESSION['user_id'] = $_SESSION['admin_id'];
    $_SESSION['user_name'] = $_SESSION['admin_name'];
    $_SESSION['user_type'] = 'admin';
    header( 'Location: '.SITE_URL.'/main.php' );
}
else{
    header( 'Location: '.ADMIN_URL.'/menu.php' );
}

?>