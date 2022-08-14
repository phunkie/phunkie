<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\Ops\Function1;

use Phunkie\Cats\Applicative;
use Phunkie\Types\Function1;
use Phunkie\Types\Kind;

trait Function1ApplicativeOps
{
    use Function1FunctorOps;

    public function pure($a): Applicative
    {
        return Function1($a);
    }

    /**
     * Function1<A,B> $this
     *
     * @param Function1<A, Function1<B,C>> $f
     * @return Function1<A,C>
     */
    public function apply(Kind $f): Kind
    {
        switch ($f) {
            case $f == None(): return None();
            case $f instanceof Function1: return Function1(fn ($x) => $f->invokeFunctionOnArg($this->invokeFunctionOnArg($x)));
            default: throw new \BadMethodCallException();
        }
    }

    public function map2(Kind $fb, callable $f): Kind
    {
        return $this->apply($fb->map(fn ($b) => fn ($a) => $f($a, $b)));
    }
}
