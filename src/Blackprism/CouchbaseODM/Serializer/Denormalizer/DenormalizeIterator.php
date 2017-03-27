<?php

declare(strict_types = 1);

namespace Blackprism\CouchbaseODM\Serializer\Denormalizer;

use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Traversable;

class DenormalizeIterator implements \IteratorAggregate
{
    /**
     * @var Traversable
     */
    private $traversable;

    /**
     * @var DenormalizerInterface
     */
    private $denormalizer;

    /**
     * @var string
     */
    private $typeProperty;

    /**
     * DenormalizeIterator constructor.
     *
     * @param Traversable           $traversable
     * @param DenormalizerInterface $denormalizer
     * @param string                $typeProperty
     */
    public function __construct(Traversable $traversable, DenormalizerInterface $denormalizer, string $typeProperty)
    {
        $this->traversable  = $traversable;
        $this->denormalizer = $denormalizer;
        $this->typeProperty = $typeProperty;
    }

    /**
     * Retrieve an external iterator
     *
     * @link  http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     * @since 5.0.0
     */
    public function getIterator()
    {
        foreach ($this->traversable as $key => $element) {
            // @TODO format et context ?
            yield
                $key => $this->denormalizer->denormalize(
                    $element,
                    $this->getClassForElement($element),
                    '$format',
                    [] //'$context'
                );
        }
    }

    /**
     * @param array $element
     *
     * @return string
     */
    private function getClassForElement(array $element): string
    {
        if (isset($element[$this->typeProperty]) === true) {
            return $element[$this->typeProperty];
        }

        // @TODO pas encore géré le typeless
        //$class = $this->typelessDenormalizer;
        var_dump("Pas trouvé de type", $this->typeProperty, $element);
        die;
    }
}
