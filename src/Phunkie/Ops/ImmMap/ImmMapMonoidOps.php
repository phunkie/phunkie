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

/**
 * Monoid operations for ImmMap.
 *
 * Provides the zero (empty map) and combine (merge) operations.
 *
 * @mixin \Phunkie\Types\ImmMap
 */
trait ImmMapMonoidOps
{
    /**
     * Returns the identity element for map combination (empty map).
     *
     * The zero element is an empty map that, when combined with any other map,
     * returns that map unchanged.
     *
     * Example:
     * ```php
     * $map = ImmMap("a" => 1);
     * $map->zero(); // ImmMap()
     * $map->combine($map->zero())->eqv($map); // true
     * ```
     *
     * @return ImmMap<K,V> An empty map
     */
    public function zero(): ImmMap
    {
        return ImmMap();
    }

    /**
     * Combines two maps into one.
     *
     * Merges the current map with another map. If keys overlap, values from
     * the argument map ($b) take precedence.
     *
     * Example:
     * ```php
     * $map1 = ImmMap("a" => 1, "b" => 2);
     * $map2 = ImmMap("b" => 3, "c" => 4);
     * $map1->combine($map2);
     * // ImmMap("a" => 1, "b" => 3, "c" => 4)
     * ```
     *
     * @param ImmMap<K,V> $b The map to combine with
     * @return ImmMap<K,V> The combined map
     */
    public function combine(ImmMap $b): ImmMap
    {
        $result = $this->copy();
        foreach ($b->iterator() as $k => $v) {
            $result = $result->plus($k, $v);
        }
        return $result;
    }
}
