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

/**
 * Represents an empty immutable list.
 * 
 * Nil is a singleton representing an empty list. It provides specific
 * implementations for list operations when applied to an empty list.
 *
 * Example:
 * ```php
 * $nil = Nil();
 * $nil->isEmpty();  // true
 * $nil->length;     // 0
 * ```
 *
 * @template A
 * @extends ImmList<A>
 */
final class Nil extends ImmList
{
    /**
     * Provides access to list properties.
     *
     * @param string $property Property name
     * @return mixed Property value
     * @throws \Error if property doesn't exist
     */
    public function __get($property) { return match ($property) {
        'length' => 0,
        'head' => $this->head(),
        'tail' => $this->tail(),
        'init' => $this->init(),
        'last' => $this->last(),
        'default' => throw new \Error("value $property is not a member of ImmList")};
    }

    /**
     * Prevents modification of list properties.
     *
     * @param string $property Property name
     * @param mixed $unused Unused value
     * @throws \BadMethodCallException|Error Always throws as Nil is immutable
     */
    public function __set($property, $unused) { return match ($property) {
        'length' => throw new \BadMethodCallException("Can't change the value of members of a ImmList"),
        default => throw new \Error("value $property is not a member of ImmList")};
    }

    /**
     * Always returns true as Nil is empty by definition.
     *
     * @return bool true
     */
    public function isEmpty(): bool
    {
        return true;
    }

    /**
     * Attempts to get the head of an empty list.
     *
     * @throws NoSuchElementException Always throws as Nil has no head
     */
    public function head()
    {
        throw new NoSuchElementException("head of empty list");
    }

    /**
     * Attempts to get the tail of an empty list.
     *
     * @return ImmList<A> Never returns - always throws
     * @throws \BadMethodCallException Always throws as Nil has no tail
     */
    public function tail(): ImmList
    {
        throw new \BadMethodCallException("tail of empty list");
    }

    /**
     * Attempts to get all elements except the last one.
     *
     * @return ImmList<A> Never returns - always throws
     * @throws \BadMethodCallException Always throws as Nil has no elements
     */
    public function init(): ImmList
    {
        throw new \BadMethodCallException("empty init");
    }

    /**
     * Attempts to get the last element of an empty list.
     *
     * @throws NoSuchElementException Always throws as Nil has no last element
     */
    public function last()
    {
        throw new NoSuchElementException("last of empty list");
    }

    /**
     * Returns a reversed empty list.
     *
     * @return ImmList<A> Returns Nil as reversing empty list is still empty
     */
    public function reverse(): ImmList
    {
        return Nil();
    }

    /**
     * Converts the empty list to an array.
     *
     * @return array<A> Returns an empty array
     */
    public function toArray(): array
    {
        return [];
    }

    /**
     * Zips this empty list with another list.
     *
     * @param ImmList<B> $list The list to zip with
     * @return ImmList<Pair<A,B>> Returns Nil as zipping with empty list yields empty list
     * @template B
     */
    public function zip(ImmList $list): ImmList
    {
        return Nil();
    }

    /**
     * Splits an empty list at given index.
     *
     * @param int $index The index to split at (unused)
     * @return Pair<ImmList<A>,ImmList<A>> Returns Pair(Nil, Nil)
     */
    public function splitAt(int $index): Pair
    {
        return Pair(Nil(), Nil());
    }

    /**
     * Partitions an empty list based on a condition.
     *
     * @param callable $condition The partition condition (unused)
     * @return Pair<ImmList<A>,ImmList<A>> Returns Pair(Nil, Nil)
     */
    public function partition(callable $condition): Pair
    {
        return Pair(Nil(), Nil());
    }

    /**
     * Returns string representation of empty list.
     *
     * @return string Always returns "List()"
     */
    public function toString(): string
    {
        return "List()";
    }

    /**
     * Filters an empty list.
     *
     * @param callable $condition The filter condition (unused)
     * @return ImmList<A> Returns Nil as filtering empty list is still empty
     */
    public function filter(callable $condition): Traversable
    {
        return Nil();
    }
}
