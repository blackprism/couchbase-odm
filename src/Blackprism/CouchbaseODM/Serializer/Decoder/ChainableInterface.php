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
     * @param ChainableInterface $chainableDecoder
     *
     * @return self
     */
    public function nextIs(ChainableInterface $chainableDecoder): self;
}
