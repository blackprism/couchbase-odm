<?php

namespace Blackprism\Demo\Repository\Mayor;

use Blackprism\CouchbaseODM\Repository\GiveMapping;
use Blackprism\CouchbaseODM\Repository\IsMapping;
use Blackprism\CouchbaseODM\Repository\Mapping;
use Blackprism\CouchbaseODM\Repository\MappingFactory;
use Blackprism\Demo\Model\Mayor;

class MappingDefinition implements GiveMapping
{
    public function getMapping(MappingFactory $mappingFactory): IsMapping
    {
        $mapping = new Mapping();
        $mapping
            ->rootIs('mayor')
            ->classIs(Mayor::class)
            ->propertyHasAccessors('id', 'setId', 'getId')
            ->propertyHasAccessors('firstname', 'setFirstname', 'getFirstname')
            ->propertyHasAccessors('lastname', 'setLastname', 'getLastname')
        ;

        var_dump(self::class . " created");

        return $mapping;
    }
}
