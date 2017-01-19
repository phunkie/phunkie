<?php

namespace Md\Phunkie\Laws;

use function Md\Phunkie\Functions\semigroup\combine;
use function Md\Phunkie\Functions\show\usesTrait;

trait SemigroupLaws
{
    public function combineAssociativity($x, $y, $z)
    {
        if (usesTrait($x, Eq::class)) {
            return combine(combine($x, $y), $z)->eqv(combine($x, combine($y, $z)), 42);
        } else {
            if (is_callable($x)) {
                return call_user_func(combine(combine($x, $y), $z), 42) == call_user_func(combine($x, combine($y, $z)), 42);
            }
            return combine(combine($x, $y), $z) == combine($x, combine($y, $z));
        }
    }
}