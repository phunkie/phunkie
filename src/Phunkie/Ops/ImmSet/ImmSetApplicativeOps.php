<?php

namespace Phunkie\Ops\ImmSet;

use BadMethodCallException;
use Phunkie\Cats\Applicative;
use Phunkie\Types\Function1;
use Phunkie\Types\ImmSet;
use Phunkie\Types\Kind;
use TypeError;

trait ImmSetApplicativeOps
{
    public function apply(Kind $f): Kind
    {
        $apply = function () use ($f) {
            $result = [];
            foreach ($this->toArray() as $a) {
                foreach ($f->toArray() as $ff) {
                    if (!is_callable($ff)) {
                        throw new TypeError(sprintf("`apply` takes Set<callable>, Set<%s> given", gettype($ff)));
                    }
                    $result[] = call_user_func($ff, $a);
                }
            }
            return ImmSet(...$result);
        };

        return match (true) {
            $f == None() => None(),
            !$this instanceof ImmSet => throw new BadMethodCallException(),
            $f instanceof ImmSet => $apply(),
            $f instanceof Function1 && is_callable($f->get()) => $this->map($f->get()),
            default => throw new BadMethodCallException()
        };
    }

    public function map2(Kind $fb, callable $f): Kind
    {
        return $this->apply($fb->map(fn ($b) => fn ($a) => $f($a, $b)));
    }

    public function pure($a): Applicative
    {
        return ImmSet($a);
    }
}
