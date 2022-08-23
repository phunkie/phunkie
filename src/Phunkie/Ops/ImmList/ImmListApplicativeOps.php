<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\Ops\ImmList;

use BadMethodCallException;
use Phunkie\Cats\Applicative;
use Phunkie\Cats\Functor;
use Phunkie\Types\ImmList;
use Phunkie\Types\Kind;
use Phunkie\Types\Option;
use TypeError;

/**
 * @mixin ImmList
 */
trait ImmListApplicativeOps
{
    use ImmListFunctorOps;

    public function pure($a): Applicative
    {
        return ImmList($a);
    }

    public function apply(Kind $f): Kind { return match(true) {
        $f == None()          => None(),
        $f instanceof Option  => throw new TypeError(sprintf("`apply` takes List<callable>, Option<%s> given", gettype($f->get()))),
        $this == Nil()        => Nil(),
        $f instanceof ImmList => $this->map(fn($a) => $f->map(fn($ff) => $ff($a))->toArray()[0]),
        default               => throw new BadMethodCallException() };
    }

    public function map2(Kind $fb, callable $f): Kind
    {
        if (!$fb instanceof Functor) {
            throw new TypeError("Argument must be a functor");
        }
        return $this->apply($fb->map(fn ($b) => fn ($a) => $f($a, $b)));
    }
}
