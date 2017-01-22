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

interface Foldable
{
    public function foldLeft($initial, callable $f);
    public function foldRight($initial, callable $f);
    public function foldMap(callable $f);
    public function fold();
}