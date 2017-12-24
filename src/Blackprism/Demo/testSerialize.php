<?php


namespace Blackprism\Demo;

use Blackprism\CouchbaseODM\Bucket\Pool;
use Blackprism\CouchbaseODM\Repository\MappingFactory;
use Blackprism\CouchbaseODM\Serializer\Normalizer;
use Blackprism\Demo\Repository\City\MappingDefinition;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;

require_once '../../../vendor/autoload.php';

$mappingFactory = new MappingFactory();

$france = new Model\Country();
$france->setName('France (yeah this one)');

$mayorLuxiol = new Model\Mayor();
$mayorLuxiol->setId('mayor-2');
$mayorLuxiol->setFirstname('Christophe');
$mayorLuxiol->setLastname('Colin');

$luxiol = new Model\City();
$luxiol->setId('city-1');
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
$palaiseau->setId('city-3');
$palaiseau->setName('Palaiseau');
$palaiseau->setMayorId('mayor-1');
$palaiseau->countryIs(clone $france);
$palaiseau->setMayor($mayorPalaiseau);
$palaiseau->setGeo($geo);

$cities = [['city' => $luxiol], ['city' => $palaiseau]];
$cities = [$luxiol, $palaiseau];


$normalizers = [
    new Normalizer\Collection(),
    new Normalizer\Mapping(
        $mappingFactory->get(new MappingDefinition())
    )
];

$encoders = [
    new JsonEncoder()
];
$serializer = new Serializer($normalizers, $encoders);
$documents = $serializer->serialize($cities, 'json');

system("echo '" . $documents . "' | jsonpp");
