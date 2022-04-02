<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\Cats;

use Phunkie\Cats\Functor\Invariant;
use Phunkie\Types\Kind;

/**
 * Functor<F<A>>
 */
interface Functor extends Invariant
{
    /**
     * (A => B) => F<B>
     */
    public function map(callable $f): Kind;

    /**
     * (A => B) => (F<A> => F<B>)
     */
    public function lift($f): callable;

    /**
     * B => F<B>
     */
    public function as($b): Kind;

    /**
     * () => F<Unit>
     */
    public function void(): Kind;

    /**
     * (A => B) => F<Pair<A,B>>
     */
    public function zipWith($f): Kind;
}
