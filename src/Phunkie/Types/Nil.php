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

use Phunkie\Types\ImmList\NoSuchElementException;
use Phunkie\Utils\Traversable;

final class Nil extends ImmList
{
    public function __get($property) { return match ($property) {
        'length' => 0,
        'head' => $this->head(),
        'tail' => $this->tail(),
        'init' => $this->init(),
        'last' => $this->last(),
        'default' => throw new \Error("value $property is not a member of ImmList")};
    }

    public function __set($property, $unused) { return match ($property) {
        'length' => throw new \BadMethodCallException("Can't change the value of members of a ImmList"),
        default => throw new \Error("value $property is not a member of ImmList")};
    }

    public function isEmpty(): bool
    {
        return true;
    }

    public function head()
    {
        throw new NoSuchElementException("head of empty list");
    }

    public function tail(): ImmList
    {
        throw new \BadMethodCallException("tail of empty list");
    }

    public function init(): ImmList
    {
        throw new \BadMethodCallException("empty init");
    }

    public function last()
    {
        throw new NoSuchElementException("last of empty list");
    }

    public function reverse(): ImmList
    {
        return Nil();
    }

    public function toArray(): array
    {
        return [];
    }

    public function zip(ImmList $list): ImmList
    {
        return Nil();
    }

    public function splitAt(int $index): Pair
    {
        return Pair(Nil(), Nil());
    }

    public function partition(callable $condition): Pair
    {
        return Pair(Nil(), Nil());
    }

    public function toString(): string
    {
        return "List()";
    }

    /**
     * @param callable $condition
     * @return Traversable|ImmList
     */
    public function filter(callable $condition): Traversable
    {
        return Nil();
    }
}
