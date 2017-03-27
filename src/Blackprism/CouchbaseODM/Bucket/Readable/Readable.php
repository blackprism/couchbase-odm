<?php

declare(strict_types = 1);

namespace Blackprism\CouchbaseODM\Bucket\Readable;

use Blackprism\CouchbaseODM\Exception\Exception;

/**
 * Interface Executable
 */
interface Readable
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
