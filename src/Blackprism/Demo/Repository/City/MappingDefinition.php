<?php

namespace Blackprism\Demo\Repository\City;

use Blackprism\CouchbaseODM\Repository\IsMapping;
use Blackprism\CouchbaseODM\Repository\GiveMapping;
use Blackprism\CouchbaseODM\Repository\Mapping;
use Blackprism\CouchbaseODM\Repository\MappingFactory;
use Blackprism\Demo\Model\City;
use Blackprism\Demo\Repository\Country;
use Blackprism\Demo\Repository\Geo;
use Blackprism\Demo\Repository\Mayor;

class MappingDefinition implements GiveMapping
{
    public function getMapping(MappingFactory $mappingFactory): IsMapping
    {
        $mapping = new Mapping();
        $mapping
            ->classIs(City::class)
            ->propertyTypeIs('type', 'city')
            ->propertyHasAccessors('id', 'setId', 'getId')
            ->propertyHasAccessors('name', 'setName', 'getName')
            ->propertyHasMappingAndAccessors(
                'country',
                $mappingFactory->get(new Country\MappingDefinition()),
                'countryIs',
                'getCountry'
            )
            ->propertyHasMappingAndAccessors(
                'geo',
                $mappingFactory->get(new Geo\MappingDefinition()),
                'setGeo',
                'getGeo'
            )
            ->propertyHasMappingAndAccessors(
                'mayor',
                $mappingFactory->get(new Mayor\MappingDefinition()),
                'setMayor',
                'getMayor'
            )
        ;

        var_dump(self::class . " created");

        return $mapping;
    }
}
