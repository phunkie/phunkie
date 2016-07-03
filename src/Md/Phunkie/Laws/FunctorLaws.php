<?php

namespace Md\Phunkie\Laws;

use Md\Phunkie\Types\Function1;
use Md\Phunkie\Types\Kind;
use Md\Phunkie\Types\Option;

trait FunctorLaws
{
    public function covariantIdentity(Kind $fa, Option $forArg): bool
    {
        return $fa->eqv($fa->map(Function1::identity()), $forArg);
    }

    public function covariantComposition(Kind $fa, Function1 $f, Function1 $g): bool
    {
        return $fa->map($f)->map($g)->eqv($fa->map($f->andThen($g)), Some(42));
    }
}