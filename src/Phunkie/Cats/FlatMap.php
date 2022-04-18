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

interface FlatMap extends Functor
{
    /**
     * (A => F<B>) => F(B)
     */
    public function flatMap(callable $f): Kind;
}
