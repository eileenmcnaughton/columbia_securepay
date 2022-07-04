<?php
namespace Civi\Api4\Action\Securepay;
use Civi\Api4\Generic\AbstractAction;
use Civi\Api4\Generic\Result;
use Civi\Api4\Securepay;

/**
 * @method $this setFields(string)
 * @method $this setIv(integer)
 */
class Submit extends AbstractAction {

  /**
   * Encrypted, json encoded fields.
   *
   * @var string
   */
  protected $fields;

  /**
   * Initialization vector for encryption
   *
   * https://www.php.net/manual/en/function.openssl-encrypt.php
   *
   * @var string|int
   */
  protected $iv;

  /**
   * Get the iv code used in open_ssl encryption.
   *
   * It is base64encoded so that json can handle it - without that json_encode fails.
   *
   * @return string
   */
  protected function getIV(): string {
    return base64_decode($this->iv);
  }

  /**
   * @inheritDoc
   *
   * @param \Civi\Api4\Generic\Result $result
   *
   * @throws \API_Exception
   * @throws \JsonException
   */
  public function _run(Result $result) {
    $log = new \CRM_Utils_SystemLogger();
    $log->alert('secure_pay_incoming', $_REQUEST);
    $cipher = 'aes-256-ctr';
    $data = json_decode(openssl_decrypt(base64_decode($this->fields), $cipher, CIVICRM_SITE_KEY, 0, $this->getIV()), TRUE, 512, JSON_THROW_ON_ERROR);
    $log->alert('secure_pay_decoded', $data);
    Securepay::create(FALSE)->setValues([
      'order_id' => $data['order_id'],
      'first_name' => $data['fields']['first_name'],
      'last_name' => $data['fields']['last_name'],
      'email' => $data['fields']['email'],
      'receive_date'  => $data['fields']['receive_date'],
      'amount' => $data['fields']['amount'],
      'order_status_id:name' => $data['fields']['status'],
      'is_test' => $data['fields']['is_test'],
      'street_address' => $data['fields']['street_address'],
      'supplemental_address_1' => $data['fields']['supplemental_address_1'],
      'city' => $data['fields']['city'],
      'state' => $data['fields']['state'],
      'postal_code' => $data['fields']['postal_code'],
      // Store the entire array in data, including the fields that don't have their own field.
      'data' => $data['fields'],

      ])->execute();
  }
}
