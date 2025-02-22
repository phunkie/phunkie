<?php

namespace Phunkie\Utils\Trampoline;

/**
 * Stack-safe recursion using trampolining.
 * 
 * Trampoline enables tail-call optimization by converting recursive calls
 * into an iterative process. This prevents stack overflow for deep recursion
 * by bouncing between thunks until reaching a final value.
 *
 * Example:
 * ```php
 * // Stack-safe factorial implementation
 * function factorial($n, $acc = 1): Trampoline {
 *     return match(true) {
 *         $n == 0 => new Done($acc),
 *         default => new More(
 *             fn() => factorial($n - 1, $acc * $n)
 *         )
 *     };
 * }
 * 
 * // Usage
 * $result = factorial(5)->run(); // 120
 * 
 * // Even large values won't overflow
 * $result = factorial(10000)->run();
 * ```
 *
 * @see Done Final value in computation
 * @see More Continuation of computation
 */
abstract class Trampoline
{
    /**
     * Runs the trampolined computation to completion.
     * 
     * Iteratively evaluates the computation until reaching a Done value,
     * preventing stack overflow that would occur with normal recursion.
     *
     * @return mixed The final result of the computation
     */
    public function run()
    {
        $result = $this->get();

        while ($result instanceof Trampoline) {
            $result = $result->get();
        }

        return $result;
    }

    /**
     * Gets the next step in the computation.
     * 
     * @return mixed Either another Trampoline or the final value
     */
    abstract public function get();
}
