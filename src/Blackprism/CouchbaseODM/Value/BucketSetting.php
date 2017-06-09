<?php

declare(strict_types = 1);

namespace Blackprism\CouchbaseODM\Value;

/**
 * BucketSetting
 *
 * @property Dsn $dsn
 * @property BucketName $bucket
 * @property string $password
 */
final class BucketSetting
{
    /**
     * @var Dsn
     */
    private $dsn;

    /**
     * @var BucketName
     */
    private $bucket;

    /**
     * @var string
     */
    private $password;

    /**
     * BucketSetting constructor.
     *
     * @param Dsn        $dsn
     * @param BucketName $bucket
     * @param string     $password
     */
    public function __construct(Dsn $dsn, BucketName $bucket, string $password)
    {
        $this->dsn = $dsn;
        $this->bucket = $bucket;
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function dsn(): string
    {
        return $this->dsn->value();
    }

    /**
     * @return string
     */
    public function bucket(): string
    {
        return $this->bucket->value();
    }

    /**
     * @return string
     */
    public function password(): string
    {
        return $this->password;
    }
}
