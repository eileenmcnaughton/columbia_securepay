<?php

namespace Civi\Api4\Action\Securepay;

use Civi\Api4\Generic\AbstractAction;
use Civi\Api4\Generic\Result;
use Civi\Api4\Securepay;
use Civi\Api4\Contribution;

/**
 * @method $this setID(integer)
 */
class Process extends AbstractAction {

  /**
   * Secure pay entry id
   *
   * @var int
   */
  protected $id;

  /**
   * @inheritDoc
   *
   * @param \Civi\Api4\Generic\Result $result
   *
   * @throws \API_Exception
   * @throws \CiviCRM_API3_Exception
   * @throws \Civi\API\Exception\UnauthorizedException
   */
  public function _run(Result $result) {
    $securePay = Securepay::get()->addSelect('*', 'order_status_id:name')->addWhere('id', '=', $this->id)->execute()->first();

    $contactParams = [
      'dupe_check' => TRUE,
      'first_name' => $securePay['first_name'],
      'last_name' => $securePay['last_name'],
      'prefix_id:label' => $securePay['data']['prefix'],
      'suffix_id:label' => $securePay['data']['suffix'],
      'email' => $securePay['email'],
      'street_address' => $securePay['street_address'],
      'supplemental_address_1' => $securePay['supplemental_address_1'],
      'postal_code' => $securePay['postal_code'],
      'city' => $securePay['city'],
      'country_id' => 'United States',
      'contact_type' => 'Individual',
      'state_province_id' => $securePay['state'] ?? NULL,
    ];
    try {
      $contactID = \civicrm_api3('Contact', 'create', $contactParams)['id'];
    }
    catch (\CiviCRM_API3_Exception $e) {
      if (!empty($e->getExtraParams()['ids'])) {
        // Handle duplicate.
        $contactID = $e->getExtraParams()['ids'][0];
      }
      else {
        throw $e;
      }
    }

    $contributionParams = [
      'total_amount' => $securePay['amount'],
      'status_id:name' => $securePay['order_status_id'],
      'invoice_id' => $securePay['order_id'],
      'is_test' => $securePay['is_test'],
      'receive_date' => $securePay['receive_date'],
      'contact_id' => $contactID,
      'financial_type_id:name' => 'Donation',
    ];

    $contribution = Contribution::create()->setValues($contributionParams)->execute()->first();

    Securepay::update(FALSE)->setValues([
      'processing_status_id:name' => 'Completed',
      'contribution_id' => $contribution['id'],
    ])->addWhere('id', '=', $this->id)->execute();
  }

}
