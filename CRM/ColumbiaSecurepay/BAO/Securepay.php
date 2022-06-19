<?php
use CRM_ColumbiaSecurepay_ExtensionUtil as E;

class CRM_ColumbiaSecurepay_BAO_Securepay extends CRM_ColumbiaSecurepay_DAO_Securepay {

  /**
   * Create a new Securepay based on array-data
   *
   * @param array $params key-value pairs
   * @return CRM_ColumbiaSecurepay_DAO_Securepay|NULL
   */
  public static function create($params) {
    return self::writeRecord($params);
  }

  /**
   * @return array[]
   */
  public static function getProcessingStatuses(): array {
    return [
      ['id' => 1, 'name' => 'Pending', 'title' => ts('Pending')],
      ['id' => 2, 'name' => 'Completed', 'title' => ts('Completed')],
    ];
  }

  /**
   * @return array[]
   */
  public static function getOrderStatuses(): array {
    return [
      ['id' => 1, 'name' => 'Pending', 'title' => ts('Pending')],
      ['id' => 2, 'name' => 'Failed', 'title' => ts('Failed')],
      ['id' => 3, 'name' => 'Completed', 'title' => ts('Completed')],
    ];
  }

}
