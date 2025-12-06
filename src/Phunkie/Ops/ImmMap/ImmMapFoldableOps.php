<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\Ops\ImmMap;

use Phunkie\Types\Pair;
use function Phunkie\Functions\currying\applyPartially;
use function Phunkie\Functions\semigroup\combine;
use function Phunkie\Functions\semigroup\zero;

/**
 * Foldable operations for ImmMap.
 *
 * Provides methods to fold/reduce maps into single values.
 *
 * @mixin \Phunkie\Types\ImmMap
 */
trait ImmMapFoldableOps
{
    /**
     * Left-associative fold of the map.
     *
     * Folds the map from left to right (in key order), applying the function
     * to each key-value pair and accumulating a result.
     *
     * Example:
     * ```php
     * $map = ImmMap("a" => 1, "b" => 2, "c" => 3);
     * $map->foldLeft(0)(fn($acc, $pair) => $acc + $pair->_2); // 6
     * ```
     *
     * @template B
     * @param B $initial The initial value for the fold
     * @return callable Either a partially applied function or the result
     */
    public function foldLeft($initial)
    {
        return applyPartially([$initial], func_get_args(), function (callable $f) use ($initial) {
            $acc = $initial;
            foreach ($this->iterator() as $k => $v) {
                $acc = $f($acc, Pair($k, $v));
            }
            return $acc;
        });
    }

    /**
     * Right-associative fold of the map.
     *
     * Folds the map from right to left (in reverse key order), applying the function
     * to each key-value pair and accumulating a result.
     *
     * Example:
     * ```php
     * $map = ImmMap("a" => 1, "b" => 2, "c" => 3);
     * $map->foldRight(0)(fn($pair, $acc) => $acc + $pair->_2); // 6
     * ```
     *
     * @template B
     * @param B $initial The initial value for the fold
     * @return callable Either a partially applied function or the result
     */
    public function foldRight($initial)
    {
        return applyPartially([$initial], func_get_args(), function (callable $f) use ($initial) {
            $acc = $initial;
            $keys = array_reverse($this->keys());
            foreach ($keys as $k) {
                $v = $this->get($k)->get();
                $acc = $f(Pair($k, $v), $acc);
            }
            return $acc;
        });
    }

    /**
     * Maps each element to a Monoid and combines them.
     *
     * Applies a function to each key-value pair that returns a value of a monoid type,
     * then combines all the results using the monoid's combine operation.
     *
     * Example:
     * ```php
     * $map = ImmMap("a" => 1, "b" => 2, "c" => 3);
     * $map->foldMap(fn($pair) => ImmList($pair->_2)); // ImmList(1, 2, 3)
     * ```
     *
     * @template M
     * @param callable(Pair<K,V>):M $f Function to map each pair to a monoid
     * @return M The combined result
     */
    public function foldMap(callable $f)
    {
        if (count($this->keys()) === 0) {
            throw new \Error("Cannot foldMap on empty map - no zero value available");
        }

        $first = true;
        $result = null;

        foreach ($this->iterator() as $k => $v) {
            $mapped = $f(Pair($k, $v));
            if ($first) {
                $result = $mapped;
                $first = false;
            } else {
                $result = combine($result, $mapped);
            }
        }

        return $result;
    }

    /**
     * Folds the map using a binary operator.
     *
     * Similar to foldLeft but with a simpler signature. If the map is empty,
     * returns the initial value.
     *
     * Example:
     * ```php
     * $map = ImmMap("a" => 1, "b" => 2);
     * $map->fold(0)(fn($acc, $pair) => $acc + $pair->_2); // 3
     * ```
     *
     * @template B
     * @param B $initial The initial value
     * @return callable Either a partially applied function or the result
     */
    public function fold($initial)
    {
        return applyPartially([$initial], func_get_args(), function (callable $f) use ($initial) {
            if (count($this->keys()) === 0) {
                return $initial;
            }
            return $this->foldLeft($initial)($f);
        });
    }
}
