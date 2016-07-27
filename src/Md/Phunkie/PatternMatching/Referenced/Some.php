<?php

namespace Md\Phunkie\PatternMatching\Referenced;

class Some
{
    public $value;
    public function __construct(&$value)
    {
        $this->value = &$value;
    }
}