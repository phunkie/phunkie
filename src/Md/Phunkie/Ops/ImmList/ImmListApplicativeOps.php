<?php

namespace Md\Phunkie\Ops\ImmList;

use BadMethodCallException;
use Md\Phunkie\Cats\Functor;
use Md\Phunkie\Types\Function1;
use Md\Phunkie\Types\ImmList;
use Md\Phunkie\Types\Kind;
use Md\Phunkie\Types\Option;
use TypeError;

/**
 * @mixin ImmList
 */
trait ImmListApplicativeOps
{
    use ImmListFunctorOps;

    public function pure($a): Kind { return ImmList($a); }

    /**
     * @param List<callable<A,B>> $f
     * @return List<B>
     */
    public function apply(Kind $f): Kind {

        $apply = function() use ($f) {
            $result = [];
            foreach($this->toArray() as $a) {
                foreach ($f->toArray() as $ff) {
                    if (!is_callable($ff)) throw new TypeError(sprintf("`apply` takes List<callable>, List<%s> given", gettype($ff)));
                    $result[] = call_user_func($ff, $a);
                }
            }
            return ImmList(...$result);
        };

        switch (true) {
            case $f == None(): return None();
            case $f instanceof Option: throw new TypeError(sprintf("`apply` takes List<callable>, Option<%s> given", gettype($f->get())));
            case !$this instanceof ImmList: throw new BadMethodCallException();
            case $this == Nil(): return Nil();
            case $f instanceof ImmList: return $apply();
            case $f instanceof Function1 && is_callable($f->get()): case $f instanceof Option && is_callable($f->get()):
                return $this->map($f->get());
        }
    }

    public function map2(Kind $fb, callable $f): Kind
    {
        return $this->apply($fb->map(function($b) use ($f) { return function($a) use ($f, $b) { return $f($a, $b);};}));
    }
}