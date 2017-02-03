<?php

namespace Phunkie\Ops\ImmSet;

use Phunkie\Ops\FunctorOps;
use Phunkie\Types\Kind;

trait ImmSetFunctorOps
{
    use FunctorOps;
    abstract public function toArray();

    public function map(callable $f): Kind
    {
        return ImmSet(...array_map($f, $this->toArray()));
    }

    public function imap(callable $f, callable $g): Kind
    {
        return $this->map($f);
    }
}