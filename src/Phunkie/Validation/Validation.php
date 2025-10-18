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
use Phunkie\Cats\Foldable;
use Phunkie\Cats\Monad;
use Phunkie\Cats\Show;
use Phunkie\Ops\FunctorOps;
use Phunkie\Types\Kind;
use Phunkie\Types\Option;
use Phunkie\Types\NonEmptyList;
use TypeError;
use function Phunkie\Functions\semigroup\combine;
use function Phunkie\Functions\semigroup\zero;
use function Phunkie\Functions\show\showType;
use function Phunkie\PatternMatching\Referenced\Success as Valid;
use function Phunkie\PatternMatching\Referenced\Failure as Invalid;

/**
 * Represents a validation result that can be either Success or Failure.
 * 
 * Validation is a data type used for accumulating errors or processing successful values.
 * Unlike Either, Validation can accumulate errors when using applicative operations.
 * This makes it particularly useful for form validation and data processing.
 *
 * Example:
 * ```php
 * $v1 = Success(42);          // Valid value
 * $v2 = Failure("Not valid"); // Invalid with error
 * 
 * // Combine validations
 * $v1->combine($v2); // Accumulates errors if both are Failures
 * ```
 *
 * @template E The error type
 * @template A The success type
 * @implements Applicative<Validation<E,A>>
 * @implements Monad<Validation<E,A>>
 * @implements Kind<Validation<E,A>>
 * @implements Foldable<A>
 */
abstract class Validation implements Applicative, Monad, Kind, Foldable
{
    use Show;
    use FunctorOps;
    public const kind = "Validation";

    /**
     * Checks if this is a successful validation.
     *
     * @return bool True if this is Success
     */
    public function isRight(): bool { return match (true) {
        $this instanceof Failure => false,
        $this instanceof Success => true,
        default => throw new TypeError("Validation cannot be extended outside namespace") };
    }

    /**
     * Checks if this is a failed validation.
     *
     * @return bool True if this is Failure
     */
    public function isLeft(): bool { return match (true) {
        $this instanceof Success => false,
        $this instanceof Failure => true,
        default => throw new TypeError("Validation cannot be extended outside namespace") };
    }

    /**
     * Returns the number of type parameters.
     *
     * @return int Always returns 2 (error and success types)
     */
    public function getTypeArity(): int
    {
        return 2;
    }

    /**
     * Returns the type variables for this validation.
     *
     * @return array<string> ['E', 'A'] for Success<E,A>, [error_type, 'A'] for Failure
     */
    public function getTypeVariables(): array { $on = pmatch($this); return match (true) {
        $on(Valid($a)) => ['E', showType($a)],
        $on(Invalid($e)) => [showType($e), 'A']};
    }

    /**
     * Combines two validations.
     * 
     * - If both are Success, combines their values
     * - If both are Failure, combines their errors
     * - If one is Failure, returns the Failure
     *
     * @param Validation<E,A> $that The validation to combine with
     * @return Validation<E,A> The combined validation
     */
    public function combine(Validation $that): Validation { 
        $on = pmatch($this, $that); 
        return match (true) {
            $on(Valid($a), Valid($b)) => Success(Nel($a, $b)),
            $on(Invalid($x), Invalid($y)) => $this->combineFailures($this, $that),
            $on(Failure(_), _) => $this,
            $on(_) => $that 
        };
    }

    /**
     * Converts this validation to an Option.
     *
     * @return Option<A> Some(value) for Success, None for Failure
     */
    public function toOption(): Option { $on = pmatch($this); return match (true) {
        $on(Valid($a)) => Some($a),
        $on(Failure(_)) => None() };
    }

    /**
     * Returns a default value if this is a Failure.
     *
     * @template B
     * @param B $default The default value
     * @return A|B The success value or default
     */
    abstract public function getOrElse($default);

    /**
     * Maps a function over the success value.
     *
     * @template B
     * @param callable(A):B $f The function to apply
     * @return Validation<E,B> A new validation with the mapped value
     */
    abstract public function map(callable $f): Kind;

    /**
     * Invariant functor mapping.
     * For Validation, this is equivalent to map.
     *
     * @template B
     * @param callable(A):B $f The forward function
     * @param callable(B):A $g The reverse function (unused)
     * @return Validation<E,B> A new validation with the mapped value
     */
    public function imap(callable $f, callable $g): Kind
    {
        return $this->map($f);
    }

    /**
     * Left-associative fold of a validation.
     * Alias for fold().
     *
     * @template B
     * @param B $initial The initial value
     * @return callable(callable(B,A):B):B The folding function
     */
    public function foldLeft($initial)
    {
        return $this->fold($initial);
    }

    /**
     * Right-associative fold of a validation.
     * Alias for fold().
     *
     * @template B
     * @param B $initial The initial value
     * @return callable(callable(B,A):B):B The folding function
     */
    public function foldRight($initial)
    {
        return $this->fold($initial);
    }

    /**
     * Maps a function over the success value and combines the results using a monoid.
     *
     * @template B
     * @param callable(A):B $f The mapping function
     * @return B The combined result
     */
    public function foldMap(callable $f)
    {
        return ($this->foldLeft(zero($this->getOrElse(null))))(fn ($b, $a) => combine($b, $f($a)));
    }
}
