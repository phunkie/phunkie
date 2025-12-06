<?php

namespace Phunkie\Ops\ImmMap;

use Phunkie\Ops\FunctorOps;
use Phunkie\Types\ImmMap;
use Phunkie\Types\Kind;
use Phunkie\Types\Pair;
use SplObjectStorage;
use function Phunkie\Functions\tuple\assign;
use function Phunkie\Functions\type\promote;

/**
 * Functor operations for ImmMap.
 *
 * Provides methods to transform maps while preserving structure.
 *
 * @mixin \Phunkie\Types\ImmMap
 */
trait ImmMapFunctorOps
{
    use FunctorOps;

    /**
     * Maps a function over key-value pairs.
     *
     * Applies a function to each key-value pair (as Pair), allowing transformation
     * of both keys and values.
     *
     * Example:
     * ```php
     * $map = ImmMap("a" => 1, "b" => 2);
     * $map->map(fn($pair) => Pair($pair->_1, $pair->_2 * 2));
     * // ImmMap("a" => 2, "b" => 4)
     * ```
     *
     * @param callable(Pair<K,V>):Pair<K2,V2> $f Function to transform pairs
     * @return Kind The transformed map
     */
    public function map(callable $f): Kind
    {
        $mappings = new SplObjectStorage();
        $key = $value = null;
        foreach ($this->copy()->iterator() as $k => $v) {
            (assign($key, $value))($f(Pair($k, $v)));
            $mappings[promote($key)] = $value;
        }
        $map = new ImmMap();
        $map->values = $mappings;
        return $map;
    }

    /**
     * Invariant map (same as map for ImmMap).
     *
     * @param callable $f Forward transformation
     * @param callable $g Backward transformation (unused)
     * @return Kind The transformed map
     */
    public function imap(callable $f, callable $g): Kind
    {
        return $this->map($f);
    }

    /**
     * Replaces values in the map.
     *
     * If the first element of the pair is _, replaces all values with the second element.
     * Otherwise, creates a map with a single key-value pair.
     *
     * Example:
     * ```php
     * $map = ImmMap("a" => 1, "b" => 2);
     * $map->as(Pair(_, 0)); // ImmMap("a" => 0, "b" => 0)
     * $map->as(Pair("x", 5)); // ImmMap("x" => 5)
     * ```
     *
     * @param Pair<K,V> $b The replacement pair
     * @return Kind The resulting map
     */
    public function as($b): Kind
    {
        if ($b->_1 === _) {
            return $this->map(fn (Pair $keyValue) => Pair($keyValue->_1, $b->_2));
        }
        return ImmMap($b->_1, $b->_2);
    }

    /**
     * Replaces all values with Unit.
     *
     * Example:
     * ```php
     * $map = ImmMap("a" => 1, "b" => 2);
     * $map->void(); // ImmMap("a" => Unit(), "b" => Unit())
     * ```
     *
     * @return Kind Map with Unit values
     */
    public function void(): Kind
    {
        return $this->map(fn (Pair $keyValue) => Pair($keyValue->_1, Unit()));
    }

    /**
     * Zips values with their transformation.
     *
     * Creates pairs of original and transformed values.
     *
     * Example:
     * ```php
     * $map = ImmMap("a" => 1, "b" => 2);
     * $map->zipWith(fn($v) => $v * 2);
     * // ImmMap("a" => Pair(1, 2), "b" => Pair(2, 4))
     * ```
     *
     * @param callable(V):V2 $f Transformation function
     * @return Kind Map with paired values
     */
    public function zipWith($f): Kind
    {
        return $this->map(fn (Pair $keyValue) => Pair($keyValue->_1, Pair($keyValue->_2, $f($keyValue->_2))));
    }

    /**
     * Maps a function over values only, preserving keys.
     *
     * Example:
     * ```php
     * $map = ImmMap("a" => 1, "b" => 2);
     * $map->mapValues(fn($v) => $v * 2);
     * // ImmMap("a" => 2, "b" => 4)
     * ```
     *
     * @template V2
     * @param callable(V):V2 $f Function to transform values
     * @return ImmMap<K,V2> Map with transformed values
     */
    public function mapValues(callable $f): ImmMap
    {
        $mappings = new SplObjectStorage();
        foreach ($this->copy()->iterator() as $k => $v) {
            $mappings[promote($k)] = $f($v);
        }
        $map = new ImmMap();
        $map->values = $mappings;
        return $map;
    }

    /**
     * Maps a function over keys only, preserving values.
     *
     * Example:
     * ```php
     * $map = ImmMap(1 => "a", 2 => "b");
     * $map->mapKeys(fn($k) => "key_" . $k);
     * // ImmMap("key_1" => "a", "key_2" => "b")
     * ```
     *
     * @template K2
     * @param callable(K):K2 $f Function to transform keys
     * @return ImmMap<K2,V> Map with transformed keys
     */
    public function mapKeys(callable $f): ImmMap
    {
        $mappings = new SplObjectStorage();
        foreach ($this->copy()->iterator() as $k => $v) {
            $newKey = $f($k);
            $mappings[promote($newKey)] = $v;
        }
        $map = new ImmMap();
        $map->values = $mappings;
        return $map;
    }
}
