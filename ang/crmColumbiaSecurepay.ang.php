<?php
// This file declares an Angular module which can be autoloaded
// in CiviCRM. See also:
// \https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_angularModules/n
return [
  'js' => [
    'ang/crmColumbiaSecurepay.js',
    'ang/crmColumbiaSecurepay/*.js',
    'ang/crmColumbiaSecurepay/*/*.js',
  ],
  'css' => [
    'ang/crmColumbiaSecurepay.css',
  ],
  'partials' => [
    'ang/crmColumbiaSecurepay',
  ],
  'requires' => [
    'crmUi',
    'crmUtil',
    'ngRoute',
  ],
  'settings' => [],
];
