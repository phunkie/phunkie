<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\Types;

/**
 * Base interface for higher-kinded types.
 * 
 * Kind represents type constructors that can be parameterized with other types.
 * It provides the foundation for generic programming with type constructors like
 * Option<A>, List<A>, Map<K,V>, etc.
 *
 * Example:
 * ```php
 * // Option<A> is a Kind with arity 1
 * Option::kind;        // "Option"
 * Option(42)->getTypeArity();  // 1
 * 
 * // Map<K,V> is a Kind with arity 2
 * ImmMap::kind;        // "ImmMap"
 * ImmMap()->getTypeArity();    // 2
 * ```
 *
 * @mixin \Phunkie\Cats\Functor
 */
interface Kind
{
    /**
     * Returns the number of type parameters this type takes.
     *
     * @return int The type arity (e.g., 1 for Option<A>, 2 for Map<K,V>)
     */
    public function getTypeArity(): int;

    /**
     * Returns an array of type variables for this type.
     *
     * @return array<string> The type variables (e.g., ["Int"] for Option<Int>)
     */
    public function getTypeVariables(): array;
}
