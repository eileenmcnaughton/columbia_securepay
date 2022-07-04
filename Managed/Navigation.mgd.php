<?php
return [
  [
    'name' => 'Navigation_Secure_Pay',
    'entity' => 'Navigation',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'label' => 'Secure Pay',
        'name' => 'Secure Pay',
        'url' => 'civicrm/search#/display/SecurePay/SecurePay_Table_1',
        'icon' => NULL,
        'permission' => [
          'access CiviContribute',
        ],
        'permission_operator' => 'AND',
        'parent_id.name' => 'Contributions',
        'is_active' => TRUE,
        'has_separator' => 0,
        'weight' => 15,
        'domain_id' => 'current_domain',
      ],
    ],
  ],
];
