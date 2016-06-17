<?php

namespace Md\Phunkie\Std\ImmList;

use Md\Phunkie\Cats\Functor;
use Md\Phunkie\Types\Kind;

trait ImmListFunctorOps
{
    use Functor;
    public function map(callable $f): Kind
    {
        return ImmList(...array_map($f, $this->values));
    }

    public function imap(callable $f,callable $g): Kind
    {
        return $this->map($f);
    }
}