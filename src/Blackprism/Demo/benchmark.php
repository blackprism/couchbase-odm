<?php

ini_set('xdebug.var_display_max_children', -1);
ini_set('xdebug.var_display_max_depth', -1);

interface Zoum
{

}

class Bouh implements Zoum
{
    private $id = 3;

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }
}

class Bouh2
{
    public function __call($name, $arguments)
    {
        return 4;
    }
}

$bouh = new Bouh();
$bouh->setId(8);
$spl = spl_object_hash($bouh);
unset($bouh);
$bouh2 = new Bouh();
$spl2 = spl_object_hash($bouh2);
var_dump($spl === $spl2, $spl, $spl2);
die;

$bouh2 = new Bouh2();
$start = microtime(true);
for ($i = 0; $i < 10000; $i++) {
    $bouh instanceof Zoum;
}
$end = microtime(true);

echo ($end - $start) . "\n";

$start = microtime(true);
for ($i = 0; $i < 10000; $i++) {
    $bouh2 instanceof Zoum;
}
$end = microtime(true);

echo ($end - $start) . "\n";
exit;


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

function recompose($values) {
    foreach ($values as $key => $value) {
        // Composed key
        $subKey = explode('.', $key, 2);

        if ($subKey !== [$key]) {
            //var_dump($key, $values);
            //var_dump($key . " set into values[" . $subKey[0] . "][" . $subKey[1] . "]");

            if (isset($values[$subKey[0]][$subKey[1]]) === true
                && is_array($values[$subKey[0]][$subKey[1]]) === true) {
                $values[$subKey[0]][$subKey[1]] = array_replace($values[$subKey[0]][$subKey[1]], $values[$key]);
            } else {
                $values[$subKey[0]][$subKey[1]] = $values[$key];
            }
            unset($values[$key]);

            //var_dump($values);

            if (strpos($subKey[1], '.') !== false) {
                $values[$subKey[0]] = recompose($values[$subKey[0]]);
            }
        }
    }

    return $values;
}

foreach ($data as &$values) {
    $values = recompose($values);

}
var_dump($data);
die;


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
