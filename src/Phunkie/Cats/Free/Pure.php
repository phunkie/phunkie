<?php

namespace Phunkie\Cats\Free;

use Phunkie\Cats\Free;

/**
 * Represents a pure value in the Free monad.
 * 
 * Pure is one of the three constructors of Free, representing values that are
 * already in their final form and need no further interpretation. It's equivalent
 * to 'return' or 'pure' in other monadic contexts.
 *
 * Example:
 * ```php
 * $pure = new Pure(42);
 * $pure->foldMap($interpreter); // Returns 42 wrapped in target monad
 * ```
 *
 * @template F
 * @template A
 * @extends Free<F,A>
 */
final class Pure extends Free
{
    private $a;

    /**
     * Creates a Pure value.
     *
     * @param A $a The pure value to wrap
     */
    public function __construct($a)
    {
        $this->a = $a;
    }

    /**
     * Returns the pure value.
     *
     * @return A The wrapped value
     */
    public function get()
    {
        return $this->a;
    }

    /**
     * Resumes one step of computation.
     * For Pure, this just returns the value.
     *
     * @return Kind<F,Free<F,A>> The resumed computation
     */
    public function resume(): Kind
    {
        return Pure($this->a);
    }
}
