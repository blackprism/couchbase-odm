<?php

namespace Blackprism\CouchbaseODM\Bucket;

interface transcodersAware
{
    public function transcodersAre(callable $encoder, callable $decoder);
}
