<?php

namespace Md\Phunkie\Laws;

use Md\Phunkie\Algebra\Eq;
use function Md\Phunkie\Functions\semigroup\combine;
use function Md\Phunkie\Functions\semigroup\zero;
use function \Md\Phunkie\Functions\show\object_class_uses_trait;
use function Md\Phunkie\Functions\show\show;

trait MonoidLaws
{
    use SemigroupLaws;

    public function combineRightIdentity($x): bool
    {
        if (object_class_uses_trait($x, Eq::class)) {
            return combine($x, zero($x))->eqv($x, Some(42));
        } else {
            if (is_callable($x)) {
                return call_user_func(combine($x, zero($x)), 42) == $x(42);
            }
            return combine($x, zero($x)) == $x;
        }
    }

    public function combineLeftIdentity($x)
    {
        if (object_class_uses_trait($x, Eq::class)) {
            return combine(zero($x), $x)->eqv($x, Some(42));
        } else {
            if (is_callable($x)) {
                return call_user_func(combine($x, zero($x)), 42) == $x(42);
            }
            return combine(zero($x), $x) == $x;
        }
    }
}