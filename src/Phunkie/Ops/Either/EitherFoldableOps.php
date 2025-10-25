<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\Ops\Either;

use function Phunkie\Functions\currying\applyPartially;
use function Phunkie\Functions\semigroup\combine;
use function Phunkie\Functions\semigroup\zero;

/**
 * Foldable operations for Either.
 *
 * Provides folding operations for reducing Either values.
 *
 * @template L The left (error) type
 * @template R The right (success) type
 * @mixin \Phunkie\Types\Either
 */
trait EitherFoldableOps
{
    /**
     * Left-associative fold over the Right value.
     *
     * Returns a curried function that accepts a binary operation.
     * Left values return the initial value unchanged.
     * Right values apply the function with (initial, value).
     *
     * Example:
     * ```php
     * $add = fn($acc, $x) => $acc + $x;
     * (Right(5)->foldLeft(10))($add);  // 15
     * (Left("error")->foldLeft(10))($add); // 10
     * ```
     *
     * @template B The accumulator type
     * @param B $initial Initial accumulator value
     * @return callable(callable(B,R):B):B Curried fold function
     */
    public function foldLeft($initial): callable
    {
        return applyPartially([$initial], func_get_args(), fn(callable $f) =>
            $this->isLeft() ? $initial : $f($initial, $this->get())
        );
    }

    /**
     * Right-associative fold over the Right value.
     *
     * Returns a curried function that accepts a binary operation.
     * Left values return the initial value unchanged.
     * Right values apply the function with (value, initial).
     *
     * Example:
     * ```php
     * $cons = fn($x, $acc) => [$x, ...$acc];
     * (Right(5)->foldRight([]))($cons);  // [5]
     * (Left("error")->foldRight([]))($cons); // []
     * ```
     *
     * @template B The accumulator type
     * @param B $initial Initial accumulator value
     * @return callable(callable(R,B):B):B Curried fold function
     */
    public function foldRight($initial): callable
    {
        return applyPartially([$initial], func_get_args(), fn(callable $f) =>
            $this->isLeft() ? $initial : $f($this->get(), $initial)
        );
    }

    /**
     * Maps each Right value to a Monoid and combines them.
     *
     * Transforms the Right value using the function and combines
     * the results using the value's Monoid instance.
     * Left values return the zero element.
     *
     * Example:
     * ```php
     * Right(5)->foldMap(fn($x) => [$x]);  // [5]
     * Left("error")->foldMap(fn($x) => [$x]); // []
     * Right("hello")->foldMap(fn($x) => $x); // "hello"
     * ```
     *
     * @template B
     * @param callable(R):B $f Function that maps to a Monoid
     * @return B Combined monoid value
     */
    public function foldMap(callable $f)
    {
        $uninitialized = uniqid("___Phunkie___");
        return $this->foldLeft(zero($this->getOrElse($uninitialized)))(function ($b, $a) use ($f, $uninitialized) {
            if ($b === $uninitialized) {
                $b = zero($a);
            }
            return combine($b, $f($a));
        });
    }

    /**
     * Catamorphism for Either - handles both cases.
     *
     * Returns a curried function that applies the appropriate
     * handler based on whether this is Left or Right.
     *
     * Example:
     * ```php
     * $handleError = fn($e) => "Error: $e";
     * $handleSuccess = fn($v) => "Success: $v";
     *
     * (Right(42)->fold($handleError))($handleSuccess);  // "Success: 42"
     * (Left("fail")->fold($handleError))($handleSuccess); // "Error: fail"
     * ```
     *
     * @template A The result type
     * @param callable(L):A $leftF Function to handle Left case
     * @return callable(callable(R):A):A Curried function for Right case
     */
    public function fold($leftF)
    {
        return applyPartially([$leftF], func_get_args(), fn(callable $rightF) =>
            $this->isLeft() ? $leftF($this->get()) : $rightF($this->get())
        );
    }
}
