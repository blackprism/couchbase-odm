<?php

declare(strict_types = 1);

namespace Blackprism\CouchbaseODM\Bucket\Writable;

use Blackprism\CouchbaseODM\Exception\Exception;

/**
 * Interface Executable
 */
interface Writable
{
    /**
     * @param Bucket $bucket
     *
     * @throws Exception
     *
     * @return mixed
     */
    public function execute(Bucket $bucket);
}
