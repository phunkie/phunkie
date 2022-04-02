<?php

namespace Phunkie\Utils\Trampoline;

class Done extends Trampoline
{
    private $v;

    public function __construct($v)
    {
        $this->v = $v;
    }

    public function get()
    {
        return $this->v;
    }
}
