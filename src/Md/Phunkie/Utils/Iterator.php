<?php

namespace Md\Phunkie\Utils;

use ArrayAccess;
use Countable;
use Md\Phunkie\Types\ImmInteger;
use Md\Phunkie\Types\ImmString;
use SplObjectStorage;

use function Md\Phunkie\Functions\type\promote;

class Iterator implements \Iterator, ArrayAccess, Countable
{
    private $storage;

    public function __construct(SplObjectStorage $storage)
    {
        $this->storage = $storage;
    }

    public function current()
    {
        return $this->storage->getInfo();
    }

    public function next()
    {
        $this->storage->next();
    }

    public function key()
    {
        if ($this->storage->current() instanceof ImmString ||
            $this->storage->current() instanceof ImmInteger) {
            return $this->storage->current()->get();
        }
        return $this->storage->current();
    }

    public function valid()
    {
        return $this->storage->valid();
    }

    public function rewind()
    {
        $this->storage->rewind();
    }

    public function offsetExists($offset)
    {
        foreach ($this->storage as $k) {
            if ($k == promote($offset)) {
                return true;
            }
        }
        return false;
    }

    public function offsetGet($offset)
    {
        foreach ($this->storage as $k) {
            if ($k == promote($offset)) {
                return Some($this->storage[$k]);
            }
        }
        return None();
    }

    public function offsetSet($offset, $value)
    {
        throw new \TypeError("Iterators are immutable");
    }

    public function offsetUnset($offset)
    {
        throw new \TypeError("Iterators are immutable");
    }

    public function count()
    {
        return $this->storage->count();
    }
}