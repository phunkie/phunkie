<?php

namespace Phunkie\Utils\Trampoline;

/**
 * Represents a continuation in a trampolined computation.
 * 
 * More wraps a thunk (suspended computation) that will produce the next
 * step in a trampolined computation. This enables stack-safe recursion
 * by preventing direct recursive calls.
 *
 * Example:
 * ```php
 * // Recursive case in factorial
 * function factorial($n, $acc = 1): Trampoline {
 *     return match(true) {
 *         $n == 0 => new Done($acc),
 *         default => new More(
 *             fn() => factorial($n - 1, $acc * $n)
 *         )
 *     };
 * }
 * 
 * // Direct usage
 * $more = new More(fn() => new Done(42));
 * $result = $more->run(); // 42
 * ```
 *
 * @see Trampoline Base class for trampolined computations
 * @see Done Terminal value in trampolined computation
 */
class More extends Trampoline
{
    /** @var callable The thunk to evaluate for next step */
    private $thunk;

    /**
     * Creates a More continuation.
     *
     * @param callable $thunk Function that returns next computation step
     */
    public function __construct(callable $thunk)
    {
        $this->thunk = $thunk;
    }

    /**
     * Evaluates the thunk to get next computation step.
     *
     * @return mixed The next step in computation
     */
    public function get()
    {
        return ($this->thunk)();
    }
}
