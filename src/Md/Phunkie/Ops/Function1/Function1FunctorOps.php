<?php

namespace Md\Phunkie\Ops\Function1;

use Md\Phunkie\Ops\FunctorOps;
use Md\Phunkie\Types\Kind;

/**
 * @mixin \Md\Phunkie\Types\Function1
 */
trait Function1FunctorOps
{
    use FunctorOps;
    public function map(callable $f): Kind
    {
        return $this->andThen($f);
    }

    public function imap(callable $f,callable $g): Kind
    {
        return $this->map($f);
    }
}