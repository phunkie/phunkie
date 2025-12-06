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

use InvalidArgumentException;
use Phunkie\Cats\Bifunctor;
use Phunkie\Cats\Comonad;
use Phunkie\Cats\Foldable;
use Phunkie\Cats\Show;
use Phunkie\Ops\Pair\PairBifunctorOps;
use Phunkie\Ops\Pair\PairComonadOps;
use Phunkie\Ops\Pair\PairFoldableOps;
use Phunkie\Ops\Pair\PairFunctorOps;
use Phunkie\Ops\Pair\PairOps;
use TypeError;
use function Phunkie\Functions\show\showValue;

/**
 * Pairs are specialized tuples with exactly two elements.
 *
 * Pairs are immutable and type-safe. The types of both elements are preserved
 * through generic type parameters T1 and T2.
 *
 * Pairs support multiple type class instances:
 * - Bifunctor: map over both elements independently
 * - Functor: map over the second element
 * - Foldable: fold over the second element
 * - Comonad: extract and extend computations
 *
 * Example:
 * ```php
 * $pair = Pair("name", 25);
 * $name = $pair->_1; // "name" (type T1)
 * $age = $pair->_2;  // 25 (type T2)
 *
 * // Bifunctor operations
 * $pair->bimap(fn($s) => strtoupper($s), fn($n) => $n + 1);
 * // Pair("NAME", 26)
 *
 * // Functor operations (maps over second element)
 * $pair->map(fn($n) => $n * 2);  // Pair("name", 50)
 *
 * // Foldable operations (folds over second element)
 * ($pair->foldLeft(0))(fn($acc, $x) => $acc + $x);  // 25
 *
 * // Comonad operations
 * $pair->extract();  // 25
 * $pair->extend(fn($p) => $p->_1 . ": " . $p->_2);
 * // Pair("name", "name: 25")
 * ```
 *
 * @template T1 The type of the first element
 * @template T2 The type of the second element
 * @property-read T1 $_1 First element of the pair
 * @property-read T2 $_2 Second element of the pair
 * @implements Bifunctor<T1,T2>
 * @implements Comonad<T2>
 * @implements Foldable<T2>
 * @implements Show<Pair<T1,T2>>
 */
final class Pair extends Tuple implements Bifunctor, Comonad, Foldable
{
    use Show;
    use PairBifunctorOps;
    use PairFunctorOps;
    use PairFoldableOps;
    use PairComonadOps;
    use PairOps;

    /**
     * Access pair elements by property name.
     * 
     * Elements can be accessed using _1 and _2 properties:
     * ```php
     * $pair = Pair("name", 25);
     * echo $pair->_1; // "name"
     * echo $pair->_2; // 25
     * ```
     *
     * @param string $member The property name (_1 or _2)
     * @return T1|T2 The value at the given position
     * @throws InvalidArgumentException if the member name is not _1 or _2
     */
    public function __get(string $member) { return match ($member) {
        "_1", "_2" => parent::__get($member),
        default => throw new InvalidArgumentException("Invalid index $member for pair")};
    }

    /**
     * Pairs are immutable - attempting to modify elements throws an error.
     *
     * Example:
     * ```php
     * $pair = Pair("hello", 42);
     * $pair->_1 = "world"; // Throws TypeError
     * $pair->_3 = true;    // Throws TypeError
     * ```
     *
     * @param string $member Unused - pairs are immutable
     * @param mixed $value Unused - pairs are immutable  
     * @throws TypeError always, since pairs are immutable
     */
    public function __set($member, $value): void
    {
        throw new TypeError("Pairs are immutable");
    }

    /**
     * Returns a string representation of the pair.
     *
     * Example:
     * ```php
     * $pair = Pair("name", 25);
     * echo $pair->toString(); // "Pair(name, 25)"
     * ```
     *
     * @return string The string representation
     */
    public function toString(): string
    {
        return "Pair(" . showValue(parent::__get("_1")) . ", " . showValue(parent::__get("_2")) . ")";
    }

    /**
     * Returns a string showing the types of both elements.
     *
     * Example:
     * ```php
     * $pair = Pair("hello", 42);
     * echo $pair->showType(); // "(String, Int)"
     * ```
     *
     * @return string The type representation as "(T1, T2)"
     */
    public function showType(): string
    {
        return sprintf("(%s)", implode(", ", $this->getTypeVariables()));
    }
}
