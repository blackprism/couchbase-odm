<?php

namespace Blackprism\CouchbaseODM\Bucket;

/**
 * Interface ProviderAware
 */
interface ProviderAware
{
    /**
     * @param Provider $provider
     */
    public function providerIs(Provider $provider);
}
