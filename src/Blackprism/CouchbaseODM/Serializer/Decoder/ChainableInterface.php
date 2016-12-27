<?php

declare(strict_types = 1);

namespace Blackprism\CouchbaseODM\Serializer\Decoder;

use Symfony\Component\Serializer\Encoder\DecoderInterface;

/**
 * Interface ChainableInterface
 */
interface ChainableInterface extends DecoderInterface
{
    /**
     * @param DecoderInterface $decoder
     *
     * @return self
     */
    public function nextIs(DecoderInterface $decoder): self;
}
