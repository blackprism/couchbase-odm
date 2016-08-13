<?php

namespace Blackprism\Couchbase\Repository;

use Blackprism\CouchbaseODM\Value\ClassName;

/**
 * Interface RepositoryFactoryInterface
 */
interface RepositoryFactoryInterface
{
    /**
     * @param ClassName $className
     *
     * @return object
     */
    public function get(ClassName $className);
}
