<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\Cats;

use function Phunkie\Functions\io\io;
use Phunkie\Types\Kind;

/**
 * Represents computations that perform I/O operations.
 * 
 * IO is a monad that encapsulates side effects, allowing them to be composed
 * and sequenced in a pure functional way. The actual effects only occur when
 * the IO is "run".
 *
 * Example:
 * ```php
 * $getLine = IO(fn() => readline());
 * $printLine = fn($s) => IO(fn() => print($s . PHP_EOL));
 * 
 * // Compose IO operations
 * $program = $getLine
 *   ->flatMap(fn($input) => $printLine("You entered: " . $input));
 * 
 * // Nothing happens until we run it
 * $program->run();
 * ```
 *
 * @template A
 * @implements Kind<IO,A>
 */
class IO implements Kind
{
    public const kind = "IO";
    private $thunk;

    /**
     * Creates a new IO action.
     *
     * @param callable():A $thunk The computation to perform
     */
    public function __construct(callable $thunk)
    {
        $this->thunk = $thunk;
    }

    /**
     * Executes the IO action.
     *
     * @return A The result of the computation
     */
    public function run()
    {
        return ($this->thunk)();
    }

    /**
     * Maps a function over the result of this IO action.
     *
     * @template B
     * @param callable(A):B $f The function to apply
     * @return IO<B> A new IO that will apply the function
     */
    public function map(callable $f): Kind
    {
        return new IO(fn() => $f($this->run()));
    }

    /**
     * Chains IO actions together.
     *
     * @template B
     * @param callable(A):IO<B> $f Function producing the next IO action
     * @return IO<B> The composed IO action
     */
    public function flatMap(callable $f): Kind
    {
        return new IO(fn() => $f($this->run())->run());
    }

    /**
     * Returns the number of type parameters.
     *
     * @return int Always returns 1
     */
    public function getTypeArity(): int
    {
        return 1;
    }

    /**
     * Returns the type variables for this Kind.
     *
     * @return array<string> Array containing the single type variable
     */
    public function getTypeVariables(): array
    {
        return ['A'];
    }
}
