<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\Functions\function1 {

    use function Phunkie\Functions\semigroup\combine;

    /**
     * Function composition and manipulation utilities.
     * 
     * This module provides utilities for working with functions:
     * - identity: The identity function
     * - compose: Function composition
     */

    /**
     * The identity function.
     * 
     * Returns its argument unchanged. Useful as a default transformation
     * or in function composition.
     *
     * Example:
     * ```php
     * identity(42);        // Returns 42
     * identity("string");  // Returns "string"
     * 
     * // As default transformation
     * $transform = $shouldTransform ? $someFunction : identity;
     * 
     * // In composition
     * compose(identity, $f); // Same as just $f
     * ```
     *
     * @template A
     * @param A $x The value to return
     * @return A The same value unchanged
     */
    const identity = "\\Phunkie\\Functions\\function1\\identity";
    function identity($x)
    {
        return $x;
    }

    /**
     * Composes multiple functions into a single function.
     * 
     * Creates a new function that applies each function from right to left.
     * Each function's result is passed as input to the next function.
     *
     * Example:
     * ```php
     * $double = fn($x) => $x * 2;
     * $addOne = fn($x) => $x + 1;
     * $toString = fn($x) => (string)$x;
     * 
     * // Creates: fn($x) => $toString($addOne($double($x)))
     * $f = compose($toString, $addOne, $double);
     * 
     * $f(3); // Returns "7" (3 * 2 = 6, + 1 = 7, toString = "7")
     * ```
     *
     * @template A,B,C,...
     * @param callable ...$fs Functions to compose
     * @return Function1 A Function1 instance representing the composition
     */
    const compose = "\\Phunkie\\Functions\\function1\\compose";
    function compose(callable ...$fs)
    {
        return combine(...array_map(fn ($f) => Function1($f), array_reverse($fs)));
    }

}

namespace {

    use Phunkie\PatternMatching\Wildcarded\Function1 as WildcardedFunction1;
    use Phunkie\Types\Function1;

    /**
     * Creates a Function1 instance.
     * 
     * Wraps a callable in a Function1 object for composition and pattern matching.
     * Returns a wildcarded Function1 if given the underscore placeholder.
     *
     * Example:
     * ```php
     * $f = Function1(fn($x) => $x * 2);
     * $f(3); // Returns 6
     * 
     * // Pattern matching
     * match($f) {
     *     Function1(_) => "Got a function",
     *     default => "Not a function"
     * };
     * ```
     *
     * @template A,B
     * @param callable(A):B|'_' $f Function to wrap or underscore
     * @return Function1|WildcardedFunction1 Function wrapper
     */
    function Function1($f)
    {
        if ($f == _) {
            return new WildcardedFunction1();
        }
        return new Function1($f);
    }

}
