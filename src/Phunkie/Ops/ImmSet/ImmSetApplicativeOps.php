<?php

namespace Phunkie\Ops\ImmSet;

use BadMethodCallException;
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

        switch (true) {
            case $f == None(): return None();
            case !$this instanceof ImmSet: throw new BadMethodCallException();
            case $f instanceof ImmSet: return $apply();
            case $f instanceof Function1 && is_callable($f->get()):
                return $this->map($f->get());
            default: throw new BadMethodCallException();
        }
    }

    public function map2(Kind $fb, callable $f): Kind
    {
        return $this->apply($fb->map(function ($b) use ($f) {
            return function ($a) use ($f, $b) {
                return $f($a, $b);
            };
        }));
    }

    public function pure($a): Kind
    {
        return ImmSet($a);
    }
}
