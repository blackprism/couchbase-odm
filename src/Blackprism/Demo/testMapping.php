<?php

namespace Blackprism\Demo;

require_once '../../../vendor/autoload.php';

use Blackprism\CouchbaseODM\Bucket\Pool;
use Blackprism\CouchbaseODM\Observer\PropertyChangedListener;
use Blackprism\CouchbaseODM\Observer\PropertyChangedListenerAwareInterface;
use Blackprism\CouchbaseODM\Repository\RepositoryFactory;
use Blackprism\CouchbaseODM\Repository\MappingFactory;
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

//$connection = new Connection\Connection(new Dsn('couchbase://localhost'));
//echo "Connection defined\n";

$pool = new Pool();
$pool->bucketSettings(new BucketSetting(new Dsn('couchbase://localhost'), new BucketName('odm-test'), 'voiture'));
echo "Pool defined\n";

$mappingFactory = new MappingFactory();
echo "MappingFactory created\n";

$repositoryFactory = new RepositoryFactory($pool, $mappingFactory);
echo "RepositoryFactory created\n";


$cityBucketWithConnection = $repositoryFactory->get(new \Blackprism\Demo\Repository\City\Repository());

$city1 = $cityBucketWithConnection->get('city-1');
var_dump($city1);
exit;
//
//
//echo "-----\n";
//$data = $cityBucketWithConnection->getCitiesAndMayor();
//foreach ($data as $datum) {
//    echo str_repeat('-', 100) . "\n";
////    $datum->setFirstname("ANNE");
////    $datum->setLastname("HIDALGO");
//    var_dump($datum);
//}


echo "-----\n";
$data = $cityBucketWithConnection->getCitiesWithMayor();
foreach ($data as $datum) {
    echo str_repeat('-', 100) . "\n";
//    $datum->setName("Boum");
//    $datum->setFirstname("ANNE");
//    $datum->setLastname("HIDALGO");
    var_dump($datum);
}
die;



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
//die;
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
$documentsToUpdate = $cityRepository->getSerializer()->serialize($cities[2], 'json');
var_dump($documentsToUpdate);
die;
//
//$cityRepository->save($cities[2]);
//var_dump($cities[2], $documentsToUpdate);
//
//die;



$mayorRepository = $repositoryFactory->get(new ClassName(Mayor\Repository::class));
$mayorRepository->connectionIs($connection);

//try {
//    var_dump($mayorRepository->getMayors());
//} catch (\Exception $e) {
//    var_dump($e);
//}

//var_dump($luxiol);
//$cityRepository->getBucket()->save($json);
$documentsToUpdate = $cityRepository->getSerializer()->serialize($luxiol, 'array');
var_dump($documentsToUpdate);
die;
