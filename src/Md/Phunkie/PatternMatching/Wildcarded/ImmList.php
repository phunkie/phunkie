<?php

namespace Md\Phunkie\PatternMatching\Wildcarded;

class ImmList
{
    public $head;
    public $tail;

    public function __construct($head, $tail)
    {
        $this->head = $head;
        $this->tail = $tail;
    }
}