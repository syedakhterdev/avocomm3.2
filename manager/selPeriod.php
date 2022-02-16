<?php
require( 'config.php' );
$period_id = isset( $_GET['id'] ) ? (int)$_GET['id'] : 0;
$spid = isset( $_GET['spid'] ) ? (int)$_GET['spid'] : 0;
$trid = isset( $_GET['trid'] ) ? (int)$_GET['trid'] : 0;
$fsid = isset( $_GET['fsid'] ) ? (int)$_GET['fsid'] : 0;

if ( $period_id && isset( $_SESSION['admin_id'] ) && (int)$_SESSION['admin_id'] ) {
  $_SESSION['admin_period_id'] = $period_id;

  if ( $spid ) header( 'Location: '.ADMIN_URL.'/shopper_program_entries/edit.php?id=' . $spid . '&pdc=1' );
  elseif ( $trid ) header( 'Location: '.ADMIN_URL.'/trade_vendor_entries/edit.php?id=' . $trid . '&pdc=1' );
  elseif ( $fsid ) header( 'Location: '.ADMIN_URL.'/fs_program_entries/edit.php?id=' . $fsid . '&pdc=1' );
  elseif ( strpos( $_SERVER['HTTP_REFERER'], 'index.php' ) > 0 ) header( 'Location: ' . add_var_to_url( 'pdc', 1, $_SERVER['HTTP_REFERER'] ) );
  else header( 'Location: '.ADMIN_URL.'/menu.php' );

} else {
  header( 'Location: '.ADMIN_URL.'/menu.php' );
}

function add_var_to_url( $variable_name, $variable_value, $url_string ) {
  if ( strpos( $url_string, "?" ) !== false ) {
    $start_pos = strpos( $url_string, "?" );
    $url_vars_strings = substr( $url_string, $start_pos + 1 );
    $names_and_values = explode( "&", $url_vars_strings );
    $url_string = substr( $url_string, 0, $start_pos );
    foreach ( $names_and_values as $value ) {
      list( $var_name, $var_value ) = explode( "=", $value );
      if ( $var_name != $variable_name ) {
        if ( strpos( $url_string, "?" ) === false ) {
          $url_string .= "?";
        } else {
          $url_string .= "&";
        }
        $url_string .= $var_name . "=" . $var_value;
      }
    }
  }
  // add variable name and variable value
  if ( strpos( $url_string, "?" ) === false ) {
    $url_string .= "?" . $variable_name . "=" . $variable_value;
  } else {
    $url_string .= "&" . $variable_name . "=" . $variable_value;
  }
  return $url_string;
}
?>