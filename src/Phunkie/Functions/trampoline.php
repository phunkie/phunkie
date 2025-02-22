<?php

namespace Phunkie\Functions\trampoline;

use Phunkie\Utils\Trampoline\More;
use Phunkie\Utils\Trampoline\Done;

/**
 * Functions for implementing trampolines.
 * 
 * Trampolines allow tail-recursive functions to execute without growing the stack.
 * This enables deep recursion without stack overflow by converting recursion to iteration.
 */

/**
 * Creates a continuation for trampoline execution.
 * 
 * Represents a computation that needs more steps to complete.
 * Used with Done() to implement stack-safe recursion.
 *
 * Example:
 * ```php
 * // Stack-safe factorial implementation
 * function factorial($n, $acc = 1) {
 *     return $n <= 1 ? Done($acc) : 
 *         More(fn() => factorial($n - 1, $n * $acc));
 * }
 * 
 * factorial(5)->run();    // 120
 * factorial(10000)->run(); // Works without overflow
 * ```
 *
 * @template A
 * @param callable():More<A>|Done<A> $k Next computation step
 * @return More<A> Continuation for more computation
 */
function More(callable $k)
{
    return new More($k);
}

/**
 * Creates a completed trampoline result.
 * 
 * Represents a computation that has finished and has a value.
 * Used with More() to implement stack-safe recursion.
 *
 * Example:
 * ```php
 * // Stack-safe list sum
 * function sum($list, $acc = 0) {
 *     return $list->isEmpty() ? Done($acc) :
 *         More(fn() => sum($list->tail(), $acc + $list->head()));
 * }
 * 
 * sum(ImmList(1,2,3))->run();  // 6
 * ```
 *
 * @template A
 * @param A $v Final computation result
 * @return Done<A> Completed computation
 */
function Done($v)
{
    return new Done($v);
}
