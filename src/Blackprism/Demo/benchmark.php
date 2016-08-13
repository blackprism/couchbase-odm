<?php

ini_set('xdebug.var_display_max_children', -1);
ini_set('xdebug.var_display_max_depth', -1);

require_once '../../../vendor/autoload.php';

$data = [[
    'city.mayor.country' => [
        'name' => 'France of Mayor'
    ],
    'city' => [
        'name' => 'Luxiol',
        'country' => 'France'
    ],
    'city.id' => 3,
    'city.mayor' => [
        'lastname' => 'Colin',
        'firstname' => 'Christophe'
    ]
]];


$data = '[
  {
    "city": {
      "country": {
        "name": "France",
        "type": "country"
      },
      "mayorId": "mayor-2",
      "name": "Luxiol",
      "type": "city"
    },
    "city.id": "city-3",
    "city.mayor": {
      "firstname": "Christophe",
      "lastname": "Colin",
      "type": "mayor"
    },
    "city.mayor.id": "mayor-2"
  },
  {
    "city": {
      "country": {
        "name": "France",
        "type": "country"
      },
      "geo": {
        "lat": 37.7825,
        "lon": -122.393
      },
      "mayorId": "mayor-1",
      "name": "Palaiseau",
      "type": "city"
    },
    "city.id": "city-1",
    "city.mayor": {
      "firstname": "Grégoire",
      "lastname": "Lasteyrie",
      "type": "mayor"
    },
    "city.mayor.id": "mayor-1"
  }
  ]';

$data = json_decode($data, true);


$elementArray = [
    'top.tip.tap',
    'toptiptap'
];

foreach ($elementArray as $element) {
    $start = microtime(true);
    for ($i = 0; $i < 10000; $i++) {
        if (strpos($element, '.') !== false) {
            $elements = explode('.', $element, 2);
            // processing
        }
    }
    $end = microtime(true);

    echo $element . " => strpos + explode : " . ($end - $start) . "\n";

    $start = microtime(true);
    for ($i = 0; $i < 10000; $i++) {
        $elements = explode('.', $element, 2);
        if ($elements !== [$element]) {
            // processing
        }
    }
    $end = microtime(true);

    echo $element . " => explode : " . ($end - $start) . "\n";
}
// time 19.53213095665
