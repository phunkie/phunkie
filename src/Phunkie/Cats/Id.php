<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\Cats;

use Phunkie\Types\Kind;
use Phunkie\Types\Unit;
use Phunkie\Types\Pair;
use function Phunkie\Functions\semigroup\combine;

/**
 * Identity functor that behaves like a function.
 * 
 * Id is a special functor that wraps a value and behaves like a function returning that value.
 * Unlike typical functors that wrap their mapped results, Id returns raw values directly.
 * This makes it useful for testing and as a neutral element in transformations.
 *
 * Laws:
 * 1. Left identity: pure(a).flatMap(f) == f(a)
 * 2. Right identity: m.flatMap(pure) == m
 * 3. Associativity: m.flatMap(f).flatMap(g) == m.flatMap(x => f(x).flatMap(g))
 *
 * Example:
 * ```php
 * $id = new Id(42);
 * $id();                   // 42 (acts like a function)
 * $id->map(fn($x) => $x * 2);     // 84 (returns raw value)
 * $id->flatMap(fn($x) => new Id($x + 1))(); // 43
 * ```
 *
 * @template A
 * @implements Kind<Id,A>
 */
class Id implements Kind
{
    public const kind = "Id";
    
    private $value;

    /**
     * Creates a new Id instance.
     *
     * @param A $value The value to wrap
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * Makes Id behave like a function returning its value.
     * This allows Id to be used both as a functor and as a function.
     *
     * @return A The wrapped value
     */
    public function __invoke()
    {
        return $this->value;
    }

    /**
     * Maps a function over the wrapped value.
     *
     * @template B
     * @param callable(A):B $f The function to apply
     * @return B the transformed value
     */
    public function map(callable $f): mixed
    {
        return $f($this());
    }

    /**
     * Chains Id computations.
     *
     * @template B
     * @param callable(A):Id<B> $f The function returning a new Id
     * @return B The result of applying and flattening
     */
    public function flatMap(callable $f): mixed
    {
        return ($f($this()))();
    }

    /**
     * Lifts a pure value into Id.
     *
     * @template B
     * @param B $a The value to lift
     * @return Id<B> The value wrapped in Id
     */
    public function pure($a): Applicative
    {
        return new Id($a);
    }

    /**
     * Applies a wrapped function to this Id value.
     *
     * @template B
     * @param Kind<Id,callable(A):B> $f The wrapped function
     * @return B The result of applying the function
     */
    public function apply(Kind $f): mixed
    {
        return $f->get()($this());
    }

    /**
     * Maps a binary function over two Id values.
     *
     * @template B
     * @template C
     * @param Kind<Id,B> $fb Second Id value
     * @param callable(A,B):C $f Function to apply
     * @return C Result of applying f to both values
     */
    public function map2(Kind $fb, callable $f): mixed
    {
        return $f($this(), $fb->get());
    }

    /**
     * Lifts a function to operate on Id values.
     *
     * @template B
     * @param callable(A):B $f The function to lift
     * @return callable(Id<A>):B The lifted function
     */
    public function lift($f): callable
    {
        return fn($fa) => $fa->map($f);
    }

    /**
     * Replaces the contents with a constant value.
     *
     * @template B
     * @param B $b The value to replace with
     * @return Id<B> A new Id containing only $b
     */
    public function as($b): Kind
    {
        return new Id($b);
    }

    /**
     * Discards the contents, replacing with Unit.
     *
     * @return Id<Unit> A new Id containing Unit
     */
    public function void(): Kind
    {
        return $this->as(Unit());
    }

    /**
     * Maps a function and pairs results with original values.
     *
     * @template B
     * @param callable(A):B $f The function to apply
     * @return Id<Pair<A,B>> A new Id containing pairs
     */
    public function zipWith($f): Kind
    {
        return new Id(Pair($this(), $f($this())));
    }

    /**
     * Flattens a nested Id structure.
     *
     * @return B where B is the type parameter of the inner Id
     */
    public function flatten(): mixed
    {
        return $this();
    }

    /**
     * Maps a pair of functions over this Id.
     *
     * @template B
     * @param callable(A):B $f The forward function
     * @param callable(B):A $g The reverse function
     * @return B The transformed value
     */
    public function imap(callable $f, callable $g): Kind
    {
        return $this->map($f);
    }

    /**
     * Composes this Id with another value using combine.
     * Applies the combine operation with this value first.
     *
     * @param mixed $b The value to combine with
     * @return mixed The result of combining both values
     */
    public function andThen($b)
    {
        return combine($this(), $b);
    }

    /**
     * Composes this Id with another value using combine.
     * Applies the combine operation with the other value first.
     *
     * @param mixed $b The value to combine with
     * @return mixed The result of combining both values
     */
    public function compose($b)
    {
        return combine($b, $this());
    }

    /**
     * Returns the number of type parameters.
     * Id is a type constructor with one parameter.
     *
     * @return int Always returns 1
     */
    public function getTypeArity(): int
    {
        return 1;
    }

    /**
     * Returns the type variables for this Kind.
     *
     * @return array<string> Array containing the single type variable
     */
    public function getTypeVariables(): array
    {
        return ['A'];
    }
}
