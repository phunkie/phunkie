<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\Ops\ImmList;

use BadMethodCallException;
use Error;
use function Phunkie\Functions\assertion\assertSameTypeAsCollectionType;
use function Phunkie\Functions\show\showArrayType;
use Phunkie\PatternMatching\Match;
use Phunkie\Types\ImmList;
use Phunkie\Types\Option;
use Phunkie\Types\Pair;
use Exception;

use function Phunkie\Functions\show\showType;
use function Phunkie\Functions\show\showValue;
use function Phunkie\PatternMatching\Referenced\ListNoTail;
use function Phunkie\PatternMatching\Referenced\ListWithTail;
use function \Phunkie\PatternMatching\Referenced\Failure as Invalid;
use function \Phunkie\PatternMatching\Referenced\Success as Valid;

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
        throw new Error("value $property is not a member of ImmList");
    }

    public function __set($property, $unused)
    {
        switch($property) {
            case 'length':
            case 'head':
            case 'tail':
            case 'init':
            case 'last':
                throw new Error("Can't change the value of members of a ImmList");
        }
        throw new Error("value $property is not a member of ImmList");
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

    public function reduce(callable $f)
    {
        /** @var $xs ImmList */
        $xs = null;
        /** @var \Exception $e */
        $e = null;

        $on = match($this); switch(true) {
            case $on(Nil): throw $this->cantReduceEmptyList();
            case $on(ListNoTail($x, Nil)): return $x;
            default: $on(ListWithTail($x, $xs));
                $result = $f($x, $xs->reduce($f));
                $when = $this->isSameTypeAsList($result);
                switch(true) {
                    case      $when(Invalid($e)): throw $e;
                    default : $when(Valid($x)); return $x;
                }
        }
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

    public function mkString(): string
    {
        switch(func_num_args()) {
            case 1: return $this->mkStringOneArgument(func_get_arg(0));
            case 3: return $this->mkStringThreeArguments(func_get_arg(0), func_get_arg(1), func_get_arg(2));
            default: throw new Error("wrong number of arguments for mkString, should be 1 or 3, " . func_num_args() . " given");
        }
    }

    private function mkStringOneArgument($glue): string
    {
        return implode($glue, array_map(function($e) { return is_string($e) ? $e : showValue($e); }, $this->toArray()));
    }

    private function mkStringThreeArguments($start, $glue, $end): string
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
        return new BadMethodCallException(sprintf("partition must be passed a callable that returns a boolean, " .
            "%s returned", gettype($result)));
    }

    private function callableMustReturnSameType($result): string
    {
        return "callable must return the same type as list type variable." . PHP_EOL .
            "Callable returns a " . (showType($result)) .
            ", and this is a list of " . showArrayType($this->toArray());
    }

    private function isSameTypeAsList($result): Match
    {
        return match(assertSameTypeAsCollectionType($result, $this->toArray(),
            $this->callableMustReturnSameType($result)));
    }

    private function cantReduceEmptyList(): Error
    {
        return new Error("can't apply reduce on an empty list");
    }
}