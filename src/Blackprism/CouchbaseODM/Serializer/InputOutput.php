<?php

namespace Blackprism\CouchbaseODM\Serializer;

class InputOutput implements InputOutputInterface
{
    /**
     * InputOutput constructor.
     *
     * @param string $input
     * @param string $output
     */
    public function __construct(string $input, string $output)
    {
        $this->input = $input;
        $this->output = $output;
    }

    public function input(): string
    {
        return $this->input;
    }

    public function output(): string
    {
        return $this->output;
    }
}
