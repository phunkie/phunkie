<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\Types;

use Phunkie\Cats\Applicative;
use Phunkie\Cats\Foldable;
use Phunkie\Cats\Monad;
use Phunkie\Cats\Show;
use Phunkie\Ops\Either\EitherApplicativeOps;
use Phunkie\Ops\Either\EitherEqOps;
use Phunkie\Ops\Either\EitherFoldableOps;
use Phunkie\Ops\Either\EitherMonadOps;
use Phunkie\Ops\Either\EitherMonoidOps;
use Phunkie\Ops\Either\EitherOps;
use Phunkie\Utils\Traversable;
use function Phunkie\Functions\show\showType;
use function Phunkie\Functions\show\showValue;

/**
 * A type that represents a value of one of two possible types (a disjoint union).
 *
 * Either is commonly used for error handling where:
 * - Left represents an error or failure case
 * - Right represents a success case
 *
 * Example:
 * ```php
 * $success = Right(42);              // Right(42)
 * $failure = Left("error");          // Left("error")
 *
 * $result = $success->map(fn($x) => $x * 2);  // Right(84)
 * $error = $failure->map(fn($x) => $x * 2);   // Left("error")
 * ```
 *
 * @template L The left (error) type
 * @template R The right (success) type
 * @implements Kind<Either<L,R>>
 * @implements Applicative<Either<L,R>>
 * @implements Monad<Either<L,R>>
 * @implements Foldable<R>
 */
abstract class Either implements Kind, Applicative, Monad, Foldable, Traversable
{
    use Show;
    use EitherApplicativeOps;
    use EitherEqOps;
    use EitherFoldableOps;
    use EitherMonadOps;
    use EitherMonoidOps;
    use EitherOps;
    public const kind = "Either";
    protected $value;

    /**
     * Protected constructor for Either types.
     *
     * This constructor is used internally by Left and Right classes.
     * To create Eithers, use the global Left() or Right() functions:
     * ```php
     * $right = Right("value");   // Right("value")
     * $left = Left("error");     // Left("error")
     * ```
     *
     * @param mixed $value The value to wrap
     * @throws \Error if attempting to create an Either that is neither Left nor Right
     */
    final protected function __construct($value)
    {
        if ($this instanceof Right || $this instanceof Left) {
            $this->value = $value;
        } else {
            throw new \Error("Either can only be Left or Right");
        }
    }

    /**
     * Returns the value if Right, otherwise returns the provided default.
     *
     * Example:
     * ```php
     * Right(42)->getOrElse(0);   // 42
     * Left("error")->getOrElse(0);  // 0
     * ```
     *
     * @template T
     * @param T $default The default value
     * @return R|T The contained value or default
     */
    abstract public function getOrElse($default);

    /**
     * Checks if this Either is a Right.
     *
     * @return bool true if this is a Right, false if this is Left
     */
    abstract public function isRight(): bool;

    /**
     * Checks if this Either is a Left.
     *
     * @return bool true if this is Left, false if this is a Right
     */
    abstract public function isLeft(): bool;

    /**
     * Returns a string representation of this Either.
     *
     * Example:
     * ```php
     * Right(42)->toString();      // "Right(42)"
     * Left("error")->toString();  // "Left(error)"
     * ```
     *
     * @return string The string representation
     */
    public function toString(): string
    {
        $prefix = $this->isRight() ? "Right" : "Left";
        return $prefix . "(" . showValue($this->value) . ")";
    }

    /**
     * Magic method for string conversion.
     *
     * @return string The string representation
     */
    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * Returns the number of type parameters this type takes.
     *
     * @return int Always returns 2 (left and right types)
     */
    public function getTypeArity(): int
    {
        return 2;
    }

    /**
     * Returns an array of type variables for this either.
     *
     * Example:
     * ```php
     * Right(42)->getTypeVariables();      // ["L", "Int"]
     * Left("error")->getTypeVariables();  // ["String", "R"]
     * ```
     *
     * @return array<string> [left_type, "R"] for Left, ["L", right_type] for Right
     */
    public function getTypeVariables(): array
    {
        return $this->isRight()
            ? ["L", showType($this->value)]
            : [showType($this->value), "R"];
    }

    /**
     * Gets the wrapped value (use with caution).
     *
     * @return L|R The wrapped value
     */
    public function get()
    {
        return $this->value;
    }
}
