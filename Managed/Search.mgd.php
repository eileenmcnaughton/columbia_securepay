<?php

return [
  [
    'name' => 'SavedSearch_SecurePay',
    'entity' => 'SavedSearch',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'SecurePay',
        'label' => 'SecurePay',
        'form_values' => NULL,
        'search_custom_id' => NULL,
        'api_entity' => 'Securepay',
        'api_params' => [
          'version' => 4,
          'select' => [
            'order_id',
            'contribution_id',
            'receive_date',
            'amount',
            'order_status_id:label',
            'processing_status_id:label',
            'first_name',
            'last_name',
            'is_test',
            'email',
            'street_address',
            'supplemental_address_1',
            'postal_code',
            'city',
            'state',
            'modified_date',
            'created_date',

            'data',
            'id',
          ],
          'orderBy' => [],
          'where' => [],
          'groupBy' => [],
          'join' => [],
          'having' => [],
        ],
        'expires_date' => NULL,
        'description' => NULL,
        'mapping_id' => NULL,
      ],
    ],
  ],
];
