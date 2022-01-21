<?php
require("vendor/autoload.php");
use Lever\Api\Client;

$client = new Client([
    'authToken' => 'FR5LH530p8IPUZd6s-Vl',
]);


$postings = $client->get('/postings', [
    'limit' => 100,
]);
echo '<pre>';
print_r($postings);

exit;

?>