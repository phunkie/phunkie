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

use Phunkie\Types\Either;
use Phunkie\Types\Option;
use Phunkie\Utils\Traversable;
use Phunkie\Utils\WithFilter;

/**
 * Operations for Either values.
 *
 * Provides utility methods for transforming, querying, and converting Either values.
 *
 * @template L The left (error) type
 * @template R The right (success) type
 * @mixin \Phunkie\Types\Either
 */
trait EitherOps
{
    /**
     * Filters the Right value with a predicate.
     *
     * Returns Right if predicate passes, Left("") if fails.
     * Left values pass through unchanged.
     *
     * Example:
     * ```php
     * $isEven = fn($x) => $x % 2 === 0;
     * Right(42)->filter($isEven);  // Right(42)
     * Right(41)->filter($isEven);  // Left("")
     * Left("error")->filter($isEven); // Left("error")
     * ```
     *
     * @param callable(R):bool $condition Predicate function
     * @return Traversable|Either<L,R>
     */
    public function filter(callable $condition): Traversable
    {
        if ($this->isLeft()) {
            return $this;
        }
        return $condition($this->get()) ? $this : Left("");
    }

    /**
     * Creates a conditional filter for chaining.
     *
     * Allows lazy filtering in for-comprehension style syntax.
     *
     * Example:
     * ```php
     * Right(42)->withFilter(fn($x) => $x > 40)->map(fn($x) => $x * 2);
     * ```
     *
     * @param callable(R):bool $filter Predicate function
     * @return WithFilter<R>
     */
    public function withFilter(callable $filter): WithFilter
    {
        return new WithFilter($this, $filter);
    }

    /**
     * Executes a block on the Right value (for side effects).
     *
     * Only executes the block if this is a Right value.
     * Left values are ignored.
     *
     * Example:
     * ```php
     * Right(42)->withEach(fn($x) => echo $x);  // Prints: 42
     * Left("error")->withEach(fn($x) => echo $x); // Nothing happens
     * ```
     *
     * @param callable(R):void $block Function to execute
     * @return void
     */
    public function withEach(callable $block)
    {
        if ($this->isRight()) {
            $block($this->get());
        }
    }

    /**
     * Returns this Either if Right, otherwise returns the alternative.
     *
     * Provides a fallback Either if this is a Left.
     *
     * Example:
     * ```php
     * Right(42)->orElse(Right(0));           // Right(42)
     * Left("error")->orElse(Right(0));       // Right(0)
     * Left("error 1")->orElse(Left("error 2")); // Left("error 2")
     * ```
     *
     * @template L2
     * @template R2
     * @param Either<L2,R2> $alternative The fallback Either
     * @return Either<L2,R|R2>
     */
    public function orElse(Either $alternative): Either
    {
        return $this->isRight() ? $this : $alternative;
    }

    /**
     * Converts this Either to an Option.
     *
     * Right becomes Some, Left becomes None.
     * The error information in Left is lost.
     *
     * Example:
     * ```php
     * Right(42)->toOption();      // Some(42)
     * Left("error")->toOption();  // None
     * ```
     *
     * @return Option<R>
     */
    public function toOption(): Option
    {
        return $this->isRight() ? Some($this->get()) : None();
    }

    /**
     * Swaps Left and Right sides.
     *
     * Converts Right to Left and vice versa.
     * Useful for transforming error/success semantics.
     *
     * Example:
     * ```php
     * Right(42)->swap();      // Left(42)
     * Left("error")->swap();  // Right("error")
     * ```
     *
     * @return Either<R,L>
     */
    public function swap(): Either
    {
        return $this->isLeft() ? Right($this->get()) : Left($this->get());
    }

    /**
     * Maps functions over both Left and Right.
     *
     * Applies leftF to Left values, rightF to Right values.
     *
     * Example:
     * ```php
     * Right(5)->bimap(
     *     fn($e) => "Error: $e",
     *     fn($v) => $v * 2
     * );  // Right(10)
     *
     * Left("fail")->bimap(
     *     fn($e) => "Error: $e",
     *     fn($v) => $v * 2
     * );  // Left("Error: fail")
     * ```
     *
     * @template A The new left type
     * @template B The new right type
     * @param callable(L):A $leftF Function for Left value
     * @param callable(R):B $rightF Function for Right value
     * @return Either<A,B>
     */
    public function bimap(callable $leftF, callable $rightF): Either
    {
        return $this->isLeft() ? Left($leftF($this->get())) : Right($rightF($this->get()));
    }

    /**
     * Maps a function over the Left value only.
     *
     * Right values pass through unchanged.
     * Useful for transforming error messages.
     *
     * Example:
     * ```php
     * Right(42)->leftMap(fn($e) => "Error: $e");   // Right(42)
     * Left("fail")->leftMap(fn($e) => "Error: $e"); // Left("Error: fail")
     * ```
     *
     * @template A The new left type
     * @param callable(L):A $f Function to apply to Left value
     * @return Either<A,R>
     */
    public function leftMap(callable $f): Either
    {
        return $this->isLeft() ? Left($f($this->get())) : $this;
    }

    /**
     * Filters Right value with a predicate.
     *
     * If predicate fails, returns Left with the provided value.
     * Left values pass through unchanged.
     *
     * Example:
     * ```php
     * $isEven = fn($x) => $x % 2 === 0;
     * Right(42)->filterOrElse($isEven, "not even");  // Right(42)
     * Right(41)->filterOrElse($isEven, "not even");  // Left("not even")
     * Left("error")->filterOrElse($isEven, "not even"); // Left("error")
     * ```
     *
     * @template L2
     * @param callable(R):bool $predicate Function to test the Right value
     * @param L2 $leftValue Value to use if predicate fails
     * @return Either<L|L2,R>
     */
    public function filterOrElse(callable $predicate, $leftValue): Either
    {
        if ($this->isLeft()) {
            return $this;
        }
        return $predicate($this->get()) ? $this : Left($leftValue);
    }

    /**
     * Checks if Right contains the given value.
     *
     * Uses strict equality (===) for comparison.
     * Always returns false for Left.
     *
     * Example:
     * ```php
     * Right(42)->contains(42);      // true
     * Right(42)->contains(0);       // false
     * Left("error")->contains(42);  // false
     * ```
     *
     * @param R $value Value to check
     * @return bool
     */
    public function contains($value): bool
    {
        return $this->isRight() && $this->get() === $value;
    }

    /**
     * Checks if Right value satisfies the predicate.
     *
     * Returns true if Right and predicate passes.
     * Always returns false for Left.
     *
     * Example:
     * ```php
     * $isPositive = fn($x) => $x > 0;
     * Right(42)->exists($isPositive);      // true
     * Right(-1)->exists($isPositive);      // false
     * Left("error")->exists($isPositive);  // false
     * ```
     *
     * @param callable(R):bool $predicate Function to test
     * @return bool
     */
    public function exists(callable $predicate): bool
    {
        return $this->isRight() && $predicate($this->get());
    }

    /**
     * Checks if all Right values satisfy the predicate.
     *
     * Returns true if Right and predicate passes, or if Left (vacuously true).
     * Left has no value to test, so the predicate is considered satisfied.
     *
     * Example:
     * ```php
     * $isPositive = fn($x) => $x > 0;
     * Right(42)->forall($isPositive);      // true
     * Right(-1)->forall($isPositive);      // false
     * Left("error")->forall($isPositive);  // true (vacuously)
     * ```
     *
     * @param callable(R):bool $predicate Function to test
     * @return bool
     */
    public function forall(callable $predicate): bool
    {
        return $this->isLeft() || $predicate($this->get());
    }

    /**
     * Executes a function on the Right value for side effects.
     *
     * Only executes if this is a Right value.
     * Left values are ignored.
     * Similar to withEach but follows Traversable interface.
     *
     * Example:
     * ```php
     * Right(42)->foreach(fn($x) => echo $x);  // Prints: 42
     * Left("error")->foreach(fn($x) => echo $x); // Nothing happens
     * ```
     *
     * @param callable(R):void $f Function to execute
     * @return void
     */
    public function foreach(callable $f): void
    {
        if ($this->isRight()) {
            $f($this->get());
        }
    }

}
