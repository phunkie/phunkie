<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace {

    use Phunkie\Types\Option;
    use Phunkie\Validation\Failure;
    use Phunkie\Validation\Success;
    use Phunkie\Validation\Validation;
    use function Phunkie\Functions\currying\applyPartially;

    /**
     * Creates a Success validation.
     * 
     * Represents a successful validation result containing a value.
     * Similar to Right in Either.
     *
     * Example:
     * ```php
     * Success(42);                // Success(42)
     * Success("valid input");     // Success("valid input")
     * 
     * // Common usage in validation
     * function validateAge($age) {
     *     return $age >= 0 ? Success($age) : 
     *         Failure("Age must be non-negative");
     * }
     * ```
     *
     * @template A
     * @param A $value The success value
     * @return Success<A> Successful validation
     */
    function Success($value): Success
    {
        return new Success($value);
    }

    /**
     * Creates a Failure validation.
     * 
     * Represents a failed validation with error message(s).
     * Can accumulate multiple errors when combined.
     *
     * Example:
     * ```php
     * Failure("Invalid input");   // Failure("Invalid input")
     * Failure(Nel("Error 1"));    // Failure(Nel("Error 1"))
     * 
     * // Combining failures accumulates errors
     * $f1 = Failure(Nel("Error 1"));
     * $f2 = Failure(Nel("Error 2"));
     * $f1->combine($f2);  // Failure(Nel("Error 1", "Error 2"))
     * ```
     *
     * @template A
     * @param string|Nel<string> $error Error message or non-empty list of errors
     * @return Failure<A> Failed validation
     */
    function Failure($error): Failure
    {
        return new Failure($error);
    }

    /**
     * Creates a Failure validation with a non-empty list of errors.
     * 
     * Helper function to create a Failure containing multiple error messages.
     *
     * Example:
     * ```php
     * FailureNel("Error 1", "Error 2");  // Failure(Nel("Error 1", "Error 2"))
     * ```
     *
     * @param string ...$e Error messages
     * @return Failure<Nel<string>> Failure with non-empty list of errors
     */
    function FailureNel(...$e)
    {
        return Nel(...$e)->failure();
    }

    /**
     * Creates a Success validation with a non-empty list of values.
     * 
     * Helper function to create a Success containing multiple values.
     *
     * Example:
     * ```php
     * SuccessNel(1, 2, 3);  // Success(Nel(1, 2, 3))
     * ```
     *
     * @template A
     * @param A ...$a Values to include
     * @return Success<Nel<A>> Success with non-empty list of values
     */
    function SuccessNel(...$a)
    {
        return Nel(...$a)->success();
    }

    /**
     * Creates a validation based on an optional value.
     * 
     * Returns Success if value exists, Failure with message if None/null.
     *
     * Example:
     * ```php
     * Either("User not found")(Some($user));  // Success($user)
     * Either("User not found")(None());       // Failure("User not found")
     * Either("Missing value")(null);          // Failure("Missing value")
     * ```
     *
     * @template A
     * @param string $message Error message for None/null case
     * @return callable(Option<A>|null):Validation<A,string> Function expecting optional value
     */
    function Either($message)
    {
        return applyPartially([$message], func_get_args(), function ($result) use ($message) {
            if (($result instanceof Option && $result == None()) || $result === null) {
                return Failure($message);
            }
            return Success($result);
        });
    }

    /**
     * Wraps a throwing function in a validation.
     * 
     * Returns Success with result if function succeeds,
     * Failure with exception if function throws.
     *
     * Example:
     * ```php
     * $parse = fn() => json_decode($json, true, 512, JSON_THROW_ON_ERROR);
     * Attempt($parse);  // Success($data) or Failure($jsonException)
     * ```
     *
     * @template A
     * @param callable():A $f Function that might throw
     * @return Validation<A,\Throwable> Validation result
     */
    function Attempt(callable $f): Validation
    {
        try {
            $a = $f();
            return Success($a);
        } catch (\Throwable $e) {
            return Failure($e);
        }
    }
}

namespace Phunkie\Functions\validation {

    use Phunkie\Types\Option;
    use Phunkie\Validation\Validation;
    use function Phunkie\Functions\semigroup\combine;
    use function Phunkie\PatternMatching\Referenced\Success as Valid;
    use function Failure as Invalid;

    /**
     * Combines multiple validations into one.
     * 
     * Accumulates all failures if any validation fails,
     * or combines successes if all succeed.
     *
     * Example:
     * ```php
     * $v1 = Success(1);
     * $v2 = Success(2);
     * $v3 = Failure("Error");
     * apply($v1, $v2);      // Success(3)
     * apply($v1, $v3);      // Failure("Error")
     * ```
     *
     * @template A
     * @param Validation<A,string> ...$validations Validations to combine
     * @return Validation<A,string> Combined validation
     */
    function apply(...$validations)
    {
        return call_user_func(
            ImmList(...$validations)->foldLeft(Unit()),
            fn ($x, $y) => combine($x, $y)
        );
    }

    /**
     * Converts a validation to an option.
     * 
     * Returns Some with value for Success,
     * None for Failure.
     *
     * Example:
     * ```php
     * toOption(Success(42));         // Some(42)
     * toOption(Failure("error"));    // None
     * ```
     *
     * @template A
     * @param Validation<A,mixed> $v Validation to convert
     * @return Option<A> Optional value
     */
    const toOption = "\\Phunkie\\Functions\\validation\\toOption";
    function toOption(Validation $v): Option { $on = pmatch($v); return match (true) {
        $on(Valid($a)) => Some($a),
        $on(Invalid(_)) => None() };
    }

    /**
     * Validates a value with a predicate.
     * 
     * Returns Success if predicate is true, Failure otherwise.
     * Useful for simple validations.
     *
     * Example:
     * ```php
     * $isPositive = fn($x) => $x > 0;
     * validate(5, $isPositive, "Must be positive");     // Success(5)
     * validate(-1, $isPositive, "Must be positive");    // Failure("Must be positive")
     * ```
     *
     * @template A
     * @param A $value Value to validate
     * @param callable(A):bool $predicate Validation function
     * @param string $error Error message if validation fails
     * @return Validation<A,string> Validation result
     */
    function validate($value, callable $predicate, string $error): Validation
    {
        return $predicate($value) ? Success($value) : Failure($error);
    }
}
