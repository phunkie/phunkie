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

/**
 * Monad operations for ImmMap.
 *
 * Provides flatMap and flatten operations for chaining computations over maps.
 *
 * @template K The key type
 * @template V The value type
 * @mixin \Phunkie\Types\ImmMap
 */
trait ImmMapMonadOps
{
    use ImmMapFunctorOps;

    /**
     * Maps a function that returns a map for each entry, then flattens the results.
     *
     * This is the monadic bind operation for maps. It applies a function to each
     * key-value pair that returns a new map, then merges all resulting maps.
     *
     * Example:
     * ```php
     * $map = ImmMap("a" => 1, "b" => 2);
     * $map->flatMap(fn($pair) => ImmMap(
     *     $pair->_1 . "1" => $pair->_2,
     *     $pair->_1 . "2" => $pair->_2 * 2
     * ));
     * // ImmMap("a1" => 1, "a2" => 2, "b1" => 2, "b2" => 4)
     * ```
     *
     * @template K2
     * @template V2
     * @param callable(Pair<K,V>):ImmMap<K2,V2> $f Function returning a map
     * @return Kind|ImmMap<K2,V2> Flattened result
     */
    public function flatMap(callable $f): Kind
    {
        $result = ImmMap();
        foreach ($this->iterator() as $k => $v) {
            $mapped = $f(Pair($k, $v));
            if (!$mapped instanceof ImmMap) {
                throw new \TypeError("flatMap function must return an ImmMap");
            }
            foreach ($mapped->iterator() as $k2 => $v2) {
                $result = $result->plus($k2, $v2);
            }
        }
        return $result;
    }

    /**
     * Flattens a nested map structure.
     *
     * Removes one level of nesting from ImmMap<K,ImmMap<K2,V>>.
     *
     * Example:
     * ```php
     * $nested = ImmMap(
     *     "a" => ImmMap("x" => 1, "y" => 2),
     *     "b" => ImmMap("z" => 3)
     * );
     * $nested->flatten();
     * // ImmMap("x" => 1, "y" => 2, "z" => 3)
     * ```
     *
     * @return Kind|ImmMap<K2,V> Flattened map
     */
    public function flatten(): Kind
    {
        return $this->flatMap(function($pair) {
            $value = $pair->_2;
            return $value instanceof ImmMap ? $value : ImmMap([$pair->_1 => $value]);
        });
    }
}
