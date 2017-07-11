<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\Utils;

use Phunkie\Cats\Functor;
use Phunkie\Cats\Monad;

interface Traversable extends Functor, Monad
{
    public function filter(callable $filter);
    public function withFilter(callable $filter): WithFilter;
}