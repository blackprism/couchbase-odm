<?php

namespace Blackprism\Demo;

require_once '../../../vendor/autoload.php';

use Blackprism\CouchbaseODM\Bucket\Pool;
use Blackprism\CouchbaseODM\Connection;
use Blackprism\CouchbaseODM\Observer\PropertyChangedListener;
use Blackprism\CouchbaseODM\Observer\PropertyChangedListenerAwareInterface;
use Blackprism\CouchbaseODM\Repository\MappingFactory;
use Blackprism\CouchbaseODM\Repository\RepositoryFactory;
use Blackprism\CouchbaseODM\Serializer\SerializerFactory;
use Blackprism\CouchbaseODM\Value\BucketName;
use Blackprism\CouchbaseODM\Value\BucketSetting;
use Blackprism\CouchbaseODM\Value\ClassName;
use Blackprism\CouchbaseODM\Value\Dsn;
use Blackprism\Demo\Model;
use Blackprism\Demo\Repository\Country;
use Blackprism\Demo\Repository\Mayor;
use Blackprism\Demo\Repository2\City\Query\GetById;
use Blackprism\Serializer\Json\Serialize;

ini_set('xdebug.var_display_max_children', -1);
ini_set('xdebug.var_display_max_depth', -1);

// vagrant & vagrant password
// root & khew3btQ (random local password, so i can commit it :p)

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



$pool = new Pool();
$pool->bucketSettings(new BucketSetting(new Dsn('couchbase://localhost'), new BucketName('odm-test'), 'toto'));
echo "Pool defined\n";

$mappingFactory = new MappingFactory();
echo "MappingFactory created\n";

$repositoryFactory = new RepositoryFactory($pool, $mappingFactory);
echo "RepositoryFactory created\n";


$cityRepository  = $repositoryFactory->get(new \Blackprism\Demo\Repository\City\Repository());
$mayorRepository = $repositoryFactory->get(new Mayor\Repository());
/*
var_dump($mayorRepository->getMayors());
die;

$city1 = $cityRepository->get('city-1');

var_dump($city1);

echo "-----\n";
//$result = $cityBucketWithConnection->getCitiesWithMayor();
$result = $cityRepository->getCitiesWithMayorAndMergePath();
var_dump($result);
foreach ($result as $truc) {
    var_dump($truc);
}
die;
*/


//
//try {
//    var_dump($cityRepository->get(new DocumentId('test-counter')));
//} catch (NoSuchKey $exception) {

//echo "Ask city-1\n";
//var_dump($cityRepository->get('city-1'));
//die;
//echo "Ask city-1 by N1QL\n";
//var_dump($cityRepository->getCity1ByN1QL());
//die;
//var_dump($cityRepository->getJuice());
//die;
//echo "Ask one juice as a city\n";
//$city = $cityRepository->getJuiceAsCity();
//var_dump($city);
//die;
//echo "Ask one city with mayor\n";
//$city = $cityRepository->getCityWithMayor('city-internal-4');
//var_dump($city);
//die;
//echo "Ask cities with mayor in mergepath mode\n";
//$cities = $cityRepository->getCitiesWithMayorAndMergePath();
//var_dump($cities);
//foreach ($cities as $city) {
//    var_dump($city);
//}
//die;

echo "Ask cities with mayor\n";
$cities = $cityRepository->getCitiesWithMayor();
foreach ($cities as $city) {
    var_dump($city);
}

$cities[2]->setName('Paris (' . uniqid('edited-') . ')');
$country = $cities[2]->getCountry();
$country->setName('France (' . uniqid('edited-') . ')');
$cities[2]->countryIs($country);
$mayor = $cities[2]->getMayor();
$mayor->setFirstname('Anne (' . uniqid('edited-') . ')');
$cities[2]->setMayor($mayor);
//var_dump($cities);
//var_dump($cities[2]);
var_dump($cities[2]->getPropertiesChanged());
//
$cityRepository->save($cities[2]);
//var_dump($cities[2], $documentsToUpdate);
//
//die;

//try {
//    var_dump($mayorRepository->getMayors());
//} catch (\Exception $e) {
//    var_dump($e);
//}

$france = new Model\Country();
$france->setName('France (yeah this one)');

$mayorLuxiol = new Model\Mayor();
//$mayorLuxiol->setId('mayor-2');
$mayorLuxiol->setFirstname('Christophe');
$mayorLuxiol->setLastname('Colin');

$luxiol = new Model\City();
//$luxiol->setId('city-3');
$luxiol->setName('Luxiol');
$luxiol->setMayorId('mayor-2');
$luxiol->countryIs($france);
$luxiol->setMayor($mayorLuxiol);

//var_dump($luxiol);
//$cityRepository->getBucket()->save($json);
$documentsToUpdate = $cityRepository->getSerializer()->serialize($luxiol, 'array');
var_dump($documentsToUpdate);
die;


$mayorPalaiseau = new Model\Mayor();
//$mayorPalaiseau->setId('mayor-1');
$mayorPalaiseau->setFirstname('Grégoire');
$mayorPalaiseau->setLastname('Lasteyrie');

$geo = new Model\Geo();
$geo->setLat(37.7825);
$geo->setLon(-122.393);

$palaiseau = new Model\City();
//$palaiseau->setId('city-3');
$palaiseau->setName('Palaiseau');
$palaiseau->setMayorId('mayor-1');
$palaiseau->countryIs(clone $france);
$palaiseau->setMayor($mayorPalaiseau);
$palaiseau->setGeo($geo);

$cities = [['city' => $luxiol], ['city' => $palaiseau]];
$cities = [$luxiol, $palaiseau];

//var_export($cities);
$serializer = $cityRepository->getSerializer();


$json = $serializer->serialize($cities, 'json');

var_dump($json, json_decode($json, true));

die;
$cityRepository->getBucket()->save($json);

exit;
system("echo '" . $serializer->serializeCollection($cities) . "' | jsonpp");
