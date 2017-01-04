<?php

namespace Md\Phunkie\Ops\ImmList;

use Exception;
use function Md\Phunkie\Functions\show\get_value_to_show;
use Md\Phunkie\Types\ImmList;
use Md\Phunkie\Types\Option;
use Md\Phunkie\Types\Pair;

trait ImmListOps
{
    abstract public function toArray(): array;

    public function __get($property)
    {
        switch($property) {
            case 'length': return count($this->toArray());
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
            case 'length':
            case 'head':
            case 'tail':
            case 'init':
            case 'last':
                throw new \Error("Can't change the value of members of a ImmList");
        }
        throw new \Error("value $property is not a member of ImmList");
    }

    public function head()
    {
        return $this->toArray()[0];
    }

    public function tail(): ImmList
    {
        return ImmList(...array_slice($this->toArray(),1));
    }

    public function init(): ImmList
    {
        return ImmList(...array_slice($this->toArray(),0,-1));
    }

    public function last()
    {
        return $this->toArray()[count($this->toArray()) - 1];
    }

    public function isEmpty(): bool
    {
        return false;
    }

    public function filter(callable $condition): ImmList
    {
        return ImmList(...array_filter($this->toArray(), $condition));
    }

    public function reject(callable $condition): ImmList
    {
        return ImmList(...array_filter($this->toArray(), function($x) use ($condition) {
            return !$condition($x);
        }));
    }

    public function nth(int $nth): Option
    {
        return array_key_exists($nth, $this->toArray()) ? Some($this->toArray()[$nth]) : None();
    }

    public function take(int $n): ImmList
    {
        return ImmList(...array_slice($this->toArray(), 0, $n < 0 ? 0 : $n));
    }

    public function drop(int $n): ImmList
    {
        return ImmList(...array_slice($this->toArray(), $n < 0 ? 0 : $n));
    }

    public function reverse(): ImmList
    {
        return ImmList(...array_reverse($this->toArray()));
    }

    public function mkString()
    {
        switch(func_num_args()) {
            case 1: return $this->mkStringOneArgument(func_get_arg(0));
            case 3: return $this->mkStringThreeArguments(func_get_arg(0), func_get_arg(1), func_get_arg(2));
            default: throw new \Error("wrong number of arguments for mkString, should be 1 or 3, " . func_num_args() . " given");
        }
    }

    private function mkStringOneArgument($glue)
    {
        return implode($glue, array_map(function($e) { return is_string($e) ? $e : get_value_to_show($e); }, $this->toArray()));
    }

    private function mkStringThreeArguments($start, $glue, $end)
    {
        return $start . $this->mkStringOneArgument($glue) . $end;
    }

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
        case $index >= count($this->toArray()): return Pair(clone $this, Nil());
        default: return Pair(ImmList(...array_slice($this->toArray(), 0, $index)),
            ImmList(...array_slice($this->toArray(), $index))); }
    }

    public function partition(callable $condition): Pair
    {
        $trues = $falses = [];
        foreach ($this->toArray() as $value) { switch ($result = call_user_func($condition, $value)) {
            case true: $trues[] = $value; break;
            case false: $falses[] = $value; break;
            default: throw $this->callableMustReturnBoolean($result); }
        }
        return Pair(ImmList(...$trues), ImmList(...$falses));
    }

    private function callableMustReturnBoolean($result): Exception
    {
        return new \BadMethodCallException(sprintf("partition must be passed a callable that returns a boolean, %s returned",
            gettype($result)));
    }
}