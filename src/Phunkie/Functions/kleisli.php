<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\Functions\kleisli;

use Phunkie\Cats\Kleisli;
use Phunkie\Types\Option;

/**
 * Functions for working with Kleisli arrows.
 * 
 * This module provides functions for composing and working with
 * functions that return monadic values (Kleisli arrows).
 * Useful for chaining operations that might fail or have effects.
 *
 * Example:
 * ```php
 * // Functions returning Option
 * $parseInt = kleisli(fn($s) => 
 *     is_numeric($s) ? Some((int)$s) : None());
 * 
 * $double = kleisli(fn($n) => Some($n * 2));
 * 
 * // Compose them
 * $parseAndDouble = compose($parseInt, $double);
 * 
 * $result = $parseAndDouble("42");    // Some(84)
 * $failed = $parseAndDouble("abc");   // None
 * ```
 */

/**
 * Creates a Kleisli arrow from a function.
 * 
 * Wraps a function that returns a monad into a Kleisli arrow
 * for safe composition.
 *
 * Example:
 * ```php
 * $safe = kleisli(fn($x) => 
 *     $x > 0 ? Some($x) : None()
 * );
 * ```
 *
 * @template A,B,M
 * @param callable(A):M<B> $f Function returning monad
 * @return Kleisli<M,A,B> The Kleisli arrow
 */
function kleisli(callable $f): Kleisli
{
    return new Kleisli($f);
}

/**
 * Composes two Kleisli arrows.
 * 
 * Creates a new Kleisli that runs the first arrow,
 * then feeds its result to the second arrow.
 *
 * Example:
 * ```php
 * $first = kleisli(fn($s) => Some((int)$s));
 * $second = kleisli(fn($n) => Some($n * 2));
 * 
 * $composed = compose($first, $second);
 * $result = $composed("21");  // Some(42)
 * ```
 *
 * @template A,B,C,M
 * @param Kleisli<M,A,B> $f First arrow
 * @param Kleisli<M,B,C> $g Second arrow
 * @return Kleisli<M,A,C> Composed arrow
 */
function compose(Kleisli $f, Kleisli $g): Kleisli
{
    return $f->andThen($g);
}

/**
 * Lifts a pure function into a Kleisli arrow.
 * 
 * Converts a regular function into a Kleisli arrow that
 * wraps its result in the specified monad.
 *
 * Example:
 * ```php
 * $double = fn($x) => $x * 2;
 * $safeDouble = lift($double, Option::class);
 * 
 * $result = $safeDouble(21);  // Some(42)
 * ```
 *
 * @template A,B,M
 * @param callable(A):B $f Pure function
 * @param class-string<M> $monad Monad class
 * @return Kleisli<M,A,B> Lifted function
 */
function lift(callable $f, string $monad): Kleisli
{
    return kleisli(fn($x) => $monad->pure($f($x)));
}

/**
 * Creates an identity Kleisli arrow.
 * 
 * Returns a Kleisli that wraps its input in the specified monad.
 *
 * Example:
 * ```php
 * $id = identity(Option::class);
 * $result = $id(42);  // Some(42)
 * ```
 *
 * @template A,M
 * @param class-string<M> $monad Monad class
 * @return Kleisli<M,A,A> Identity arrow
 */
function identity(string $monad): Kleisli
{
    return kleisli(fn($x) => $monad->pure($x));
}

/**
 * Sequences multiple Kleisli arrows.
 * 
 * Creates a new Kleisli that runs all arrows in sequence,
 * collecting their results in a list.
 *
 * Example:
 * ```php
 * $k1 = kleisli(fn($x) => Some($x + 1));
 * $k2 = kleisli(fn($x) => Some($x * 2));
 * 
 * $both = sequence([$k1, $k2]);
 * $result = $both(20);  // Some([21, 40])
 * ```
 *
 * @template A,B,M
 * @param array<Kleisli<M,A,B>> $arrows Arrows to sequence
 * @return Kleisli<M,A,array<B>> Combined arrow
 */
function sequence(array $arrows): Kleisli
{
    return kleisli(function($x) use ($arrows) {
        $results = [];
        foreach ($arrows as $arrow) {
            $result = $arrow->run($x);
            if ($result->isEmpty()) {
                return $result;
            }
            $results[] = $result->get();
        }
        return $result->pure($results);
    });
}
