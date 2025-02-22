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
use const Phunkie\Functions\function1\identity;

/**
 * Represents a successful validation result.
 * 
 * Success wraps a valid value and implements validation operations that
 * preserve or transform that value.
 *
 * Example:
 * ```php
 * $success = Success(42);
 * $success->map(fn($x) => $x * 2);     // Success(84)
 * $success->getOrElse(0);              // 42
 * ```
 *
 * @template E The error type (unused in Success)
 * @template A The success value type
 * @extends Validation<E,A>
 */
final class Success extends Validation
{
    private $valid;

    /**
     * Creates a new Success with a valid value.
     *
     * @param A $valid The valid value
     */
    public function __construct($valid)
    {
        $this->valid = $valid;
    }

    /**
     * Returns a string representation.
     *
     * @return string "Success(value)"
     */
    public function toString(): string
    {
        return "Success(" . showValue($this->valid) . ")";
    }

    /**
     * Returns the success value, ignoring the default.
     *
     * @template B
     * @param B $default Unused default value
     * @return A The success value
     */
    public function getOrElse($default)
    {
        return $this->valid;
    }

    /**
     * Returns this Success, ignoring the alternative.
     *
     * @param Validation<E,A> $ignored Alternative validation (unused)
     * @return Success<E,A> This Success
     */
    public function orElse(Validation $ignored)
    {
        return $this;
    }

    /**
     * Maps a function over the success value.
     *
     * @template B
     * @param callable(A):B $f The function to apply
     * @return Success<E,B> A new Success with the transformed value
     */
    public function map(callable $f): Kind
    {
        return Success($f($this->valid));
    }

    /**
     * Folds over the success value.
     *
     * @template B
     * @param callable $fe Unused error handler
     * @return callable(callable(A):B):B Function that applies success handler
     */
    public function fold($fe)
    {
        return fn ($fa) => $fa($this->valid);
    }

    /**
     * Flattens a nested validation.
     *
     * @return Kind The inner validation
     */
    public function flatten(): Kind
    {
        return $this->flatMap(identity);
    }

    /**
     * Chains validation operations.
     *
     * @template B
     * @param callable(A):Validation<E,B> $f The function to apply
     * @return Validation<E,B> The result of applying f to the success value
     */
    public function flatMap(callable $f): Kind
    {
        return $f($this->valid);
    }

    /**
     * Applies a function wrapped in a validation.
     *
     * @template B
     * @param Kind<Validation<E,callable(A):B>> $f The function to apply
     * @return Validation<E,B> The result of applying the function
     */
    public function apply(Kind $f): Kind { return match (true) {
        $f instanceof Success && is_callable($f->valid)
            => Success(($f->valid)($this->valid)),
        $f instanceof Failure && !is_callable(($f->fold(identity))(_))
            => $f };
    }

    /**
     * Creates a new Success containing the given value.
     *
     * @template B
     * @param B $a The value to wrap
     * @return Success<E,B> A new Success
     */
    public function pure($a): Applicative
    {
        return Success($a);
    }

    /**
     * Maps a binary function over two validations.
     *
     * @template B
     * @template C
     * @param Kind<Validation<E,B>> $fb Second validation
     * @param callable(A,B):C $f Function to apply
     * @return Validation<E,C> Result of applying f to both values
     */
    public function map2(Kind $fb, callable $f): Kind
    {
        return $this->apply($fb->map(fn ($b) => fn ($a) => $f($a, $b)));
    }
}
