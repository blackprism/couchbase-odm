<?php

namespace Blackprism\Demo\Repository\Geo;

use Blackprism\CouchbaseODM\Repository\GiveMapping;
use Blackprism\CouchbaseODM\Repository\IsMapping;
use Blackprism\CouchbaseODM\Repository\Mapping;
use Blackprism\CouchbaseODM\Repository\MappingFactory;
use Blackprism\Demo\Model\Geo;

class MappingDefinition implements GiveMapping
{
    public function getMapping(MappingFactory $mappingFactory): IsMapping
    {
        $mapping = new Mapping();
        $mapping
            ->classIs(Geo::class)
            ->propertyTypeIs('type', 'geo')
            ->propertyHasAccessors('lat', 'setLat', 'getLat')
            ->propertyHasAccessors('lon', 'setLon', 'getLon')
        ;

        var_dump(self::class . " created");

        return $mapping;
    }
}
