<?php

// Create user with permission AuthX: Authenticate to services with API key & Secure Pay post
//

use GuzzleHttp\Client;

require_once '../../../../../all/modules/civicrm/vendor/autoload.php';

$apiKey = 'abc';
$encryptionKey = 'xyz';
$siteURL = 'https://dmaster.localhost:32353';
$cipher = 'aes-256-ctr';
// Using the ssl method creates a string which doesn't json well.
$iv = random_bytes(16);
try {
  $params = [
    'order_id' => 5,
    'fields' => [
      'receive_date' => '2021-05-23 15:23:45',
      'amount' => 2345.88,
      'status' => 'Pending',
      'first_name' => 'Sue',
      'last_name' => 'Presley',
      'email' => 'sue@example.com',
      'street_address' => '23 Main St',
      'supplemental_address_1' => 'Mosley Corner',
      'city' => 'Bigville',
      'state' => 'CA',
      'postal_code' => 90210,
      'prefix' => 'Mr.',
      'suffix' => 'I',
      'affiliation' => 'Barnard College',
      'uni' => 'uni y',
      'heard_from' => 'A friend',
      'is_test' => 1,
    ],
  ];

  $string = base64_encode(openssl_encrypt(json_encode($params), $cipher, $encryptionKey, 0, $iv));

  $client = new Client([
    'base_uri' => $siteURL,
    'verify' => false,
    'headers' => ['X-Civi-Auth' => 'Bearer ' . $apiKey],
  ]);

  $response = $client->post('civicrm/ajax/api4/Securepay/submit', [
    'form_params' => [
      'params' => json_encode(['fields' => $string, 'iv' => base64_encode($iv)]),
    ],
  ]);
  $results = json_decode((string) $response->getBody(), TRUE);
}
catch (Exception $e) {
  echo $e->getMessage();
  die;
}

