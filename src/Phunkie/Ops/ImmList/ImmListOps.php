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
use Phunkie\PatternMatching\PMatch;
use Phunkie\Types\ImmList;
use Phunkie\Types\Option;
use Phunkie\Types\Pair;
use Exception;
use Phunkie\Utils\Traversable;
use Phunkie\Utils\WithFilter;
use function Phunkie\Functions\assertion\assertSameTypeAsCollectionType;

use function Phunkie\Functions\immlist\concat;
use function Phunkie\Functions\show\showArrayType;
use function Phunkie\Functions\show\showType;
use function Phunkie\Functions\show\showValue;
use function Phunkie\PatternMatching\Referenced\ListNoTail;
use function Phunkie\PatternMatching\Referenced\ListWithTail;
use function Phunkie\PatternMatching\Referenced\Failure as Invalid;
use function Phunkie\PatternMatching\Referenced\Success as Valid;

/**
 * @mixin ImmList
 * @property int $length
 * @property mixed $head
 * @property ImmList $tail
 * @property ImmList $init
 * @property mixed $last
 */
trait ImmListOps
{
    abstract public function toArray(): array;

    public function __get($property) { return match ($property) {
        'length' => count($this->toArray()),
        'head' => $this->head(),
        'tail' => $this->tail(),
        'init' => $this->init(),
        'last' => $this->last(),
        default => throw new Error("value $property is not a member of ImmList") };
    }

    public function __set($property, $unused) { return match ($property) {
            'length', 'head', 'tail', 'init', 'last'
                => throw new Error("Can't change the value of members of a ImmList"),
             default => throw new Error("value $property is not a member of ImmList") };
    }

    public function append(mixed $element): ImmList
    {
        return ImmList(...array_merge($this->toArray(), [$element]));
    }

    public function prepend(mixed $element): ImmList
    {
        return ImmList(...array_merge([$element], $this->toArray()));
    }

    public function head()
    {
        return $this->toArray()[0];
    }

    public function tail(): ImmList
    {
        return ImmList(...array_slice($this->toArray(), 1));
    }

    public function init(): ImmList
    {
        return ImmList(...array_slice($this->toArray(), 0, -1));
    }

    public function last()
    {
        return $this->toArray()[count($this->toArray()) - 1];
    }

    public function isEmpty(): bool
    {
        return false;
    }

    /**
     * @param callable $condition
     * @return Traversable|ImmList
     */
    public function filter(callable $condition): Traversable
    {
        return ImmList(...array_filter($this->toArray(), $condition));
    }

    public function withFilter(callable $filter): WithFilter
    {
        return new WithFilter($this, $filter);
    }

    public function withEach(callable $block)
    {
        foreach ($this->toArray() as $item) {
            $block($item);
        }
    }

    public function reject(callable $condition): ImmList
    {
        return ImmList(...array_filter($this->toArray(), fn ($x) => !$condition($x)));
    }

    public function reduce(callable $f)
    {
        /** @var $xs ImmList */
        $xs = null;
        /** @var \Exception $e */
        $e = null;

        $reduceListWithTail = function($f, $x, $xs) {
            $result = $f($x, $xs->reduce($f));
            $when = $this->isSameTypeAsList($result);
            return match (true) {
                $when(Invalid($e)) => throw $e,
                $when(Valid($x)) => $x
            };
        };

        $on = pmatch($this);
        return match (true) {
            $on(Nil) => throw $this->cantReduceEmptyList(),
            $on(ListNoTail($x, Nil)) => $x,
            $on(ListWithTail($x, $xs)) => $reduceListWithTail($f, $x, $xs)
        };
    }

    public function nth(int $nth): Option
    {
        return array_key_exists($nth, $this->toArray()) ? Some($this->toArray()[$nth]) : None();
    }

    public function take(int $n): ImmList
    {
        return ImmList(...array_slice($this->toArray(), 0, $n < 0 ? 0 : $n));
    }

    public function takeWhile(callable $f): ImmList
    {
        $loop = function (ImmList $list, ImmList $acc) use (&$loop, $f): ImmList {
            if (!$list->isEmpty() && $f($list->head()) === true) {
                return $loop($list->tail(), concat($acc, $list->head()));
            }
            return $acc;
        };
        return $loop($this, ImmList());
    }

    public function drop(int $n): ImmList
    {
        return ImmList(...array_slice($this->toArray(), $n < 0 ? 0 : $n));
    }

    public function dropWhile(callable $f): ImmList
    {
        $loop = function (ImmList $list) use (&$loop, $f): ImmList {
            if ($list->isEmpty() || $f($list->head()) === false) {
                return $list;
            }
            return $loop($list->tail());
        };
        return $loop($this);
    }

    public function reverse(): ImmList
    {
        return ImmList(...array_reverse($this->toArray()));
    }

    public function mkString(): string { return match (func_num_args()) {
        1 => $this->mkStringOneArgument(func_get_arg(0)),
        3 => $this->mkStringThreeArguments(func_get_arg(0), func_get_arg(1), func_get_arg(2)),
        default => throw new Error("wrong number of arguments for mkString, should be 1 or 3, " . func_num_args() . " given") };
    }

    public function transpose()
    {
        $new = [];
        foreach ($this->head->toArray() as $i => $values) {
            $new[] = $this->map(fn (ImmList $list) => $list->nth($i)->get());
        }
        return ImmList(...$new);
    }

    private function mkStringOneArgument($glue): string
    {
        return implode($glue, array_map(fn ($e) => is_string($e) ? $e : showValue($e), $this->toArray()));
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
            return $this->map(function ($x) use (&$other) {
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
    public function splitAt(int $index): Pair { return match (true) {
        $index == 0 => Pair(Nil(), clone $this),
        $index >= count($this->toArray()) => Pair(clone $this, Nil()),
        default => Pair(
            ImmList(...array_slice($this->toArray(), 0, $index)),
            ImmList(...array_slice($this->toArray(), $index))
        ) };
    }

    public function partition(callable $condition): Pair
    {
        $truesAndFalses = Pair([], []);
        foreach ($this->toArray() as $value) {
            $truesAndFalses = match ($result = call_user_func($condition, $value)) {
                true => $truesAndFalses(array_merge($truesAndFalses->_1, $value), $truesAndFalses->_2),
                false => $truesAndFalses($truesAndFalses->_1, array_merge($truesAndFalses->_2, $value)),
                default => throw $this->callableMustReturnBoolean($result) };
        }
        return Pair(ImmList(...$truesAndFalses->_1), ImmList(...$truesAndFalses->_2));
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

    private function isSameTypeAsList($result): PMatch
    {
        return pmatch(assertSameTypeAsCollectionType(
            $result,
            $this->toArray(),
            $this->callableMustReturnSameType($result)
        ));
    }

    private function cantReduceEmptyList(): Error
    {
        return new Error("can't apply reduce on an empty list");
    }
}
