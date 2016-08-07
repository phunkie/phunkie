<?php

namespace Md\Phunkie\Types;

use Md\Phunkie\Cats\Applicative;
use Md\Phunkie\Cats\Show;
use function Md\Phunkie\Functions\show\get_value_to_show;
use Md\Phunkie\Ops\ImmList\ImmListApplicativeOps;
use Md\Phunkie\Ops\ImmList\ImmListEqOps;
use Md\Phunkie\Ops\ImmList\ImmListFoldableOps;
use Md\Phunkie\Ops\ImmList\ImmListMonadOps;
use Md\Phunkie\Ops\ImmList\ImmListMonoidOps;

abstract class ImmList implements Kind, Applicative
{
    use Show;
    use ImmListApplicativeOps,
        ImmListEqOps,
        ImmListMonadOps,
        ImmListFoldableOps,
        ImmListMonoidOps;

    const kind = "ImmList";
    private $values;
    final public function __construct()
    {
        switch (get_class($this)) {
            case NonEmptyList::class:
                if (func_num_args() == 0) {
                    throw new \Error("not enough arguments for constructor Nel");
                }
                $this->values = func_get_args();
                break;
            case Cons::class:
                if (func_num_args() != 2) {
                    throw new \Error((func_num_args() < 2 ? "not enough" : "too many") . " arguments for constructor List");
                }
                $head = func_get_arg(0);
                $tail = func_get_arg(1);
                if (!$tail instanceof ImmList) {
                    throw new \TypeError("type mismatch 2nd argument List: expected List, found " .
                                         ((gettype($tail) == "object") ? get_class($tail) : gettype($tail)));
                }
                $this->values = array_merge([$head], $tail->toArray());
                break;
            case Nil::class:
                if (func_num_args() > 0) {
                    throw new \Error("too many arguments for constructor Nil");
                }
                $this->values = [];
                break;
            default:
                throw new \TypeError("List cannot be extended outside namespace");
        }
    }

    public function isEmpty(): bool
    {
        return false;
    }

    public function toString(): string
    {
        return "List(". implode(",", $this->map(function($e) { return get_value_to_show($e); })->values) . ")";
    }

    /**
     * @param callable $condition
     * @return ImmList<T>
     */
    public function filter(callable $condition): ImmList
    {
        return ImmList(...array_filter($this->values, $condition));
    }

    public function __get($property)
    {
        switch($property) {
            case 'length': return count($this->values);
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

    public function toArray(): array { return $this->values; }

    /**
     * @param ImmList<T> $list
     * @return ImmList<Pair<T>>
     */
    public function zip(ImmList $list): ImmList
    {
        if ($this->length <= $list->length) {
            $other = $list->toArray();
            reset($other);
            return $this->map(function($x) use ($other) {
                $pair = Pair($x, current($other));
                next($other);
                return $pair;
            });
        }
        return $list->zip($this);
    }

    /**
     * @param int $index
     * @return ImmList<ImmList<T>>
     */
    public function splitAt(int $index): Pair { switch (true) {
        case $index == 0:                    return Pair(Nil(), clone $this);
        case $index >= count($this->values): return Pair(clone $this, Nil());
        default: return Pair(ImmList(...array_slice($this->values, 0, $index)),
            ImmList(...array_slice($this->values, $index))); }
    }

    public function partition(callable $condition): Pair
    {
        $trues = $falses = [];
        foreach ($this->values as $value) { switch ($result = call_user_func($condition, $value)) {
            case true: $trues[] = $value; break;
            case false: $falses[] = $value; break;
            default: throw $this->callableMustReturnBoolean($result); }
        }
        return Pair(ImmList(...$trues), ImmList(...$falses));
    }

    public function head()
    {
        return $this->values[0];
    }

    public function tail()
    {
        return ImmList(...array_slice($this->values,1));
    }

    public function init()
    {
        return ImmList(...array_slice($this->values,0,-1));
    }

    public function last()
    {
        return $this->values[count($this->values) - 1];
    }

    public function reverse()
    {
        return ImmList(...array_reverse($this->values));
    }

    private function callableMustReturnBoolean($result)
    {
        return new \BadMethodCallException(sprintf("partition must be passed a callable that returns a boolean, %s returned",
            gettype($result)));
    }

}