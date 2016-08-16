<?php

namespace Md\Phunkie\Types;

final class ImmString
{
    private $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function get()
    {
        return $this->value;
    }

    public function __toString()
    {
        return $this->value;
    }
}