<?php

// Create user with permission AuthX: Authenticate to services with API key & Secure Pay post
//

use GuzzleHttp\Client;

require_once '../../../../../all/modules/civicrm/vendor/autoload.php';

$apiKey = 'abc';
$encryptionKey = 'xyz';
$siteURL = 'https://dmaster.localhost:32353';
$cipher = 'aes-256-ctr';
include_once '/securecreds.php';
// Using the ssl method creates a string which doesn't json well.
$fp = fopen('output.txt', 'w');
$iv = random_bytes(16);
fwrite($fp, 'First the iv is generated');
fwrite($fp, "\n");
fwrite($fp, 'Command - random_bytes(16)');
fwrite($fp, "\n");
fwrite($fp, 'Generated iv is :');
fwrite($fp, "\n");
$ivFile = fopen('iv.bin', 'w');
fwrite($fp, $iv);
fwrite($ivFile, $iv);
fwrite($fp, "\n");
fwrite($fp, "\n");

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

  fwrite($fp, 'The parameters as json_encoded');
  fwrite($fp, "\n");
  fwrite($fp, 'Command - json_encode($params)');
  fwrite($fp, "\n");
  $jsonParams = json_encode($params);
  fwrite($fp, $jsonParams);
  fwrite($fp, "\n");
  fwrite($fp, "\n");

  fwrite($fp, 'The json is encrypted');
  fwrite($fp, "\n");
  fwrite($fp, 'Command - openssl_encrypt($jsonParams, $cipher, $encryptionKey, 0, $iv)');
  fwrite($fp, "\n");

  $encryptedParams = openssl_encrypt($jsonParams, $cipher, $encryptionKey, 0, $iv);
  fwrite($fp, $encryptedParams);
  fwrite($fp, "\n");
  fwrite($fp, "\n");
  fwrite($fp, 'The encrypted string is base64_encoded (so it will make valid json)');
  fwrite($fp, "\n");
  fwrite($fp, 'Command - base64_encode($encryptedParams)');
  fwrite($fp, "\n");
  $string = base64_encode($encryptedParams);
  fwrite($fp, $string);
  fwrite($fp, "\n");
  fwrite($fp, "\n");

  $client = new Client([
    'base_uri' => $siteURL,
    'verify' => false,
    'headers' => ['X-Civi-Auth' => 'Bearer ' . $apiKey],
  ]);

  fwrite($fp, 'The iv is base64_encoded (so it will make valid json)');
  fwrite($fp, "\n");
  fwrite($fp, 'Command - base64_encode($iv)');
  fwrite($fp, "\n");

  $encodedIv = base64_encode($iv);
  fwrite($fp, $encodedIv);
  fwrite($fp, "\n");

  fwrite($fp, "\n");
  fwrite($fp, 'Finally the postArray is compiled, as json');
  fwrite($fp, "\n");

  $postValues = ['form_params' => [
    'params' => json_encode(['fields' => $string, 'iv' => $encodedIv]),
  ]];
  fwrite($fp, print_r($postValues, TRUE));
  fwrite($fp, "\n");
  $response = $client->post('civicrm/ajax/api4/Securepay/submit', $postValues);
  $results = json_decode((string) $response->getBody(), TRUE);
}
catch (Exception $e) {
  echo $e->getMessage();
  die;
}

