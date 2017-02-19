<?php

namespace Phunkie\Utils\Trampoline;

class More extends Trampoline
{
    private $k;

    public function __construct(callable $k)
    {
        $this->k = $k;
    }

    public function get(): Trampoline
    {
        return ($this->k)();
    }
}