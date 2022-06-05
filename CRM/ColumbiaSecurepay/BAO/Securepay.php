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
      1 => ['name' => 'Pending', 'title' => ts('Pending')],
      2 => ['name' => 'Completed', 'title' => ts('Completed')],
    ];
  }

  /**
   * @return array[]
   */
  public static function getOrderStatuses(): array {
    return [
      1 => ['id' => 1, 'name' => 'Pending', 'title' => ts('Pending')],
      2 => ['id' => 2, 'name' => 'Failed', 'title' => ts('Failed')],
      3 => ['id' => 3, 'name' => 'Completed', 'title' => ts('Completed')],
    ];
  }

}
