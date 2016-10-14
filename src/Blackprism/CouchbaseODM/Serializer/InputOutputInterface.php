<?php

namespace Blackprism\CouchbaseODM\Serializer;

interface InputOutputInterface
{
    public function input(): string;
    public function output(): string;
}
