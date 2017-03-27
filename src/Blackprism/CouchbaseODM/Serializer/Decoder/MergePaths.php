<?php

declare(strict_types = 1);

namespace Blackprism\CouchbaseODM\Serializer\Decoder;

use ArrayIterator;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Traversable;

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
     * @param array  $values
     * @param string $key
     *
     * @return array
     */
    private function mergeWhenKeyIsComposed(array $values, string $key): array
    {
        if (strpos($key, '.') !== false) {
            return $this->merge($values);
        }

        return $values;
    }

    /**
     * @param array  $values
     * @param string $key
     * @param array  $valuesToAppend
     *
     * @return array
     */
    private function appendOrReplaceForKey(array $values, string $key, array $valuesToAppend): array
    {
        if (isset($values[$key]) === true) {
            $values[$key] = array_replace($values[$key], $valuesToAppend);
            return $values;
        }

        $values[$key] = $valuesToAppend;

        return $values;
    }

    /**
     * Explode composed keys like city.id in city['id]', city.country.president into city['country']['president']
     *
     * @param array $values
     *
     * @return mixed
     */
    private function merge(array $values)
    {
        foreach ((new KeepComposedKey(new ArrayIterator($values))) as $key => $value) {
            list($mainKey, $subKey) = explode('.', $key, 2);

            $subValues = $this->mergeWhenKeyIsComposed([$subKey => $value], $subKey);
            $values = $this->appendOrReplaceForKey($values, $mainKey, $subValues);
            unset($values[$key]);
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
        // @TODO bizarre le type hint est string et on dit que ça peut être un array un objet ou autre ?!
        if (is_array($data) === true) {
            array_walk($data, function (&$item) {
                $item = $this->merge($item);
            });
        }

        if ($data instanceof Traversable) {
            iterator_apply($data, function (&$item) {
                $item = $this->merge($item);
            });
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
