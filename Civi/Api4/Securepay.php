<?php
namespace Civi\Api4;

use Civi\Api4\Action\Securepay\Process;
use Civi\Api4\Action\Securepay\Submit;

/**
 * Securepay entity.
 *
 * Provided by the FIXME extension.
 *
 * @searchable secondary
 *
 * @package Civi\Api4
 */
class Securepay extends Generic\DAOEntity {

  /**
   * SecurePay submit function.
   *
   * @param bool $checkPermissions
   *
   * @return \Civi\Api4\Action\Securepay\Submit
   */
  public static function submit(bool $checkPermissions = TRUE): Submit {
    return (new Submit(__CLASS__, __FUNCTION__))
      ->setCheckPermissions($checkPermissions);
  }

  /**
   * SecurePay process function.
   *
   * @param bool $checkPermissions
   *
   * @return \Civi\Api4\Action\Securepay\Process
   */
  public static function process(bool $checkPermissions = TRUE): Process {
    return (new Process(__CLASS__, __FUNCTION__))
      ->setCheckPermissions($checkPermissions);
  }

  public static function permissions() {
    return [
      'submit' => ['post_securepay'],
    ];
  }
}
