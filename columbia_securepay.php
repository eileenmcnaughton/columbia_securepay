<?php

require_once 'columbia_securepay.civix.php';
// phpcs:disable
use CRM_ColumbiaSecurepay_ExtensionUtil as E;
// phpcs:enable

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
 */
function columbia_securepay_civicrm_config(&$config) {
  _columbia_securepay_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function columbia_securepay_civicrm_install() {
  _columbia_securepay_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function columbia_securepay_civicrm_enable() {
  _columbia_securepay_civix_civicrm_enable();
}

/**
 * https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_permission/
 *
 * @param array $permissions
 */
function columbia_securepay_civicrm_permission(array &$permissions) {
  $permissions['post_securepay'] = [
    E::ts('Secure Pay submit'),
    E::ts('Permits posting data from securepay'),
  ];
}

/**
 * Implements hook_civicrm_searchKitTasks().
 *
 * @param array[] $tasks
 */
function columbia_securepay_civicrm_searchKitTasks(&$tasks) {
  $tasks['Securepay']['process'] = [
    'module' => 'crmColumbiaSecurepay',
    'title' => E::ts('Process Securepay notification'),
    'icon' => 'fa-random',
    'uiDialog' => ['templateUrl' => '~/crmColumbiaSecurepay/process.html'],
  ];
}
