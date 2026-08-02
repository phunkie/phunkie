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

use ArrayAccess;
use Countable;
use Phunkie\Types\ImmInteger;
use Phunkie\Types\ImmString;
use SplObjectStorage;

use function Phunkie\Functions\type\promote;

/**
 * Immutable iterator implementation with array-like access.
 * 
 * Iterator wraps an SplObjectStorage to provide immutable iteration and array-like
 * access to a collection of objects. It handles special cases for ImmString and
 * ImmInteger keys, and returns Option values for safe element access.
 *
 * Example:
 * ```php
 * $storage = new SplObjectStorage();
 * $storage[$key] = $value;
 * $iterator = new Iterator($storage);
 * 
 * // Iterate over elements
 * foreach ($iterator as $key => $value) {
 *     // Keys are unwrapped if ImmString/ImmInteger
 *     // Values are direct from storage
 * }
 * 
 * // Array-like access returns Option
 * $maybeValue = $iterator[$key]; // Returns Some($value) or None
 * 
 * // Immutable - these throw TypeError
 * $iterator[$key] = $value;  // Error
 * unset($iterator[$key]);    // Error
 * ```
 *
 * @implements \Iterator<mixed,mixed>
 * @implements ArrayAccess<mixed,mixed>
 * @implements Countable
 */
class Iterator implements \Iterator, ArrayAccess, Countable
{
    /** @var SplObjectStorage The underlying storage */
    private $storage;

    /**
     * Creates a new iterator.
     *
     * @param SplObjectStorage $storage The storage to iterate over
     */
    public function __construct(SplObjectStorage $storage)
    {
        $this->storage = $storage;
    }

    /**
     * Gets the current element's value.
     *
     * @return mixed The storage info for current element
     */
    #[\ReturnTypeWillChange]
    public function current()
    {
        return $this->storage->getInfo();
    }

    /**
     * Moves to next element.
     */
    public function next(): void
    {
        $this->storage->next();
    }

    /**
     * Gets the current element's key.
     * Unwraps ImmString and ImmInteger keys to their primitive values.
     *
     * @return mixed The key (unwrapped if immutable type)
     */
    #[\ReturnTypeWillChange]
    public function key()
    {
        if ($this->storage->current() instanceof ImmString ||
            $this->storage->current() instanceof ImmInteger) {
            return $this->storage->current()->get();
        }
        return $this->storage->current();
    }

    /**
     * Checks if current position is valid.
     */
    public function valid(): bool
    {
        return $this->storage->valid();
    }

    /**
     * Rewinds iterator to start.
     */
    public function rewind(): void
    {
        $this->storage->rewind();
    }

    /**
     * Checks if offset exists.
     * Promotes offset to immutable type for comparison.
     *
     * @param mixed $offset The offset to check
     */
    public function offsetExists($offset): bool
    {
        foreach ($this->storage as $k) {
            if ($k == promote($offset)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Gets value at offset wrapped in Option.
     * Returns None if offset doesn't exist.
     *
     * @param mixed $offset The offset to get
     * @return Option The value wrapped in Some or None
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        foreach ($this->storage as $k) {
            if ($k == promote($offset)) {
                return Some($this->storage[$k]);
            }
        }
        return None();
    }

    /**
     * Disabled - iterator is immutable.
     * @throws \TypeError Always
     */
    public function offsetSet($offset, $value): void
    {
        throw new \TypeError("Iterators are immutable");
    }

    /**
     * Disabled - iterator is immutable.
     * @throws \TypeError Always
     */
    public function offsetUnset($offset): void
    {
        throw new \TypeError("Iterators are immutable");
    }

    /**
     * Gets number of elements in iterator.
     */
    public function count(): int
    {
        return $this->storage->count();
    }
}
