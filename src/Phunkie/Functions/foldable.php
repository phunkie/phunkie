<?php

namespace Phunkie\Functions\foldable;

use Phunkie\Cats\Foldable;
use function Phunkie\Functions\currying\applyPartially;

/**
 * Functions for folding over Foldable structures.
 * 
 * This module provides functions for reducing Foldable structures using:
 * - foldl: Left-associative fold
 * - foldr: Right-associative fold
 * - foldMap: Fold by mapping then combining
 */

/**
 * Left-associative fold over a Foldable structure.
 * 
 * Reduces a structure from left to right, starting with an initial value
 * and applying a binary function to each element.
 *
 * Example:
 * ```php
 * $add = fn($acc, $x) => $acc + $x;
 * $list = ImmList(1, 2, 3);
 * 
 * // ((0 + 1) + 2) + 3
 * $result = foldl($add)(0)($list); // Returns 6
 * 
 * // String concatenation
 * $concat = fn($acc, $x) => $acc . $x;
 * foldl($concat)("")(ImmList("a", "b", "c")); // Returns "abc"
 * ```
 *
 * @template A,B
 * @param callable(B,A):B $f Binary reduction function
 * @return callable(B) Returns function expecting initial value
 */
const foldl = "Phunkie\\Functions\\foldable\\foldl";
function foldl(callable $f)
{
    return applyPartially([$f], func_get_args(), fn ($initial) => 
        applyPartially([$initial], func_get_args(), fn (Foldable $foldable) => 
            $foldable->foldLeft($initial, $f)));
}

/**
 * Right-associative fold over a Foldable structure.
 * 
 * Reduces a structure from right to left, starting with an initial value
 * and applying a binary function to each element.
 *
 * Example:
 * ```php
 * $div = fn($x, $acc) => $x / $acc;
 * $list = ImmList(12, 6, 3);
 * 
 * // 12 / (6 / (3 / 1))
 * $result = foldr($div)(1)($list); // Returns 0.5
 * 
 * // List construction
 * $cons = fn($x, $acc) => Cons($x, $acc);
 * foldr($cons)(Nil())(ImmList(1,2,3)); // Returns ImmList(1,2,3)
 * ```
 *
 * @template A,B
 * @param callable(A,B):B $f Binary reduction function
 * @return callable(B) Returns function expecting initial value
 */
const foldr = "Phunkie\\Functions\\foldable\\foldr";
function foldr(callable $f)
{
    return applyPartially([$f], func_get_args(), fn ($initial) =>
        applyPartially([$initial], func_get_args(), fn (Foldable $foldable) =>
            $foldable->foldRight($initial, $f)));
}

/**
 * Maps elements to a Monoid then combines them.
 * 
 * First maps each element to a Monoid value, then combines all
 * results using the Monoid's combine operation.
 *
 * Example:
 * ```php
 * $toString = fn($x) => (string)$x;
 * $list = ImmList(1, 2, 3);
 * 
 * // Maps to strings then concatenates
 * $result = foldMap($toString)($list); // Returns "123"
 * 
 * // With custom monoid
 * $toList = fn($x) => ImmList($x);
 * foldMap($toList)(ImmList(1,2,3)); // Returns ImmList(1,2,3)
 * ```
 *
 * @template A,B
 * @param callable(A):B $f Function returning Monoid values
 * @return callable(Foldable<A>) Returns function expecting Foldable
 */
const foldMap = "Phunkie\\Functions\\foldable\\foldMap";
function foldMap(callable $f)
{
    return applyPartially([$f], func_get_args(), fn (Foldable $foldable) => 
        $foldable->foldMap($f));
}
