<?php

namespace Md\Phunkie\Ops\Option;

use Md\Phunkie\Algebra\Monoid;
use function Md\Phunkie\Functions\currying\curry;
use function Md\Phunkie\Functions\semigroup\combine;
use function Md\Phunkie\Functions\semigroup\zero;
use Md\Phunkie\Types\Function1;

trait OptionFoldableOps
{
    public function foldLeft($initial)
    {
        return curry([$initial], func_get_args(), function(callable $f) use ($initial) {
            return $f($initial, $this->getOrElse(zero($initial)));
        });
    }

    public function foldRight($initial)
    {
        return curry([$initial], func_get_args(), function(callable $f) use ($initial) {
            return $f($this->getOrElse(zero($initial)), $initial);
        });
    }

    public function foldMap(callable $f)
    {
        $none = md5("None");
        return $this->foldLeft(zero($this->getOrElse($none)), function($b, $a) use ($f, $none) {
            if ($b == $none) $b = zero($a);
            return combine($b, $f($a));
        });
    }

    public function fold($initial)
    {
        return curry([$initial], func_get_args(), function(callable $f) use ($initial) {
            return $this->isDefined() ? $f($this->get()) : $initial;
        });
    }
}