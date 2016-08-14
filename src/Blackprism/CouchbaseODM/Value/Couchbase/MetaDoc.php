<?php

declare(strict_types = 1);

namespace Blackprism\CouchbaseODM\Value\Couchbase;

/**
 * MetaDoc
 */
final class MetaDoc
{
    /**
     * @var mixed
     */
    private $value;

    /**
     * @TODO unknown type https://forums.couchbase.com/t/couchbasemetadoc-properties-type/9542
     *
     * @var string
     */
    private $error;

    /**
     * @var int
     */
    private $flags;

    /**
     * @var string|null
     */
    private $cas;

    /**
     * @TODO unknown type https://forums.couchbase.com/t/couchbasemetadoc-properties-type/9542
     *
     * @var string|null
     */
    private $token;

    /**
     * MetaDoc constructor.
     *
     * @param \CouchbaseMetaDoc $couchbaseMetaDoc
     */
    public function __construct(\CouchbaseMetaDoc $couchbaseMetaDoc)
    {
        $this->value = $couchbaseMetaDoc->value;
        $this->error = $couchbaseMetaDoc->error;
        $this->flags = $couchbaseMetaDoc->flags;
        $this->cas = $couchbaseMetaDoc->cas;
        $this->token = $couchbaseMetaDoc->token;

    }

    /**
     * @return mixed
     */
    public function value()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function error(): string
    {
        return $this->error;
    }

    /**
     * @return int
     */
    public function flags(): int
    {
        return $this->flags;
    }

    /**
     * @return string|null
     */
    public function cas()
    {
        return $this->cas;
    }

    /**
     * @return string|null
     */
    public function token()
    {
        return $this->token;
    }
}
