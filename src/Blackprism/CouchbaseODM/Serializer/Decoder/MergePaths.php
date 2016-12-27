<?php

declare(strict_types = 1);

namespace Blackprism\CouchbaseODM\Serializer\Decoder;

use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;

/**
 * MergePaths
 */
class MergePaths implements ChainableInterface
{

    /**
     * Type to use for output of denormalize.
     *
     * @var string
     */
    private $type;

    /**
     * @var ChainableInterface
     */
    private $next = null;

    /**
     * MergePaths constructor.
     *
     * @param string $type type to use for output of denormalize
     */
    public function __construct(string $type)
    {
        $this->type = $type;
    }

    /**
     * @param DecoderInterface $chainableDecoder
     *
     * @return ChainableInterface
     */
    public function nextIs(ChainableInterface $chainableDecoder): ChainableInterface
    {
        $this->next = $chainableDecoder;

        return $this;
    }

    /**
     * @param mixed $data
     * @param string $format
     * @param array $context
     *
     * @return int|string|bool|array
     *
     * @throws UnexpectedValueException
     */
    private function next($data, $format, array $context = array())
    {
        if ($this->next !== null) {
            return $this->next->decode($data, $format, $context);
        }

        return $data;
    }

    /**
     * Merge composed keys like city.id in city, city.country.president into city and country
     *
     * @param array $values
     *
     * @return mixed
     */
    private function merge(array $values)
    {
        foreach ($values as $key => $value) {
            // Composed key
            $subKey = explode('.', $key, 2);

            if ($subKey !== [$key]) {
                if (isset($values[$subKey[0]][$subKey[1]]) === true
                    && is_array($values[$subKey[0]][$subKey[1]]) === true) {
                    $values[$subKey[0]][$subKey[1]] = array_replace($values[$subKey[0]][$subKey[1]], $values[$key]);
                } else {
                    $values[$subKey[0]][$subKey[1]] = $values[$key];
                }
                unset($values[$key]);

                if (strpos($subKey[1], '.') !== false) {
                    $values[$subKey[0]] = $this->merge($values[$subKey[0]]);
                }
            }
        }

        return $values;
    }

    /**
     * Decodes a string into PHP data.
     *
     * @param string $data    Data to decode
     * @param string $format  Format name
     * @param array  $context options that decoders have access to
     *
     * The format parameter specifies which format the data is in; valid values
     * depend on the specific implementation. Authors implementing this interface
     * are encouraged to document which formats they support in a non-inherited
     * phpdoc comment.
     *
     * @return mixed
     *
     * @throws UnexpectedValueException
     */
    public function decode($data, $format, array $context = array())
    {
        if (is_array($data) === true || $data instanceof \Traversable) {
            foreach ($data as &$item) {
                $item = $this->merge($item);
            }
        }

        return $this->next($data, $format, $context);
    }

    /**
     * Checks whether the deserializer can decode from given format.
     *
     * @param string $format format name
     *
     * @return bool
     */
    public function supportsDecoding($format)
    {
        if ($format === self::class || $format === $this->type) {
            echo self::class . "\n";
            return true;
        }

        return false;
    }
}
