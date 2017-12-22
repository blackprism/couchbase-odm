<?php

namespace Blackprism\Demo\Repository\Country;

use Blackprism\CouchbaseODM\Repository\GiveMapping;
use Blackprism\CouchbaseODM\Repository\IsMapping;
use Blackprism\CouchbaseODM\Repository\Mapping;
use Blackprism\CouchbaseODM\Repository\MappingFactory;
use Blackprism\Demo\Model\Country;
use Blackprism\Demo\Repository\Geo;

class MappingDefinition implements GiveMapping
{
    public function getMapping(MappingFactory $mappingFactory): IsMapping
    {
        $mapping = new Mapping();
        $mapping
            ->classIs(Country::class)
            ->propertyTypeIs('type', 'country')
            ->propertyHasAccessors('name', 'setName', 'getName')
            ->propertyHasMappingAndAccessors(
                'geo',
                $mappingFactory->get(Geo\MappingDefinition::class),
                'setGeo',
                'getGeo'
            )
        ;

        var_dump(self::class . " created");

        return $mapping;
    }
}
