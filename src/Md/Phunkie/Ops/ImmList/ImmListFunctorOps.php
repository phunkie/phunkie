<?php

namespace Md\Phunkie\Ops\ImmList;

use Md\Phunkie\Ops\FunctorOps;
use Md\Phunkie\Types\Kind;

trait ImmListFunctorOps
{
    use FunctorOps;
    public function map(callable $f): Kind
    {
        return ImmList(...array_map($f, $this->values));
    }

    public function imap(callable $f,callable $g): Kind
    {
        return $this->map($f);
    }
}