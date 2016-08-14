<?php

namespace Blackprism\Demo;

require_once '../../../vendor/autoload.php';

use Blackprism\Demo\Model;
use Blackprism\Demo\Repository\City;
use Blackprism\Demo\Repository\Country;
use Blackprism\Demo\Repository\Mayor;
use Blackprism\CouchbaseODM\Connection;
use Blackprism\CouchbaseODM\Observer\PropertyChangedListener;
use Blackprism\CouchbaseODM\Repository\RepositoryFactory;
use Blackprism\CouchbaseODM\Serializer\SerializerFactory;
use Blackprism\CouchbaseODM\Value\ClassName;
use Blackprism\CouchbaseODM\Value\DocumentId;
use Blackprism\CouchbaseODM\Value\Dsn;
use Blackprism\Serializer\Json\Serialize;

ini_set('xdebug.var_display_max_children', -1);
ini_set('xdebug.var_display_max_depth', -1);

//

//$result = $bucket->query('select * from `odm-test` as city limit 1');
//var_dump($bucket->deserializeCollection($result));

/*
$configuration2 = new Configuration();
$flattenConfiguration = new Configuration\Object(new ClassName(Model\Flatten::class));
$flattenConfiguration
    ->attributeUseMethod('city', 'setCity', 'getCity')
    ->attributeUseMethod('country', 'setCountry', 'getCountry')
    ->registerToConfiguration($configuration2);

$result = $bucket->query('select city.name as city, country.name as country from `odm-test` as city');
*/
//var_dump($bucket->deserializeCollection($result, new Deserialize($configuration2), new ClassName(Flatten::class)));

$connection = new Connection\Connection(new Dsn('couchbase://localhost'));
echo "Connection defined\n";

$propertyChangedListener = new PropertyChangedListener();
echo "PropertyChangedListener created\n";

$serializerFactory = new SerializerFactory();
echo "SerializerFactory created\n";

$serializerFactory->propertyChangedListenerIs($propertyChangedListener);
echo "Injected PropertyChangedListener in serializerFactory\n";

$repositoryFactory = new RepositoryFactory($connection, $serializerFactory);
echo "RepositoryFactory created\n";

$cityRepository = $repositoryFactory->get(new ClassName(City\Repository::class));

var_dump($cityRepository->get(new DocumentId('test-counter')));
var_dump($cityRepository->get(new DocumentId('odwalla-juice1')));
var_dump($cityRepository->get(new DocumentId('city-1')));

echo "Ask cities with mayor\n";
$cities = $cityRepository->getCitiesWithMayor();
$cities[2]->setName('Paris (edited)');
var_dump($cities);


$mayorRepository = $repositoryFactory->get(new ClassName(Mayor\Repository::class));
$mayorRepository->connectionIs($connection);
var_dump($mayorRepository->getMayors());
die;



$data = '
  {
    "city": {
      "country": {
        "name": "France",
        "type": "country",
        "mayor": {
          "firstname": "Christophe",
          "lastname": "Colin",
          "type": "mayor"
        }
      },
      "name": "Luxiol",
      "type": "city"
    }
  }
  ';


$data = json_decode($data, true);

var_dump($data);
var_dump($cityRepository->bucket->update(new DocumentId('odwalla-juice1'), ['place' => 1, 'type' => 'test']));
die;

$mayorRepository = new Mayor\Repository();
$mayorRepository->connectionIs($connection);
var_dump($mayorRepository->getMayors());
die;

$france = new Model\Country();
$france->setName('France');

$mayorLuxiol = new Model\Mayor();
$mayorLuxiol->setId('mayor-2');
$mayorLuxiol->setFirstname('Christophe');
$mayorLuxiol->setLastname('Colin');

$luxiol = new Model\City();
$luxiol->setId('city-3');
$luxiol->setName('Luxiol');
$luxiol->setMayorId('mayor-2');
$luxiol->countryIs($france);
$luxiol->setMayor($mayorLuxiol);

$mayorPalaiseau = new Model\Mayor();
$mayorPalaiseau->setId('mayor-1');
$mayorPalaiseau->setFirstname('Grégoire');
$mayorPalaiseau->setLastname('Lasteyrie');

$geo = new Model\Geo();
$geo->setLat(37.7825);
$geo->setLon(-122.393);

$palaiseau = new Model\City();
$luxiol->setId('city-3');
$palaiseau->setName('Palaiseau');
$palaiseau->setMayorId('mayor-1');
$palaiseau->countryIs(clone $france);
$palaiseau->setMayor($mayorPalaiseau);
$palaiseau->setGeo($geo);

$cities = [['city' => $luxiol], ['city' => $palaiseau]];
$cities = ['city' => $luxiol, 'city' => $palaiseau];

var_export($cities);
$serializer = new Serialize($configuration);

system("echo '" . $serializer->serializeCollection($cities) . "' | jsonpp");
