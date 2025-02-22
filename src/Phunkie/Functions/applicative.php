<?php

namespace Phunkie\Functions\applicative;

use Phunkie\Cats\Applicative;
use Phunkie\Cats\Apply;
use Phunkie\Types\Kind;
use function Phunkie\Functions\currying\applyPartially;

/**
 * Applicative functor operations.
 * 
 * This module provides functions for working with applicative functors:
 * - ap: Apply a wrapped function to a wrapped value
 * - pure: Lift a value into an applicative context
 * - map2: Map a function over two applicative values
 */

/**
 * Applies a wrapped function to a wrapped value.
 * 
 * Given F<A -> B> and F<A>, produces F<B> by applying the function
 * inside F to the value inside F.
 *
 * Example:
 * ```php
 * $f = Some(fn($x) => $x + 1);
 * $x = Some(42);
 * ap($f)($x); // Some(43)
 * ```
 *
 * @template A,B
 * @param Kind<A -> B> $f The wrapped function
 * @return callable Function expecting F<A> that returns F<B>
 */
const ap = "\\Phunkie\\Functions\\applicative\\ap";
function ap(Kind $f)
{
    return applyPartially([$f], func_get_args(), fn (Applicative $applicative) => $applicative->apply($f));
}

/**
 * Lifts a value into an applicative context.
 * 
 * Takes a value and wraps it in the minimal context of a given applicative.
 *
 * Example:
 * ```php
 * pure(Option)(42);  // Some(42)
 * pure(ImmList)(42); // ImmList(42)
 * ```
 *
 * @template A
 * @param callable $context The applicative constructor
 * @return callable Function expecting value to lift
 * @throws \Error If context is not applicative
 */
const pure = "\\Phunkie\\Functions\\applicative\\pure";
function pure($context)
{
    return applyPartially([$context], func_get_args(), function ($a) use ($context) {
        if (($fa = $context($a)) instanceof Applicative) {
            return $fa;
        }
        throw new \Error("$context is not an applicative context");
    });
}

/**
 * Maps a function over two applicative values.
 * 
 * Takes a binary function and applies it to two wrapped values,
 * producing a new wrapped value.
 *
 * Example:
 * ```php
 * $add = fn($x, $y) => $x + $y;
 * $x = Some(42);
 * $y = Some(1);
 * map2($add)($x)($y); // Some(43)
 * ```
 *
 * @template A,B,C
 * @param callable $f Function taking two args
 * @return callable Function expecting two Apply values
 */
const map2 = "\\Phunkie\\Functions\\applicative\\map2";
function map2(callable $f)
{
    return applyPartially([$f], func_get_args(), fn (Apply $fa) => fn (Apply $fb) => $fa->map2($fb, $f));
}
