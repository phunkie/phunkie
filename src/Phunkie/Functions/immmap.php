<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

 namespace {
    /**
     * Functions for working with immutable maps.
     * 
     * This module provides functions for creating and manipulating
     * immutable key-value maps. Core operations include:
     * - Creating maps from various input formats
     * - Mapping over values or key-value pairs
     * - Adding and removing entries
     * - Accessing keys and values
     * - Converting between maps and arrays
     * - Comparing maps for equality
     *
     * Most functions support both ImmMap and PHP arrays, preserving the input type
     * in the output. All functions are curried for functional composition.
     *
     * Example:
     * ```php
     * // Create maps
     * $map = ImmMap("a" => 1, "b" => 2);
     * $fromArray = fromArray(["a" => 1, "b" => 2]);
     * 
     * // Modify entries
     * $added = plus("c", 3)($map);      // ImmMap("a" => 1, "b" => 2, "c" => 3)
     * $removed = minus("b")($map);       // ImmMap("a" => 1)
     * 
     * // Transform values
     * $doubled = mapValues(fn($x) => $x * 2)($map);  // ImmMap("a" => 2, "b" => 4)
     * 
     * // Access contents
     * $hasKey = contains("a")($map);     // true
     * $allKeys = keys($map);             // ImmList("a", "b")
     * $allValues = values($map);         // ImmList(1, 2)
     * 
     * // Convert formats
     * $asArray = toArray($map);          // ["a" => 1, "b" => 2]
     * ```
     *
     * Creates an immutable map.
     * 
     * Creates an ImmMap from key-value pairs. Keys can be provided as:
     * - Alternating key, value arguments
     * - Array of key-value pairs
     * - Associative array
     *
     * Example:
     * ```php
     * // From key-value pairs
     * ImmMap("a", 1, "b", 2);        // ImmMap("a" => 1, "b" => 2)
     * 
     * // From array of pairs
     * ImmMap([["a", 1], ["b", 2]]);  // ImmMap("a" => 1, "b" => 2)
     * 
     * // From associative array
     * ImmMap(["a" => 1, "b" => 2]);  // ImmMap("a" => 1, "b" => 2)
     * 
     * // Empty map
     * ImmMap();                       // ImmMap()
     * ```
     * 
     * @see ImmMap The underlying immutable map type
     * @see mapValues For transforming values
     * @see mapKV For transforming with keys
     * @see plus For adding entries
     * @see minus For removing entries
     * @see contains For checking keys
     * @see keys For getting all keys
     * @see values For getting all values
     * @see fromArray For array conversion
     * @see toArray For map conversion
     * @see equals For comparing maps
     * @see ImmMap The underlying immutable map type
     * @see mapValues For transforming values
     * @see mapKV For transforming with keys
     * @see equals For comparing maps
     *
     * @template K,V
     * @param mixed ...$values Key-value pairs or array
     * @return ImmMap<K,V> The immutable map
     */
    function ImmMap(...$values)
    {
        return new \Phunkie\Types\ImmMap(...$values);
    }
 }

 namespace Phunkie\Functions\immmap {
    use Phunkie\Types\ImmMap;
    use Phunkie\Types\ImmList;
    use function Phunkie\Functions\currying\applyPartially;

    const mapValues = "\\Phunkie\\Functions\\immmap\\mapValues";
    /**
     * Maps over the values in a map.
     * 
     * Applies a function to each value in the map while preserving keys.
     * Returns a new map with transformed values.
     *
     * Example:
     * ```php
     * $double = fn($x) => $x * 2;
     * $map = ImmMap("a" => 1, "b" => 2);
     * mapValues($double)($map);  // ImmMap("a" => 2, "b" => 4)
     * ```
     *
     * @template K,A,B
     * @param callable(A):B $f Function to apply to values
     * @return callable(ImmMap<K,A>):ImmMap<K,B> Function expecting map
     */
    function mapValues(callable $f)
    {
        return applyPartially([$f], func_get_args(), fn(ImmMap $map) => $map->map($f));
    }

    /**
     * Maps over both keys and values in a map.
     * 
     * Applies a function to each key-value pair in the map.
     * Returns a new map with transformed entries.
     *
     * Example:
     * ```php
     * $addKeyToValue = fn($k, $v) => "$k:$v";
     * $map = ImmMap("a" => 1, "b" => 2);
     * mapKV($addKeyToValue)($map);  // ImmMap("a" => "a:1", "b" => "b:2")
     * ```
     *
     * @template K1,K2,A,B
     * @param callable(K1,A):B $f Function taking key and value
     * @return callable(ImmMap<K1,A>):ImmMap<K1,B> Function expecting map
     */
    const mapKV = "\\Phunkie\\Functions\\immmap\\mapKV";
    function mapKV(callable $f)
    {
        return applyPartially([$f], func_get_args(), fn(ImmMap $map) => $map->mapKV($f));
    }

    /**
     * Checks if two maps are equal.
     * 
     * Maps are equal if they have the same keys with equal values.
     * Uses the Eq typeclass for value comparison.
     *
     * Example:
     * ```php
     * $map1 = ImmMap("a" => 1, "b" => 2);
     * $map2 = ImmMap("a" => 1, "b" => 2);
     * $map3 = ImmMap("a" => 2, "b" => 1);
     * 
     * equals($map1)($map2);  // true
     * equals($map1)($map3);  // false
     * ```
     *
     * @template K,V
     * @param ImmMap<K,V> $m1 First map
     * @return callable(ImmMap<K,V>):bool Function expecting second map
     */
    const equals = "\\Phunkie\\Functions\\immmap\\equals";
    function equals(ImmMap $m1)
    {
        return applyPartially([$m1], func_get_args(), fn(ImmMap $m2) => $m1->eqv($m2));
    }

    /**
     * Adds a key-value pair to a map.
     * 
     * Returns a new map with the added key-value pair.
     * If the key exists, its value is replaced.
     *
     * Example:
     * ```php
     * $map = ImmMap("a" => 1);
     * plus("b", 2)($map);        // ImmMap("a" => 1, "b" => 2)
     * plus("a", 3)($map);        // ImmMap("a" => 3)
     * 
     * // Works with arrays too
     * plus("b", 2)(["a" => 1]);  // ["a" => 1, "b" => 2]
     * ```
     *
     * @template K,V
     * @param K $key Key to add
     * @param V $value Value to add
     * @return callable(ImmMap<K,V>|array<K,V>):ImmMap<K,V>|array<K,V> Function expecting map
     */
    const plus = "\\Phunkie\\Functions\\immmap\\plus";
    function plus($key, $value)
    {
        return applyPartially([$key, $value], func_get_args(), function($map) use ($key, $value) {
            if (is_array($map)) {
                return array_merge($map, [$key => $value]);
            }
            return $map->plus($key, $value);
        });
    }

    /**
     * Removes a key from a map.
     * 
     * Returns a new map without the specified key.
     * If the key doesn't exist, returns the original map.
     *
     * Example:
     * ```php
     * $map = ImmMap("a" => 1, "b" => 2);
     * minus("b")($map);        // ImmMap("a" => 1)
     * minus("c")($map);        // ImmMap("a" => 1, "b" => 2)
     * 
     * // Works with arrays too
     * minus("b")(["a" => 1, "b" => 2]);  // ["a" => 1]
     * ```
     *
     * @template K,V
     * @param K $key Key to remove
     * @return callable(ImmMap<K,V>|array<K,V>):ImmMap<K,V>|array<K,V> Function expecting map
     */
    const minus = "\\Phunkie\\Functions\\immmap\\minus";
    function minus($key)
    {
        return applyPartially([$key], func_get_args(), function($map) use ($key) {
            if (is_array($map)) {
                return array_diff_key($map, [$key => true]);
            }
            return $map->minus($key);
        });
    }

    /**
     * Checks if a map contains a key.
     * 
     * Returns true if the map contains the specified key.
     *
     * Example:
     * ```php
     * $map = ImmMap("a" => 1, "b" => 2);
     * contains("a")($map);        // true
     * contains("c")($map);        // false
     * 
     * // Works with arrays too
     * contains("a")(["a" => 1]);  // true
     * ```
     *
     * @template K,V
     * @param K $key Key to check
     * @return callable(ImmMap<K,V>|array<K,V>):bool Function expecting map
     */
    const contains = "\\Phunkie\\Functions\\immmap\\contains";
    function contains($key)
    {
        return applyPartially([$key], func_get_args(), function($map) use ($key) {
            if (is_array($map)) {
                return array_key_exists($key, $map);
            }
            return $map->contains($key);
        });
    }

    /**
     * Gets all keys from a map.
     * 
     * Returns an ImmList containing all keys in the map.
     *
     * Example:
     * ```php
     * $map = ImmMap("a" => 1, "b" => 2);
     * keys($map);        // ImmList("a", "b")
     * 
     * // Works with arrays too
     * keys(["a" => 1]);  // ImmList("a")
     * ```
     *
     * @template K,V
     * @param ImmMap<K,V>|array<K,V> $map Map to get keys from
     * @return ImmList<K> List of keys
     */
    function keys($map): ImmList
    {
        if (is_array($map)) {
            return ImmList(...array_keys($map));
        }
        return $map->keys();
    }

    /**
     * Gets all values from a map.
     * 
     * Returns an ImmList containing all values in the map.
     *
     * Example:
     * ```php
     * $map = ImmMap("a" => 1, "b" => 2);
     * values($map);        // ImmList(1, 2)
     * 
     * // Works with arrays too
     * values(["a" => 1]);  // ImmList(1)
     * ```
     *
     * @template K,V
     * @param ImmMap<K,V>|array<K,V> $map Map to get values from
     * @return ImmList<V> List of values
     */
    function values($map): ImmList
    {
        if (is_array($map)) {
            return ImmList(...array_values($map));
        }
        return $map->values();
    }

    /**
     * Creates a map from an array.
     * 
     * Converts an associative array to an ImmMap.
     *
     * Example:
     * ```php
     * fromArray(["a" => 1, "b" => 2]);  // ImmMap("a" => 1, "b" => 2)
     * fromArray([["a", 1], ["b", 2]]);  // ImmMap("a" => 1, "b" => 2)
     * ```
     *
     * @template K,V
     * @param array<K,V>|array<array{K,V}> $array Array to convert
     * @return ImmMap<K,V> Resulting map
     */
    function fromArray(array $array): ImmMap
    {
        return ImmMap(...$array);
    }

    /**
     * Converts a map to an array.
     * 
     * Returns an associative array containing the map's entries.
     *
     * Example:
     * ```php
     * $map = ImmMap("a" => 1, "b" => 2);
     * toArray($map);  // ["a" => 1, "b" => 2]
     * 
     * // Works with arrays too (returns copy)
     * toArray(["a" => 1]);  // ["a" => 1]
     * ```
     *
     * @template K,V
     * @param ImmMap<K,V>|array<K,V> $map Map to convert
     * @return array<K,V> Resulting array
     */
    function toArray($map): array
    {
        if (is_array($map)) {
            return $map;
        }
        return $map->toArray();
    }

}
