<?php

namespace Md\Phunkie\Types;

use Md\Phunkie\Cats\Applicative;
use Md\Phunkie\Cats\Monad;
use Md\Phunkie\Cats\Show;
use function Md\Phunkie\Functions\show\get_value_to_show;
use Md\Phunkie\Ops\ImmList\ImmListApplicativeOps;
use Md\Phunkie\Ops\ImmList\ImmListEqOps;
use Md\Phunkie\Ops\ImmList\ImmListFoldableOps;
use Md\Phunkie\Ops\ImmList\ImmListMonadOps;
use Md\Phunkie\Ops\ImmList\ImmListMonoidOps;

abstract class ImmList implements Kind, Applicative, Monad
{
    use Show;
    use ImmListApplicativeOps,
        ImmListEqOps,
        ImmListMonadOps,
        ImmListFoldableOps,
        ImmListMonoidOps;

    const kind = ImmList;
    private $values;
    final public function __construct() { switch (get_class($this)) {
        case NonEmptyList::class: $this->constructNonEmptyList(func_num_args(), func_get_args()); break;
        case Cons::class: $this->constructCons(func_num_args(), func_get_args()); break;
        case Nil::class: $this->constructNil(func_num_args()); break;
        default: throw $this->listIsSealed(); }
    }

    public function isEmpty(): bool
    {
        return false;
    }

    public function toString(): string
    {
        return "List(". implode(", ", $this->map(function($e) { return get_value_to_show($e); })->values) . ")";
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
     * @param ImmList<B> $list
     * @return ImmList<Pair<A,B>>
     */
    public function zip(ImmList $list): ImmList
    {
        if ($this->length <= $list->length) {
            $other = $list->toArray();
            reset($other);
            return $this->map(function($x) use (&$other) {
                $pair = Pair($x, current($other));
                next($other);
                return $pair;
            });
        }
        return $list->zip($this);
    }

    /**
     * @param int $index
     * @return Pair<ImmList<A>,ImmList<A>>
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

    public function mkString()
    {
        switch(func_num_args()) {
            case 1: return $this->mkStringOneArgument(func_get_arg(0));
            case 3: return $this->mkStringThreeArguments(func_get_arg(0), func_get_arg(1), func_get_arg(2));
            default: throw new \Error("wrong number of arguments for mkString, should be 1 or 3, " . func_num_args() . " given");
        }
    }

    private function callableMustReturnBoolean($result)
    {
        return new \BadMethodCallException(sprintf("partition must be passed a callable that returns a boolean, %s returned",
            gettype($result)));
    }

    private function constructNonEmptyList(int $argc, array $argv)
    {
        if ($argc == 0) {
            throw new \Error("not enough arguments for constructor Nel");
        }
        $this->values = $argv;
    }

    private function constructCons(int $argc, array $argv)
    {
        if ($argc != 2) {
            throw new \Error(($argc < 2 ? "not enough" : "too many") . " arguments for constructor List");
        }
        $head = $argv[0];
        $tail = $argv[1];
        if (!$tail instanceof ImmList) {
            throw new \TypeError("type mismatch 2nd argument List: expected List, found " .
                ((gettype($tail) == "object") ? get_class($tail) : gettype($tail)));
        }
        $this->values = array_merge([$head], $tail->toArray());
    }

    private function constructNil(int $argc)
    {
        if ($argc > 0) {
            throw new \Error("too many arguments for constructor Nil");
        }
        $this->values = [];
    }

    private function listIsSealed()
    {
        return new \TypeError("List cannot be extended outside namespace");
    }

    private function mkStringOneArgument($glue)
    {
        return implode($glue, array_map(function($e) { return get_value_to_show($e); }, $this->values));
    }

    private function mkStringThreeArguments($start, $glue, $end)
    {
        return $start . $this->mkStringOneArgument($glue) . $end;
    }
}