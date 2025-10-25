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

use Phunkie\Types\Either;
use Phunkie\Types\Kind;

/**
 * A monad transformer that combines Either with another monad F.
 *
 * EitherT allows you to work with nested structures of the form F<Either<L,R>>
 * (like IO<Either<Error,Value>>) as if they were a single monad. This makes it
 * easier to compose computations that might fail within another effect.
 *
 * Example:
 * ```php
 * // Without EitherT
 * $io = IO(fn() => divide(10, 0));  // IO<Either<String,Int>>
 * $result = $io->map(fn($either) => $either->map(fn($x) => $x * 2));
 *
 * // With EitherT
 * $io = EitherT(IO(fn() => divide(10, 2)));  // EitherT<IO,String,Int>
 * $result = $io->map(fn($x) => $x * 2);      // EitherT<IO,String,Int>
 * ```
 *
 * @template F The outer monad
 * @template L The left (error) type
 * @template R The right (success) type
 * @implements Kind<EitherT,R>
 */
class EitherT implements Kind
{
    const kind = "EitherT";

    /**
     * @var Kind<F,Either<L,R>>
     */
    private $monad;

    /**
     * Creates a new EitherT wrapping a monadic value containing Eithers.
     *
     * @param Kind<F,Either<L,R>> $monad The wrapped monadic value
     */
    public function __construct(Kind $monad)
    {
        $this->monad = $monad;
    }

    /**
     * Gets the underlying monadic value.
     *
     * @return Kind<F,Either<L,R>> The wrapped value
     */
    public function getValue(): Kind
    {
        return $this->monad;
    }

    /**
     * Maps a function over the Right value inside the monad F.
     *
     * @template B
     * @param callable(R):B $f The function to apply
     * @return EitherT<F,L,B> A new EitherT with transformed values
     */
    public function map(callable $f): EitherT
    {
        return new EitherT(
            $this->monad->map(fn(Either $e) => $e->map($f))
        );
    }

    /**
     * Chains EitherT computations.
     *
     * @template B
     * @param callable(R):EitherT<F,L,B> $f Function returning another EitherT
     * @return EitherT<F,L,B> The composed computation
     */
    public function flatMap(callable $f): EitherT
    {
        return EitherT($this->monad->flatMap(function (Either $e) use ($f) {
            return $e->isLeft()
                ? $this->monad->pure($e)
                : $f($e->get())->monad;
        }));
    }

    /**
     * Checks if the Either values are Right.
     *
     * @return Kind<F,bool> A monadic value containing boolean results
     */
    public function isRight(): Kind
    {
        return $this->monad->map(fn(Either $e) => $e->isRight());
    }

    /**
     * Checks if the Either values are Left.
     *
     * @return Kind<F,bool> A monadic value containing boolean results
     */
    public function isLeft(): Kind
    {
        return $this->monad->map(fn(Either $e) => $e->isLeft());
    }

    /**
     * Gets the Right values or a default if Left.
     *
     * @param R $default The default value
     * @return Kind<F,R> A monadic value containing the results
     */
    public function getOrElse($default): Kind
    {
        return $this->monad->map(fn(Either $e) => $e->getOrElse($default));
    }

    /**
     * Folds the Either values.
     *
     * @template B
     * @param callable(L):B $leftF Function for Left values
     * @param callable(R):B $rightF Function for Right values
     * @return Kind<F,B> A monadic value containing the folded results
     */
    public function fold(callable $leftF, callable $rightF): Kind
    {
        return $this->monad->map(fn(Either $e) =>
            ($e->fold($leftF))($rightF)
        );
    }

    /**
     * Swaps Left and Right in the wrapped Eithers.
     *
     * @return EitherT<F,R,L> A new EitherT with swapped sides
     */
    public function swap(): EitherT
    {
        return new EitherT(
            $this->monad->map(fn(Either $e) => $e->swap())
        );
    }

    /**
     * Maps over both Left and Right values.
     *
     * @template A
     * @template B
     * @param callable(L):A $leftF Function for Left values
     * @param callable(R):B $rightF Function for Right values
     * @return EitherT<F,A,B> A new EitherT with mapped values
     */
    public function bimap(callable $leftF, callable $rightF): EitherT
    {
        return new EitherT(
            $this->monad->map(fn(Either $e) => $e->bimap($leftF, $rightF))
        );
    }

    /**
     * Maps over Left values only.
     *
     * @template A
     * @param callable(L):A $f Function for Left values
     * @return EitherT<F,A,R> A new EitherT with mapped Left values
     */
    public function leftMap(callable $f): EitherT
    {
        return new EitherT(
            $this->monad->map(fn(Either $e) => $e->leftMap($f))
        );
    }

    /**
     * Converts to OptionT, discarding Left values.
     *
     * @return OptionT<F,R> An OptionT with Right values as Some, Left as None
     */
    public function toOptionT(): OptionT
    {
        return new OptionT(
            $this->monad->map(fn(Either $e) => $e->toOption())
        );
    }

    /**
     * Creates an EitherT from an Option in a monad.
     * None becomes Left with the provided error.
     *
     * @template F,L,R
     * @param Kind<F,Option<R>> $monad Monad containing Option
     * @param L $leftValue Value to use for None case
     * @return EitherT<F,L,R> The resulting EitherT
     */
    public static function fromOptionT(Kind $monad, $leftValue): EitherT
    {
        return new EitherT(
            $monad->map(fn($o) => $o->isDefined() ? Right($o->get()) : Left($leftValue))
        );
    }

    /**
     * Lifts a value into EitherT as a Right.
     *
     * @template F,L,R
     * @param Kind $monad The base monad instance (for pure)
     * @param R $value The value to lift
     * @return EitherT<F,L,R> EitherT containing Right(value)
     */
    public static function pure(Kind $monad, $value): EitherT
    {
        return new EitherT($monad->pure(Right($value)));
    }

    /**
     * Lifts a Left value into EitherT.
     *
     * @template F,L,R
     * @param Kind $monad The base monad instance (for pure)
     * @param L $error The error value to lift
     * @return EitherT<F,L,R> EitherT containing Left(error)
     */
    public static function left(Kind $monad, $error): EitherT
    {
        return new EitherT($monad->pure(Left($error)));
    }

    /**
     * Returns the number of type parameters.
     *
     * @return int Always returns 3 (F, L, and R)
     */
    public function getTypeArity(): int
    {
        return 3;
    }

    /**
     * Returns the type variables for this Kind.
     *
     * @return array<string> Array containing the type variables
     */
    public function getTypeVariables(): array
    {
        return ['F', 'L', 'R'];
    }
}
