<?php

namespace Civi\Api4\Action\Securepay;

use Civi\Api4\CustomField;
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
    $securePay = Securepay::get($this->getCheckPermissions())->addSelect('*', 'order_status_id:name')->addWhere('id', '=', $this->id)->execute()->first();
    $existingContribution = Contribution::get($this->getCheckPermissions())
      ->addWhere('invoice_id', '=', $securePay['order_id'])
      ->addWhere('is_test', 'IN', [1, 0])
      ->addSelect('id', 'invoice_id', 'status_id:name', 'contact_id')
      ->execute()->first();

    $contactParams = [
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
      'source' => 'SecurePay',
    ];
    if (isset($existingContribution['contact_id'])) {
      $contactParams['id'] = $existingContribution['contact_id'];
    }
    else {
      $contactParams['dupe_check'] = TRUE;
    }

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
    unset($contactParams['dupe_check'], $contactParams['source']);

    $mappedFields = [
      'prefix' => 'prefix_id',
      'suffix' => 'prefix_id',
    ];

    $customFields = (array) CustomField::get($this->getCheckPermissions())
      ->addSelect('custom_group_id.extends', 'id', 'Custom_Fields.Secure_pay_field', 'name','custom_group_id.name', 'option_group_id')
      ->addWhere('Custom_Fields.Secure_pay_field', 'IS NOT EMPTY')
      ->setLimit(25)
      ->execute();
    foreach ($customFields as $customField) {
      // We assume anything not contribution is contact.
      if ($customField['custom_group_id.extends'] !== 'Contribution') {
        $mappedFields[$customField['Custom_Fields.Secure_pay_field']] = 'custom_' . $customField['id'];
      }
    }
    foreach ($mappedFields as $remoteFieldName => $civicrmField) {
      // For contact we don't want to over-write existing data with blank.
      if (!empty($securePay['data'][$remoteFieldName])) {
        $contactParams[$civicrmField] = $securePay['data'][$remoteFieldName];
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
      'contribution_status_id:name' => $this->getMappedStatus($securePay['data']['status']),
      'invoice_id' => $securePay['order_id'],
      'is_test' => $securePay['is_test'],
      'receive_date' => $securePay['receive_date'],
      'contact_id' => $contactID,
      'financial_type_id:name' => 'Donation',
      'source' => 'SecurePay',
      'payment_instrument_id:name' => 'Credit Card',
    ];
    foreach ($customFields as $customField) {
      // We assume anything not contribution is contact.
      if ($customField['custom_group_id.extends'] === 'Contribution') {
        $field = $customField['custom_group_id.name'] . '.' . $customField['name'];
        if ($customField['option_group_id']) {
          $field .= ':label';
        }
        $contributionParams[$field] = $securePay['data'][$customField['Custom_Fields.Secure_pay_field']] ?? NULL;
      }
    }
    if (!empty($existingContribution['id'])) {
      $contributionParams['id'] = $existingContribution['id'];
    }

    $contribution = Contribution::save($this->getCheckPermissions())->setRecords([$contributionParams])->execute()->first();

    Securepay::update($this->getCheckPermissions())->setValues([
      'processing_status_id:name' => 'Completed',
      'contribution_id' => $contribution['id'],
    ])->addWhere('id', '=', $this->id)->execute();
  }

  /**
   * Get the mapped contribution status.
   *
   * @param $orderStatus
   *
   * @return string
   */
  private function getMappedStatus($orderStatus): string {
    return [
      'processing' => 'Pending',
      'complete' => 'Completed',
       // There is a status - requires Follow-up on the site...?
      'discarded' => 'Cancelled',
      'incomplete(CANCEL)' => 'Cancelled',
    ][$orderStatus] ?? 'Pending';
  }

}
