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

use Phunkie\Types\Kind;
use function Phunkie\Functions\applicative\pure as point;
use function Phunkie\Functions\monad\bind as flatMap;
use function Phunkie\PatternMatching\Referenced\Pure;
use function Phunkie\PatternMatching\Referenced\Suspend;
use function Phunkie\PatternMatching\Referenced\Bind;

/**
 * Abstract base class for the Free monad.
 * 
 * Free monads decouple program description from interpretation, allowing you to
 * write pure functional programs and interpret them later in different ways.
 * This enables separation of concerns between business logic and effects.
 *
 * The Free monad has three constructors:
 * - Pure: wraps pure values
 * - Suspend: wraps a single computation step
 * - Bind: sequences computations
 *
 * Example:
 * ```php
 * // Define program algebra
 * interface Console { 
 *     public function readLine(): Free;
 *     public function printLine(string $s): Free;
 * }
 * 
 * // Write pure program
 * $program = Free::pure("What's your name?")
 *     ->flatMap(fn($q) => Console::printLine($q))
 *     ->flatMap(fn($_) => Console::readLine())
 *     ->flatMap(fn($name) => Console::printLine("Hello, $name!"));
 * 
 * // Interpret later
 * $interpreter = new ConsoleInterpreter();
 * $program->foldMap($interpreter);
 * ```
 *
 * @template F
 * @template A
 */
abstract class Free
{
    /**
     * Lifts a pure value into the Free monad.
     *
     * @template B
     * @param B $a The value to lift
     * @return Free<F,B> A Pure instance containing the value
     */
    public static function pure($a)
    {
        return new Free\Pure($a);
    }

    /**
     * Lifts a functor value into the Free monad.
     *
     * @template B
     * @param Kind<F,B> $fa The functor value to lift
     * @return Free<F,B> A Suspend instance containing the computation
     */
    public static function liftM(Kind $fa)
    {
        return new Free\Suspend($fa);
    }

    /**
     * Sequences two Free computations.
     *
     * @template B
     * @param callable(A):Free<F,B> $f Function producing the next computation
     * @return Free<F,B> A Bind instance representing the sequence
     */
    public function flatMap($f)
    {
        return new Free\Bind($this, $f);
    }

    /**
     * Interprets the Free structure using a natural transformation.
     *
     * @template G
     * @param NaturalTransformation<F,G> $nt Transformation to the target type
     * @return Kind<G,A> The interpreted result
     */
    public function foldMap(NaturalTransformation $nt) { $on = pmatch($this); return match (true) {
        $on(Pure($a)) => (point($nt->to))($a),
        $on(Suspend($fa)) => $nt($fa),
        $on(Bind($target, $f)) => flatMap(
            fn ($e) => $f($e)->foldMap($nt),
            $target->foldMap($nt)
        )};
    }
}
