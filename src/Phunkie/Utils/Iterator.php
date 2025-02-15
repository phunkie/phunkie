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

class Iterator implements \Iterator, ArrayAccess, Countable
{
    private $storage;

    public function __construct(SplObjectStorage $storage)
    {
        $this->storage = $storage;
    }

    #[\ReturnTypeWillChange]
    public function current()
    {
        return $this->storage->getInfo();
    }

    public function next(): void
    {
        $this->storage->next();
    }

    #[\ReturnTypeWillChange]
    public function key()
    {
        if ($this->storage->current() instanceof ImmString ||
            $this->storage->current() instanceof ImmInteger) {
            return $this->storage->current()->get();
        }
        return $this->storage->current();
    }

    public function valid(): bool
    {
        return $this->storage->valid();
    }

    public function rewind(): void
    {
        $this->storage->rewind();
    }

    public function offsetExists($offset): bool
    {
        foreach ($this->storage as $k) {
            if ($k == promote($offset)) {
                return true;
            }
        }
        return false;
    }

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

    public function offsetSet($offset, $value): void
    {
        throw new \TypeError("Iterators are immutable");
    }

    public function offsetUnset($offset): void
    {
        throw new \TypeError("Iterators are immutable");
    }

    public function count(): int
    {
        return $this->storage->count();
    }
}
