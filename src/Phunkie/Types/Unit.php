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

use Error;
use Phunkie\Cats\Show;

final class Unit extends Tuple
{
    use Show;

    public function toString(): string
    {
        return '()';
    }

    public function showType(): string
    {
        return 'Unit';
    }

    public function __get($member)
    {
        throw new Error("$member is not a member of Unit");
    }

    public function __set($member, $value): void
    {
        throw new Error("$member is not a member of Unit");
    }

    public function copy(array $fields): Tuple
    {
        throw new Error("copy is not a member of Unit");
    }
}
