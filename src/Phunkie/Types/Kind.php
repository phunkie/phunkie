<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\Types;

/**
 * @mixin \Phunkie\Cats\Functor
 */
interface Kind
{
    public function getTypeArity(): int;
    public function getTypeVariables(): array;
}