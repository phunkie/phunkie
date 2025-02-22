<?php

namespace Phunkie\Functions\monad;

use Phunkie\Cats\FlatMap;
use Phunkie\Cats\Monad as Flatten;
use function Phunkie\Functions\currying\applyPartially;
use function Phunkie\Functions\function1\compose;
use const Phunkie\Functions\function1\identity;

/**
 * Functions for working with monads.
 * 
 * This module provides functions for working with monadic structures,
 * particularly for sequencing operations and handling effects.
 * Supports common monadic operations like:
 * - Binding computations (flatMap)
 * - Lifting values (pure)
 * - Sequencing effects (mcompose)
 */

/**
 * Binds a monadic function to a monad.
 * 
 * Applies a function that returns a monad to a monadic value,
 * flattening the result. This is the core monadic operation,
 * also known as flatMap or >>=.
 *
 * Example:
 * ```php
 * $safeDivide = fn($n) => $n == 0 ? None() : Some(1/$n);
 * 
 * bind($safeDivide)(Some(2));    // Some(0.5)
 * bind($safeDivide)(Some(0));    // None
 * bind($safeDivide)(None());     // None
 * ```
 *
 * @template A,B,M
 * @param callable(A):M<B> $f Function returning monad
 * @return callable(M<A>):M<B> Function expecting monad
 */
const bind = "\\Phunkie\\Functions\\monad\\bind";
function bind($f)
{
    return applyPartially([$f], func_get_args(), fn (FlatMap $monad) => $monad->flatMap($f));
}

/**
 * Flattens a nested monadic structure.
 * 
 * Removes one level of monadic structure from a nested monad.
 * Useful for avoiding deeply nested monadic values.
 *
 * Example:
 * ```php
 * $nested = Some(Some(42));
 * flatten($nested);  // Some(42)
 * 
 * $lists = ImmList(ImmList(1,2), ImmList(3,4));
 * flatten($lists);   // ImmList(1,2,3,4)
 * ```
 *
 * @template A,M
 * @param M<M<A>> $monad Nested monadic value
 * @return M<A> Flattened monad
 */
const flatten = "\\Phunkie\\Functions\\monad\\flatten";
function flatten(Flatten $monad)
{
    return $monad->flatten();
}

/**
 * Composes monadic functions.
 * 
 * Creates a new function that chains two monadic functions,
 * passing the result of the first into the second.
 *
 * Example:
 * ```php
 * $parseInt = fn($s) => is_numeric($s) ? Some((int)$s) : None();
 * $double = fn($n) => Some($n * 2);
 * 
 * $parseAndDouble = mcompose($parseInt, $double);
 * $parseAndDouble("42");  // Some(84)
 * $parseAndDouble("abc"); // None
 * ```
 *
 * @template A,B,C,M
 * @param callable(A):M<B> $f First function
 * @param callable(B):M<C> $g Second function
 * @return callable(A):M<C> Composed function
 */
const mcompose = "\\Phunkie\\Functions\\monad\\mcompose";
function mcompose(...$fs) { return match (count($fs)) {
    0 => identity,
    1 => bind($fs[0]),
    default => compose(bind($fs[0]), mcompose(...array_slice($fs, 1))) };
}
