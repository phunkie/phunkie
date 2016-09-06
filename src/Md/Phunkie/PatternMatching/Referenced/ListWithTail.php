<?php

namespace Md\Phunkie\PatternMatching\Referenced;

class ListWithTail
{
    public $head;
    public $tail;

    public function __construct(&$x, &$xs)
    {
        $this->head = &$x;
        $this->tail = &$xs;
    }
}