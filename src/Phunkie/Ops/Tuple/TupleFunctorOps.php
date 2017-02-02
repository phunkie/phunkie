<?php

namespace Phunkie\Ops\Tuple;

use Phunkie\Ops\FunctorOps;
use Phunkie\Types\Kind;

trait TupleFunctorOps
{
    use FunctorOps;
    abstract public function toArray(): array;
    public function map(callable $f): Kind
    {
        return Tuple(...array_map($f, $this->toArray()));
    }

    public function imap(callable $f, callable $g): Kind
    {
        return $this->map($f);
    }
}