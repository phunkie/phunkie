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
    use Phunkie\Cats\Functor\FunctorComposite;

    /**
     * Creates a composite functor from a type.
     * 
     * Allows composing multiple functors together to create
     * a new functor that combines their behaviors.
     *
     * Example:
     * ```php
     * // Compose Option and List functors
     * $f = Functor(Option(ImmList(1, 2, 3)));
     * $result = $f->map(fn($x) => $x * 2);
     * // Some(ImmList(2, 4, 6))
     * ```
     *
     * @template A
     * @param A $type The type to create functor from
     * @return FunctorComposite<A> The composite functor
     */
    function Functor($type)
    {
        return new FunctorComposite($type);
    }
}

namespace Phunkie\Functions\functor {

    use Phunkie\Cats\Functor;
    use function Phunkie\Functions\currying\applyPartially;

    /**
     * Maps a function over a functor.
     * 
     * Transforms the values inside a functor while preserving its structure.
     * Returns a new functor containing the transformed values.
     *
     * Example:
     * ```php
     * $double = fn($x) => $x * 2;
     * 
     * fmap($double)(Some(3));     // Some(6)
     * fmap($double)(ImmList(1,2)); // ImmList(2,4)
     * ```
     *
     * @template A,B
     * @param callable(A):B $f Function to apply
     * @return callable(Functor<A>):Functor<B> Function expecting functor
     */
    const fmap = "\\Phunkie\\Functions\\functor\\fmap";
    function fmap(callable $f)
    {
        return applyPartially([$f], func_get_args(), fn (Functor $functor) => $functor->map($f));
    }

    /**
     * Replaces all values in a functor with a constant value.
     * 
     * Maps over a functor, ignoring the original values and replacing
     * them all with the given value.
     *
     * Example:
     * ```php
     * allAs(42)(Some("hello"));     // Some(42)
     * allAs(true)(ImmList(1,2,3));  // ImmList(true,true,true)
     * ```
     *
     * @template A,B
     * @param B $b Value to replace with
     * @return callable(Functor<A>):Functor<B> Function expecting functor
     */
    const allAs = "\\Phunkie\\Functions\\functor\\allAs";
    function allAs($b)
    {
        return applyPartially([$b], func_get_args(), fn (Functor $functor) => $functor->as($b));
    }

    /**
     * Discards values in a functor, replacing them with Unit.
     * 
     * Maps over a functor, replacing all values with Unit while
     * preserving the structure.
     *
     * Example:
     * ```php
     * asVoid(Some(42));        // Some(Unit)
     * asVoid(ImmList(1,2,3));  // ImmList(Unit,Unit,Unit)
     * ```
     *
     * @template A
     * @param Functor<A> $functor The functor to void
     * @return Functor<Unit> Functor containing only Unit
     */
    const asVoid = "\\Phunkie\\Functions\\functor\\asVoid";
    function asVoid(Functor $functor)
    {
        return $functor->void();
    }

    /**
     * Maps a function over a functor, collecting results with another function.
     * 
     * Like map but also provides access to a collecting function that
     * can combine results in custom ways.
     *
     * Example:
     * ```php
     * $f = fn($x) => $x * 2;
     * $collect = fn($orig, $mapped) => Pair($orig, $mapped);
     * 
     * zipWith($collect)($f)(Some(3));
     * // Some(Pair(3, 6))
     * ```
     *
     * @template A,B
     * @param callable $f Function to apply
     * @return callable Function expecting functor
     */
    const zipWith = "\\Phunkie\\Functions\\functor\\zipWith";
    function zipWith($f)
    {
        return applyPartially([$f], func_get_args(), fn (Functor $functor) => $functor->zipWith($f));
    }
}
