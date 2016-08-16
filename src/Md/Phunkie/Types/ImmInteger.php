<?php

namespace Md\Phunkie\Types;

final class ImmInteger
{
    private $value;

    public function __construct(int $value)
    {
        $this->value = $value;
    }

    public function get()
    {
        return $this->value;
    }
}