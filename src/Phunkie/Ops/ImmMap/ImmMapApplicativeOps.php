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

use Phunkie\Cats\Applicative;
use Phunkie\Types\ImmMap;
use Phunkie\Types\Kind;
use Phunkie\Types\Pair;

/**
 * Applicative operations for ImmMap.
 *
 * Provides pure and apply operations for working with maps in an applicative context.
 *
 * @template K The key type
 * @template V The value type
 * @mixin \Phunkie\Types\ImmMap
 */
trait ImmMapApplicativeOps
{
    use ImmMapMonadOps;

    /**
     * Lifts a value into an ImmMap.
     *
     * Creates a map with a single entry using "default" as the key.
     *
     * Example:
     * ```php
     * ImmMap()->pure(42); // ImmMap("default" => 42)
     * ```
     *
     * @template T
     * @param T $a The value to lift
     * @return Applicative|ImmMap<string,T>
     */
    public function pure($a): Applicative
    {
        return ImmMap(["default" => $a]);
    }

    /**
     * Applies functions in a map to values in this map.
     *
     * For matching keys, applies the function from $ff to the value from this map.
     *
     * Example:
     * ```php
     * $map = ImmMap("a" => 5, "b" => 10);
     * $fns = ImmMap("a" => fn($x) => $x * 2, "b" => fn($x) => $x + 1);
     * $map->apply($fns); // ImmMap("a" => 10, "b" => 11)
     * ```
     *
     * @template B
     * @param Kind|ImmMap<K,callable(V):B> $ff Map of functions
     * @return Kind|ImmMap<K,B>
     */
    public function apply(Kind $ff): Kind
    {
        if (!$ff instanceof ImmMap) {
            throw new \TypeError("apply expects an ImmMap");
        }

        $result = ImmMap();
        foreach ($this->iterator() as $k => $v) {
            $maybeF = $ff->get($k);
            if ($maybeF->isDefined()) {
                $f = $maybeF->get();
                if (!is_callable($f)) {
                    throw new \TypeError("apply expects map values to be callable");
                }
                $result = $result->plus($k, $f($v));
            }
        }
        return $result;
    }

    /**
     * Combines two maps with a binary function.
     *
     * For matching keys, applies the function to values from both maps.
     *
     * Example:
     * ```php
     * $map1 = ImmMap("a" => 5, "b" => 10);
     * $map2 = ImmMap("a" => 3, "b" => 2);
     * $map1->map2($map2, fn($x, $y) => $x + $y);
     * // ImmMap("a" => 8, "b" => 12)
     * ```
     *
     * @template V2
     * @template R
     * @param Kind|ImmMap<K,V2> $fb Second map
     * @param callable(V,V2):R $f Binary function
     * @return Kind|ImmMap<K,R>
     */
    public function map2(Kind $fb, callable $f): Kind
    {
        if (!$fb instanceof ImmMap) {
            throw new \TypeError("map2 expects an ImmMap");
        }

        $result = ImmMap();
        foreach ($this->iterator() as $k => $v) {
            $maybeV2 = $fb->get($k);
            if ($maybeV2->isDefined()) {
                $result = $result->plus($k, $f($v, $maybeV2->get()));
            }
        }
        return $result;
    }
}
