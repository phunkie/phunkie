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

use Phunkie\Types\Kind;

interface Apply extends Functor
{
    /**
     * @param Kind<callable<A,B>> $f
     * @return Kind<B>
     */
    public function apply(Kind $f): Kind;

    /**
     * @param Kind<B> $fb
     * @param (A,B) => C $f
     * @return Kind<C>
     */
    public function map2(Kind $fb, callable $f): Kind;
}