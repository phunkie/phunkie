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
use Phunkie\Cats\Monad;
use Phunkie\Cats\Show;
use Phunkie\Ops\Function1\Function1ApplicativeOps;
use Phunkie\Ops\Function1\Function1EqOps;
use Phunkie\Ops\Function1\Function1MonadOps;
use Phunkie\Ops\Function1\Function1ProfunctorOps;

use function Phunkie\Functions\type\normaliseType;

/**
 * Represents a function that takes one argument.
 * 
 * Function1 is a type-safe wrapper around PHP callables that ensures the function
 * takes exactly one argument. It implements Applicative to support functional
 * composition and transformation.
 *
 * Example:
 * ```php
 * $f = Function1(fn(int $x): string => (string)($x * 2));
 * $f(21);  // "42"
 * 
 * // Function composition
 * $g = Function1('strtoupper');
 * $f->andThen($g)(21);  // "42"
 * ```
 *
 * @template A The input type
 * @template B The output type
 * @implements Kind<Function1, A, B>
 * @implements Monad<Function1, B>
 */
final class Function1 implements Kind, Monad
{
    use Function1ApplicativeOps;
    use Function1EqOps;
    use Function1MonadOps;
    use Function1ProfunctorOps;
    use Show;
    public const kind = "Function1";
    private \ReflectionFunction|\ReflectionMethod $reflection;
    private $f;

    /**
     * Creates a new Function1 from a callable.
     *
     * @param callable(A):B $f The function to wrap
     * @throws \TypeError if the callable doesn't take exactly one parameter
     */
    public function __construct(callable $f)
    {
        $this->f = $f;
        try {
            $this->reflection = method_exists($f, '__invoke') ?
                new \ReflectionMethod($f, '__invoke') :
                new \ReflectionFunction($f);
        } catch (\ReflectionException $e) {
            throw new \TypeError("Not a valid function");
        }

        $this->guardCallableNumberOfParameters();
    }

    /**
     * Returns the wrapped callable.
     *
     * @return callable(A):B The wrapped function
     */
    public function get()
    {
        return $this->f;
    }

    /**
     * Invokes the function with the given argument.
     *
     * @param A $a The input value
     * @return B The function result
     */
    public function __invoke($a)
    {
        return $this->invokeFunctionOnArg($a);
    }

    /**
     * Alias for __invoke.
     *
     * @param A $a The input value
     * @return B The function result
     */
    public function run($a)
    {
        return $this->invokeFunctionOnArg($a);
    }

    /**
     * Returns a new function that applies this function and then the given function.
     *
     * Example:
     * ```php
     * $f = Function1(fn($x) => $x * 2);
     * $g = Function1(fn($x) => (string)$x);
     * $h = $f->andThen($g);
     * $h(21); // "42"
     * ```
     *
     * @template C
     * @param callable(B):C $g The function to apply after this one
     * @return Function1<A,C> The composed function
     */
    public function andThen(callable $g): Function1
    {
        $f = $this;
        return Function1(fn ($x) => $g($f->invokeFunctionOnArg($x)));
    }

    /**
     * Returns a new function that applies the given function and then this function.
     *
     * @template C
     * @param callable(C):A $g The function to apply before this one
     * @return Function1<C,B> The composed function
     */
    public function compose(callable $g): Function1
    {
        $f = $this;
        return Function1(fn ($x) => $f->invokeFunctionOnArg(
            $g instanceof Function1 ? $g->invokeFunctionOnArg($x) : call_user_func($g, $x)
        ));
    }

    /**
     * Returns the identity function.
     *
     * Example:
     * ```php
     * $id = Function1::identity();
     * $id(42); // 42
     * ```
     *
     * @template T
     * @return Function1<T,T> A function that returns its input unchanged
     */
    public static function identity(): Function1
    {
        return Function1(fn ($x) => $x);
    }

    /**
     * Returns the identity function as the zero element for function composition.
     *
     * @return Function1<A,A> The identity function
     */
    public function zero()
    {
        return Function1::identity();
    }

    /**
     * Combines two functions through composition.
     * Alias for compose().
     *
     * @param callable $g The function to compose with
     * @return Function1 The composed function
     */
    public function combine(callable $g)
    {
        return $this->compose($g);
    }

    /**
     * Returns a memoized version of this function.
     *
     * The memoized function caches results based on input values, so repeated
     * calls with the same input return the cached result without re-executing
     * the function. This is useful for expensive computations.
     *
     * Note: Memoization uses the input value as array key, so:
     * - Works best with scalar inputs (int, string, bool)
     * - Objects are converted to string via __toString or serialization
     * - Arrays and resources are not suitable for memoization
     *
     * Example:
     * ```php
     * $expensive = Function1(function($n) {
     *     sleep(1); // Simulate expensive computation
     *     return $n * $n;
     * });
     *
     * $memoized = $expensive->memoize();
     *
     * $memoized(5); // Takes ~1 second, returns 25
     * $memoized(5); // Instant, returns cached 25
     * $memoized(6); // Takes ~1 second, returns 36
     * ```
     *
     * @return Function1<A,B> A memoized version of this function
     */
    public function memoize(): Function1
    {
        $cache = [];
        $f = $this;

        return Function1(function ($arg) use ($f, &$cache) {
            // Generate cache key
            $key = is_scalar($arg) ? $arg : (
                is_object($arg) ? (
                    method_exists($arg, '__toString') ?
                        (string)$arg :
                        spl_object_hash($arg)
                ) : serialize($arg)
            );

            // Return cached result if available
            if (array_key_exists($key, $cache)) {
                return $cache[$key];
            }

            // Compute and cache result
            $result = $f->invokeFunctionOnArg($arg);
            $cache[$key] = $result;

            return $result;
        });
    }

    /**
     * Returns a string representation of the function with its type signature.
     *
     * Example:
     * ```php
     * $f = Function1(fn(int $x): string => (string)$x);
     * echo $f->toString(); // "Function1(Int=>String)"
     * ```
     *
     * @return string The string representation
     */
    public function toString(): string
    {
        return sprintf(
            "Function1(%s=>%s)",
            normaliseType($this->reflection->getParameters()[0]->getType()->getName()) ?: "?",
            normaliseType($this->reflection->getReturnType()->getName()) ?: "?"
        );
    }

    /**
     * Returns the number of type parameters this type takes.
     *
     * @return int Always returns 2 (input and output types)
     */
    public function getTypeArity(): int
    {
        return 2;
    }

    /**
     * Returns an array of type variables for this function.
     *
     * @return array<string> [input_type, output_type]
     */
    public function getTypeVariables(): array
    {
        return [
            normaliseType($this->reflection->getParameters()[0]->getType()) ?: "?",
            normaliseType($this->reflection->getReturnType()) ?: "?"
        ];
    }

    /**
     * Returns the type representation.
     *
     * @return string Always returns "Function1"
     */
    public function showType()
    {
        return "Function1";
    }

    /**
     * Internal method to invoke the function on an argument.
     *
     * @param A $arg The argument to apply the function to
     * @return B The result of the function application
     */
    protected function invokeFunctionOnArg($arg)
    {
        return call_user_func($this->f, $arg);
    }

    /**
     * Validates that the wrapped callable takes exactly one parameter.
     *
     * @throws \TypeError if the callable doesn't take exactly one parameter
     */
    private function guardCallableNumberOfParameters()
    {
        $numberOfParameters = $this->reflection->getNumberOfParameters();
        if ($numberOfParameters != 1) {
            throw new \TypeError("Function1 takes a callable with 1 parameter. $numberOfParameters given.");
        }
    }
}
