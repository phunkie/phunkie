<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\Ops;

use Phunkie\Types\Kind;

trait FunctorOps
{
    /**
     * (A => B) => (F<A> => F<B>)
     */
    public function lift($f): callable
    {
        return fn ($fa) => $fa->map($f);
    }

    /**
     * B => F<B>
     */
    public function as($b): Kind
    {
        return $this->map(fn ($ignored) => $b);
    }

    /**
     * () => F<Unit>
     */
    public function void(): Kind
    {
        return $this->map(fn ($ignored) => Unit());
    }

    /**
     * (A => B) => F<Pair<A,B>>
     */
    public function zipWith($f): Kind
    {
        return $this->map(fn ($a) => Pair($a, $f($a)));
    }
}
