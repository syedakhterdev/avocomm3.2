<?php
session_start();
require( '../includes/pdo.php' );
require( '../includes/check_login.php' );
require( '../includes/UserManager.php' );
$User = new UserManager($conn);
$msg = '';

$filename = "users_" . date('Y-m-d') . ".csv";
$delimiter = ",";

$f = fopen('php://memory', 'w');

$users  =   $User->getExportUsers();

$fields = array('First Name', 'Last Name', 'Email', 'Company', 'Status', 'Verified','Terms Agreed');
fputcsv($f, $fields, $delimiter);
if($conn->num_rows() > 0) {
    while ($row = $conn->fetch($users)) {
        $verify='No';
        $active =   'In-active';
        $terms_agreed = 'No';
        if($row['verify_code']==NULL) $verify = 'Yes';
        if($row['agree_to_terms']==1) $terms_agreed = 'Yes';
        if($row['active']==1) $active = 'Active';
        $lineData = array($row['first_name'], $row['last_name'], $row['email'], $row['company'], $active, $verify,$terms_agreed);
        fputcsv($f, $lineData, $delimiter);

    }
}
fseek($f, 0);
// Set headers to download file rather than displayed
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '";');

// Output all remaining data on a file pointer
fpassthru($f);
exit;
 //$conn->close();
 ?>