<?php

namespace Md\Phunkie\PatternMatching\Referenced;

class Cons
{
    public $head;
    public $tail;

    public function __construct(&$x, &$xs)
    {
        $this->head = &$x;
        $this->tail = &$xs;
    }
}