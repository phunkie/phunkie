<?php

namespace Phunkie\Cats\Free;

use Phunkie\Cats\Free;

/**
 * Represents a suspended computation in the Free monad.
 * 
 * Suspend is one of the three constructors of Free, representing a single step
 * of computation that needs to be interpreted. It wraps a functor value that
 * will be transformed by the interpreter during foldMap.
 *
 * Example:
 * ```php
 * // Suspend a console read operation
 * $read = new Suspend(ConsoleF::readLine());
 * $read->foldMap($interpreter); // Interpreter handles the actual reading
 * ```
 *
 * @template F
 * @template A
 * @extends Free<F,A>
 */
final class Suspend extends Free
{
    private $fa;

    /**
     * Creates a suspended computation.
     *
     * @param Kind<F,A> $fa The functor value to suspend
     */
    public function __construct($fa)
    {
        $this->fa = $fa;
    }

    /**
     * Returns the suspended functor value.
     *
     * @return Kind<F,A> The wrapped functor
     */
    public function get()
    {
        return $this->fa;
    }

    /**
     * Resumes one step of computation.
     * For Suspend, this returns the suspended functor value.
     *
     * @return Kind<F,Free<F,A>> The resumed computation
     */
    public function resume()
    {
        return $this->fa;
    }
}
