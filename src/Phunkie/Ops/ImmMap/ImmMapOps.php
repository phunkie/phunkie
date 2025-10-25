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

use Phunkie\Types\ImmList;
use Phunkie\Types\ImmMap;
use Phunkie\Types\Pair;
use SplObjectStorage;
use function Phunkie\Functions\type\promote;

/**
 * Additional utility operations for ImmMap.
 *
 * @mixin \Phunkie\Types\ImmMap
 */
trait ImmMapOps
{
    /**
     * Returns a new map with a key-value pair added or updated.
     *
     * Alias for plus() with better naming for immutable updates.
     *
     * Example:
     * ```php
     * $map = ImmMap("a" => 1, "b" => 2);
     * $map->updated("c", 3); // ImmMap("a" => 1, "b" => 2, "c" => 3)
     * $map->updated("a", 10); // ImmMap("a" => 10, "b" => 2)
     * ```
     *
     * @param K $key The key to update
     * @param V $value The value to set
     * @return ImmMap<K,V> A new map with the update
     */
    public function updated($key, $value): ImmMap
    {
        return $this->plus($key, $value);
    }

    /**
     * Returns a new map with a key removed.
     *
     * Alias for minus() with better naming for immutable removal.
     *
     * Example:
     * ```php
     * $map = ImmMap("a" => 1, "b" => 2);
     * $map->removed("a"); // ImmMap("b" => 2)
     * ```
     *
     * @param K $key The key to remove
     * @return ImmMap<K,V> A new map without the key
     */
    public function removed($key): ImmMap
    {
        return $this->minus($key);
    }

    /**
     * Converts the map to a list of key-value pairs.
     *
     * Example:
     * ```php
     * $map = ImmMap("a" => 1, "b" => 2);
     * $map->toList();
     * // ImmList(Pair("a", 1), Pair("b", 2))
     * ```
     *
     * @return ImmList<Pair<K,V>> List of pairs
     */
    public function toList(): ImmList
    {
        $pairs = [];
        foreach ($this->iterator() as $k => $v) {
            $pairs[] = Pair($k, $v);
        }
        return ImmList(...$pairs);
    }

    /**
     * Maps a function that receives both key and value.
     *
     * Unlike map() which receives a Pair, this passes key and value as separate arguments.
     *
     * Example:
     * ```php
     * $map = ImmMap("a" => 1, "b" => 2);
     * $map->mapWithKey(fn($k, $v) => "$k:$v");
     * // ImmMap("a" => "a:1", "b" => "b:2")
     * ```
     *
     * @template V2
     * @param callable(K,V):V2 $f Function receiving key and value
     * @return ImmMap<K,V2> Map with transformed values
     */
    public function mapWithKey(callable $f): ImmMap
    {
        $mappings = new SplObjectStorage();
        foreach ($this->copy()->iterator() as $k => $v) {
            $mappings[promote($k)] = $f($k, $v);
        }
        $map = new ImmMap();
        $map->values = $mappings;
        return $map;
    }

    /**
     * Zips two maps by keys into pairs of values.
     *
     * Only includes keys present in both maps.
     *
     * Example:
     * ```php
     * $map1 = ImmMap("a" => 1, "b" => 2, "c" => 3);
     * $map2 = ImmMap("a" => "x", "b" => "y", "d" => "z");
     * $map1->zip($map2);
     * // ImmMap("a" => Pair(1, "x"), "b" => Pair(2, "y"))
     * ```
     *
     * @template V2
     * @param ImmMap<K,V2> $other The map to zip with
     * @return ImmMap<K,Pair<V,V2>> Map of paired values
     */
    public function zip(ImmMap $other): ImmMap
    {
        $result = ImmMap();
        foreach ($this->iterator() as $k => $v) {
            if ($other->contains($k)) {
                $result = $result->plus($k, Pair($v, $other->get($k)->get()));
            }
        }
        return $result;
    }

    /**
     * Zips two maps by keys, combining values with a function.
     *
     * Only includes keys present in both maps.
     *
     * Example:
     * ```php
     * $map1 = ImmMap("a" => 1, "b" => 2);
     * $map2 = ImmMap("a" => 10, "b" => 20);
     * $map1->zipWith($map2, fn($v1, $v2) => $v1 + $v2);
     * // ImmMap("a" => 11, "b" => 22)
     * ```
     *
     * @template V2
     * @template V3
     * @param ImmMap<K,V2> $other The map to zip with
     * @param callable(V,V2):V3 $f Function to combine values
     * @return ImmMap<K,V3> Map of combined values
     */
    public function zipWithMap(ImmMap $other, callable $f): ImmMap
    {
        $result = ImmMap();
        foreach ($this->iterator() as $k => $v) {
            if ($other->contains($k)) {
                $result = $result->plus($k, $f($v, $other->get($k)->get()));
            }
        }
        return $result;
    }

    /**
     * Partitions the map into two maps based on a predicate.
     *
     * Returns a Pair where the first element contains entries satisfying the predicate,
     * and the second element contains entries that don't.
     *
     * Example:
     * ```php
     * $map = ImmMap("a" => 1, "b" => 2, "c" => 3);
     * $map->partition(fn($pair) => $pair->_2 % 2 === 0);
     * // Pair(ImmMap("b" => 2), ImmMap("a" => 1, "c" => 3))
     * ```
     *
     * @param callable(Pair<K,V>):bool $predicate Function to test each pair
     * @return Pair<ImmMap<K,V>,ImmMap<K,V>> Pair of (matching, non-matching) maps
     */
    public function partition(callable $predicate): Pair
    {
        $trues = ImmMap();
        $falses = ImmMap();

        foreach ($this->iterator() as $k => $v) {
            if ($predicate(Pair($k, $v))) {
                $trues = $trues->plus($k, $v);
            } else {
                $falses = $falses->plus($k, $v);
            }
        }

        return Pair($trues, $falses);
    }

    /**
     * Groups map entries by a computed key.
     *
     * Applies a function to each key-value pair to compute a grouping key,
     * then creates a map of lists grouped by those keys.
     *
     * Example:
     * ```php
     * $map = ImmMap("a" => 1, "b" => 2, "c" => 1);
     * $map->groupBy(fn($pair) => $pair->_2);
     * // ImmMap(1 => ImmList(Pair("a", 1), Pair("c", 1)),
     * //        2 => ImmList(Pair("b", 2)))
     * ```
     *
     * @template K2
     * @param callable(Pair<K,V>):K2 $f Function to compute grouping key
     * @return ImmMap<K2,ImmList<Pair<K,V>>> Map of grouped entries
     */
    public function groupBy(callable $f): ImmMap
    {
        $groups = [];

        foreach ($this->iterator() as $k => $v) {
            $groupKey = $f(Pair($k, $v));
            $promoted = promote($groupKey);

            if (!isset($groups[$promoted instanceof \Phunkie\Types\ImmString || $promoted instanceof \Phunkie\Types\ImmInteger ? $promoted->get() : (string)$promoted])) {
                $groups[$promoted instanceof \Phunkie\Types\ImmString || $promoted instanceof \Phunkie\Types\ImmInteger ? $promoted->get() : (string)$promoted] = [];
            }

            $groups[$promoted instanceof \Phunkie\Types\ImmString || $promoted instanceof \Phunkie\Types\ImmInteger ? $promoted->get() : (string)$promoted][] = Pair($k, $v);
        }

        $result = ImmMap();
        foreach ($groups as $groupKey => $items) {
            $result = $result->plus($groupKey, ImmList(...$items));
        }

        return $result;
    }

    /**
     * Checks if the map is empty.
     *
     * Example:
     * ```php
     * ImmMap()->isEmpty(); // true
     * ImmMap("a" => 1)->isEmpty(); // false
     * ```
     *
     * @return bool True if the map has no entries
     */
    public function isEmpty(): bool
    {
        return count($this->keys()) === 0;
    }

    /**
     * Returns the number of entries in the map.
     *
     * Example:
     * ```php
     * ImmMap("a" => 1, "b" => 2)->size(); // 2
     * ```
     *
     * @return int The number of entries
     */
    public function size(): int
    {
        return count($this->keys());
    }
}
