<?php

namespace Phunkie\Cats\Free;

use Phunkie\Cats\Free;
use Phunkie\Types\Kind;

/**
 * Represents sequential composition in the Free monad.
 * 
 * Bind is one of the three constructors of Free, representing the sequencing
 * of computations. It combines a Free computation with a function that produces
 * the next computation based on the result of the first.
 *
 * Example:
 * ```php
 * // Sequence reading and writing
 * $program = new Bind(
 *     ConsoleF::readLine(),
 *     fn($input) => ConsoleF::printLine("You entered: " . $input)
 * );
 * $program->foldMap($interpreter); // Interprets both operations in sequence
 * ```
 *
 * @template F
 * @template A
 * @template B
 * @extends Free<F,B>
 */
final class Bind extends Free
{
    private $target;
    private $f;

    /**
     * Creates a sequential composition of Free operations.
     *
     * @param Free<F,A> $target The first computation
     * @param callable(A):Free<F,B> $f Function producing the next computation
     */
    public function __construct(Free $target, callable $f)
    {
        $this->target = $target;
        $this->f = $f;
    }

    /**
     * Returns the first computation.
     *
     * @return Free<F,A> The target computation
     */
    public function getTarget(): Free
    {
        return $this->target;
    }

    /**
     * Returns the continuation function.
     *
     * @return callable(A):Free<F,B> The function producing the next computation
     */
    public function getF(): callable
    {
        return $this->f;
    }

    /**
     * Resumes one step of computation.
     * For Bind, this evaluates the first computation and applies the continuation.
     *
     * @return Kind<F,Free<F,B>> The resumed computation
     */
    public function resume(): Kind
    {
        return $this->target->flatMap($this->f);
    }
}
