<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\Validation;

use Phunkie\Cats\Applicative;
use Phunkie\Types\Kind;
use function Phunkie\Functions\show\showValue;

/**
 * Represents a failed validation result.
 * 
 * Failure wraps an error value and implements validation operations that
 * preserve or combine errors. When combined with other Failures, their
 * errors are accumulated.
 *
 * Example:
 * ```php
 * $failure = Failure("Invalid input");
 * $failure->map(fn($x) => $x * 2);     // Failure("Invalid input")
 * $failure->getOrElse("default");      // "default"
 * 
 * // Error accumulation
 * $f1 = Failure("Error 1");
 * $f2 = Failure("Error 2");
 * $f1->combine($f2);                   // Failure("Error 1Error 2")
 * ```
 *
 * @template E The error type
 * @template A The success type (unused in Failure)
 * @extends Validation<E,A>
 */
final class Failure extends Validation
{
    private $invalid;

    /**
     * Creates a new Failure with an error value.
     *
     * @param E $invalid The error value
     */
    public function __construct($invalid)
    {
        $this->invalid = $invalid;
    }

    /**
     * Returns a string representation.
     *
     * @return string "Failure(error)"
     */
    public function toString(): string
    {
        return "Failure(" . showValue($this->invalid) . ")";
    }

    /**
     * Returns the default value, ignoring the error.
     *
     * @template B
     * @param B $default The default value
     * @return B The default value
     */
    public function getOrElse($default)
    {
        return $default;
    }

    /**
     * Returns the alternative validation, ignoring this Failure.
     *
     * @param Validation<E,A> $another Alternative validation
     * @return Validation<E,A> The alternative validation
     */
    public function orElse(Validation $another)
    {
        return $another;
    }

    /**
     * Preserves the error, ignoring the mapping function.
     *
     * @template B
     * @param callable(A):B $f Unused mapping function
     * @return Failure<E,B> This Failure
     */
    public function map(callable $f): Kind
    {
        return $this;
    }

    /**
     * Folds over the error value.
     *
     * @template B
     * @param callable(E):B $fe Error handler
     * @return callable(callable):B Function that applies error handler
     */
    public function fold($fe)
    {
        return fn ($fa) => $fe($this->invalid);
    }

    /**
     * Returns the error value.
     *
     * @return E The error value
     */
    public function flatten(): Kind
    {
        return $this->invalid;
    }

    /**
     * Preserves the error, ignoring the function.
     *
     * @template B
     * @param callable(A):Validation<E,B> $f Unused function
     * @return Failure<E,B> This Failure
     */
    public function flatMap(callable $f): Kind
    {
        return $this;
    }

    /**
     * Applies a function if possible, otherwise preserves the error.
     *
     * @template B
     * @param Kind<Validation<E,callable(A):B>> $f The function to apply
     * @return Validation<E,B> The result or this Failure
     */
    public function apply(Kind $f): Kind
    {
        if ($f instanceof Failure && is_callable($f->invalid)) {
            return Failure(($f->invalid)($this->invalid));
        }
        return $this;
    }

    /**
     * Creates a new Failure containing the given error.
     *
     * @template B
     * @param B $e The error value
     * @return Failure<B,A> A new Failure
     */
    public function pure($e): Applicative
    {
        return Failure($e);
    }

    /**
     * Preserves the error, ignoring the function and second validation.
     *
     * @template B
     * @template C
     * @param Kind<Validation<E,B>> $fb Unused second validation
     * @param callable(A,B):C $f Unused function
     * @return Failure<E,C> This Failure
     */
    public function map2(Kind $fb, callable $f): Kind
    {
        return $this;
    }
}
