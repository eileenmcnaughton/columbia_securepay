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
    $customFields = [
      'Contact' => [
        'affiliation' => 'custom_41',//'User_Profile.Affiliation:name',
        'uni' => 'custom_8',//'User_Profile.UNI',
      ],
      'Contribution' => [
        'heard_from' => 'Contribution_Details.Referral_Source:label',
        'affiliation' => 'Contribution_Details.Affiliation:label',
      ],
    ];
    $contactParams = [
      'dupe_check' => TRUE,
      'first_name' => $securePay['first_name'],
      'last_name' => $securePay['last_name'],
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
    // We do this second update cos it will not have updated above if it found an id.
    $contactParams['id'] = $contactID;
    unset($contactParams['dupe_check']);
    foreach (array_merge($customFields['Contribution'], [
      'prefix' => 'prefix_id',
      'suffix' => 'prefix_id',
    ]) as $remoteFieldName => $civicrmField) {
      if (!empty($securePay['data'][$remoteFieldName])) {
        $contactParams['prefix_id:label'] = $securePay['data']['prefix'];
      }
    }
    try {
      $contactID = \civicrm_api3('Contact', 'create', $contactParams)['id'];
    }
    catch (\CiviCRM_API3_Exception $e) {
      // We can continue here as we have a contact - we just failed to save
      // some piece of data.
      \Civi::log()->warning('secure_pay_update_fail', ['message' => $e->getMessage()]);
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
    foreach ($customFields['Contribution'] as $remoteFieldName => $civicrmField) {
      $contributionParams[$civicrmField] = $securePay['data'][$remoteFieldName] ?? NULL;
    }

    $contribution = Contribution::create()->setValues($contributionParams)->execute()->first();

    Securepay::update(FALSE)->setValues([
      'processing_status_id:name' => 'Completed',
      'contribution_id' => $contribution['id'],
    ])->addWhere('id', '=', $this->id)->execute();
  }

}
