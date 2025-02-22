<?php

namespace Phunkie\Utils\Trampoline;

/**
 * Represents a completed trampolined computation.
 * 
 * Done wraps a final value in a trampolined computation, indicating
 * that no further evaluation steps are needed.
 *
 * Example:
 * ```php
 * // Base case in factorial
 * function factorial($n, $acc = 1): Trampoline {
 *     return match(true) {
 *         $n == 0 => new Done($acc), // Terminal case
 *         default => new More(...)
 *     };
 * }
 * 
 * // Direct usage
 * $done = new Done(42);
 * $result = $done->run(); // 42
 * ```
 *
 * @see Trampoline Base class for trampolined computations
 * @see More Continuation in trampolined computation
 */
class Done extends Trampoline
{
    /** @var mixed The final computed value */
    private $value;

    /**
     * Creates a Done value.
     *
     * @param mixed $value The final result of the computation
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * Returns the final value.
     *
     * @return mixed The completed computation result
     */
    public function get()
    {
        return $this->value;
    }
}
