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
use Phunkie\Types\Option;

/**
 * A monad transformer that combines Option with another monad F.
 * 
 * OptionT allows you to work with nested structures of the form F<Option<A>>
 * (like List<Option<A>>) as if they were a single monad. This makes it easier
 * to compose computations that might fail within another effect.
 *
 * Example:
 * ```php
 * // Without OptionT
 * $users = ImmList(Some(1), None(), Some(2));
 * $result = $users->map(fn($id) => $id->map(fn($x) => $x + 1));
 * // ImmList(Some(2), None(), Some(3))
 *
 * // With OptionT
 * $users = OptionT(ImmList(Some(1), None(), Some(2)));
 * $result = $users->map(fn($x) => $x + 1);
 * // OptionT(ImmList(Some(2), None(), Some(3)))
 * ```
 *
 * @template F The outer monad
 * @template A The value type
 * @implements Kind<OptionT,A>
 */
class OptionT implements Kind
{
    public const kind = "OptionT";

    /**
     * @var Kind<F,Option<A>>
     */
    private $monad;

    /**
     * Creates a new OptionT wrapping a monadic value containing Options.
     *
     * @param Kind<F,Option<A>> $monad The wrapped monadic value
     */
    public function __construct(Kind $monad)
    {
        $this->monad = $monad;
    }

    /**
     * Gets the underlying monadic value.
     *
     * @return Kind<F,Option<A>> The wrapped value
     */
    public function getValue(): Kind
    {
        return $this->monad;
    }

    /**
     * Maps a function over the Option value inside the monad F.
     *
     * @template B
     * @param callable(A):B $f The function to apply
     * @return OptionT<F,B> A new OptionT with transformed values
     */
    public function map(callable $f): OptionT
    {
        return new OptionT(
            $this->monad->map(fn(Option $o) => $o->map($f))
        );
    }

    /**
     * Chains OptionT computations.
     *
     * @template B
     * @param callable(A):OptionT<F,B> $f Function returning another OptionT
     * @return OptionT<F,B> The composed computation
     */
    public function flatMap(callable $f): OptionT
    {
        return OptionT($this->monad->flatMap(fn (Option $o) => $o->map(
            fn ($a) => $f($a)->monad
        )->getOrElse($this->monad->pure(None()))));
    }

    /**
     * Checks if the Option values are defined.
     *
     * @return Kind<F,bool> A monadic value containing boolean results
     */
    public function isDefined(): Kind
    {
        return $this->monad->map(fn(Option $o) => $o->isDefined());
    }

    /**
     * Checks if the Option values are empty.
     *
     * @return Kind<F,bool> A monadic value containing boolean results
     */
    public function isEmpty(): Kind
    {
        return $this->monad->map(fn(Option $o) => $o->isEmpty());
    }

    /**
     * Gets the values or a default if None.
     *
     * @param A $default The default value
     * @return Kind<F,A> A monadic value containing the results
     */
    public function getOrElse($default): Kind
    {
        return $this->monad->map(fn(Option $o) => $o->getOrElse($default));
    }

    /**
     * Returns the number of type parameters.
     *
     * @return int Always returns 2 (F and A)
     */
    public function getTypeArity(): int
    {
        return 2;
    }

    /**
     * Returns the type variables for this Kind.
     *
     * @return array<string> Array containing the type variables
     */
    public function getTypeVariables(): array
    {
        return ['F', 'A'];
    }
}
