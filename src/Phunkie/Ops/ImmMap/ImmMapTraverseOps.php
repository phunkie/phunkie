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

use Phunkie\Types\ImmMap;
use Phunkie\Types\Kind;
use Phunkie\Types\Pair;
use function Phunkie\Functions\show\showArrayType;

/**
 * Traversable operations for ImmMap.
 *
 * Provides methods to traverse and filter maps.
 *
 * @mixin \Phunkie\Types\ImmMap
 */
trait ImmMapTraverseOps
{
    /**
     * Maps each element to an effect and collects the results.
     *
     * Applies a function that returns an effect (Option, Either, etc.) to each
     * key-value pair, then sequences all effects into a single effect containing
     * the map of results.
     *
     * Example:
     * ```php
     * $map = ImmMap("a" => 1, "b" => 2);
     * $map->traverse(fn($pair) => Some($pair->_2 * 2));
     * // Some(ImmMap("a" => 2, "b" => 4))
     * ```
     *
     * @template F of Kind
     * @param callable(Pair<K,V>):F $f Function returning an effect
     * @return Kind An effect containing the map of results
     */
    public function traverse(callable $f): Kind
    {
        $results = [];
        foreach ($this->iterator() as $k => $v) {
            $results[] = $f(Pair($k, $v));
        }

        // Map the results and sequence them
        $mapped = ImmMap();
        foreach ($this->keys() as $idx => $k) {
            $mapped = $mapped->plus($k, $results[$idx]);
        }

        return $mapped->sequence();
    }

    /**
     * Sequences effects in the map values into a single effect.
     *
     * If the map contains values that are effects (like Option or Either),
     * this combines them into a single effect containing a map of the unwrapped values.
     *
     * Example:
     * ```php
     * $map = ImmMap("a" => Some(1), "b" => Some(2));
     * $map->sequence(); // Some(ImmMap("a" => 1, "b" => 2))
     *
     * $map2 = ImmMap("a" => Some(1), "b" => None());
     * $map2->sequence(); // None()
     * ```
     *
     * @return Kind An effect containing the map of unwrapped values
     */
    public function sequence(): Kind
    {
        if (count($this->keys()) === 0) {
            // For empty maps, we need to return the appropriate type constructor
            // We'll return Some(ImmMap()) as the default
            return Some(ImmMap());
        }

        $typeConstructor = $this->guardIsMapOfTypeConstructor();
        $unwrappedMap = ImmMap();

        foreach ($this->iterator() as $k => $v) {
            if (!$v->isDefined()) {
                return None();
            }
            $unwrappedMap = $unwrappedMap->plus($k, $v->get());
        }

        return $typeConstructor($unwrappedMap);
    }

    /**
     * Filters the map by a predicate on key-value pairs.
     *
     * Returns a new map containing only the entries where the predicate returns true.
     *
     * Example:
     * ```php
     * $map = ImmMap("a" => 1, "b" => 2, "c" => 3);
     * $map->filter(fn($pair) => $pair->_2 > 1);
     * // ImmMap("b" => 2, "c" => 3)
     * ```
     *
     * @param callable(Pair<K,V>):bool $condition Predicate to test each pair
     * @return ImmMap<K,V> Filtered map
     */
    public function filter(callable $condition): ImmMap
    {
        $result = ImmMap();
        foreach ($this->iterator() as $k => $v) {
            if ($condition(Pair($k, $v))) {
                $result = $result->plus($k, $v);
            }
        }
        return $result;
    }

    /**
     * Filters the map by a predicate on keys only.
     *
     * Returns a new map containing only the entries where the key predicate returns true.
     *
     * Example:
     * ```php
     * $map = ImmMap("a" => 1, "b" => 2, "c" => 3);
     * $map->filterKeys(fn($k) => $k !== "b");
     * // ImmMap("a" => 1, "c" => 3)
     * ```
     *
     * @param callable(K):bool $condition Predicate to test each key
     * @return ImmMap<K,V> Filtered map
     */
    public function filterKeys(callable $condition): ImmMap
    {
        $result = ImmMap();
        foreach ($this->iterator() as $k => $v) {
            if ($condition($k)) {
                $result = $result->plus($k, $v);
            }
        }
        return $result;
    }

    /**
     * Filters the map by a predicate on values only.
     *
     * Returns a new map containing only the entries where the value predicate returns true.
     *
     * Example:
     * ```php
     * $map = ImmMap("a" => 1, "b" => 2, "c" => 3);
     * $map->filterValues(fn($v) => $v % 2 === 0);
     * // ImmMap("b" => 2)
     * ```
     *
     * @param callable(V):bool $condition Predicate to test each value
     * @return ImmMap<K,V> Filtered map
     */
    public function filterValues(callable $condition): ImmMap
    {
        $result = ImmMap();
        foreach ($this->iterator() as $k => $v) {
            if ($condition($v)) {
                $result = $result->plus($k, $v);
            }
        }
        return $result;
    }

    /**
     * Guards that the map contains values of a type constructor.
     *
     * @return string The type constructor name
     * @throws \TypeError If values are not of a type constructor
     */
    private function guardIsMapOfTypeConstructor(): string
    {
        $values = $this->values();
        if (empty($values)) {
            throw new \TypeError("Cannot sequence an empty map");
        }

        $mapType = showArrayType($values);
        $typeConstructor = substr($mapType, 0, strpos($mapType, "<"));

        if ($typeConstructor == "") {
            throw new \TypeError("Cannot find a type constructor in map values of type $mapType");
        }

        if (!is_callable($typeConstructor)) {
            throw new \TypeError("$typeConstructor is not a callable type constructor");
        }

        return $typeConstructor;
    }
}
