<?php

namespace Md\Phunkie\Validation;

use function Md\Phunkie\Functions\show\showValue;
use Md\Phunkie\Types\Kind;

class Failure extends Validation
{
    private $invalid;

    public function __construct($invalid)
    {
        $this->invalid = $invalid;
    }

    public function toString(): string
    {
        return "Failure(" . showValue($this->invalid) . ")";
    }

    public function getOrElse($default)
    {
        return $default;
    }

    public function map(callable $f): Kind
    {
        return $this;
    }

    public function fold($fe, $fa)
    {
        return $fe($this->invalid);
    }
}