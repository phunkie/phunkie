<?php

namespace Md\Phunkie\Types;

use Md\Phunkie\Types\ImmList\NoSuchElementException;

final class Nil extends ImmList
{
    public function __get($property)
    {
        switch($property) {
            case 'length': return 0;
            case 'head': return $this->head();
            case 'tail': return $this->tail();
            case 'init': return $this->init();
            case 'last': return $this->last();
        }
        throw new \Error("value $property is not a member of ImmList");
    }

    public function __set($property, $unused)
    {
        switch($property) {
            case 'length': throw new \BadMethodCallException("Can't change the value of members of a ImmList");
        }
        throw new \Error("value $property is not a member of ImmList");
    }

    public function isEmpty(): bool
    {
        return true;
    }

    public function head()
    {
        throw new NoSuchElementException("head of empty list");
    }

    public function tail()
    {
        throw new \BadMethodCallException("tail of empty list");
    }

    public function init()
    {
        throw new \BadMethodCallException("empty init");
    }

    public function last()
    {
        throw new NoSuchElementException("last of empty list");
    }

    public function reverse()
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

    function toString(): string
    {
        return "List()";
    }

    public function filter(callable $condition): ImmList
    {
        return Nil();
    }
}