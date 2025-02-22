<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\Laws;

use Phunkie\Types\Function1;
use Phunkie\Types\Kind;
use Phunkie\Types\Option;
use const Phunkie\Functions\function1\identity;

/**
 * Laws that every Functor must satisfy.
 * 
 * A functor is a type that implements a map operation which preserves:
 * - Structure of the container
 * - Composition of functions
 *
 * These laws ensure that mapping behaves consistently:
 * 
 * 1. Identity:     fa.map(id) = fa
 * 2. Composition:  fa.map(f).map(g) = fa.map(f andThen g)
 *
 * Example:
 * ```php
 * class ListFunctor implements Kind {
 *     use FunctorLaws;
 * 
 *     public function test(): void {
 *         $list = ImmList(1, 2, 3);
 *         
 *         // Verify identity law
 *         assert($this->covariantIdentity(
 *             $list,
 *             Some(42)  // For equality comparison
 *         ));
 * 
 *         // Verify composition law
 *         assert($this->covariantComposition(
 *             $list,
 *             Function1(fn($x) => $x + 1),
 *             Function1(fn($x) => $x * 2)
 *         ));
 *     }
 * }
 * ```
 */
trait FunctorLaws
{
    /**
     * Identity law: fa.map(id) = fa
     * 
     * Mapping the identity function over a functor should return
     * an equivalent functor.
     *
     * @param Kind $fa The functor to test
     * @param Option $forArg Value for equality comparison
     * @return bool True if the law holds
     */
    public function covariantIdentity(Kind $fa, Option $forArg): bool
    {
        return $fa->eqv($fa->map(identity), $forArg);
    }

    /**
     * Composition law: fa.map(f).map(g) = fa.map(f andThen g)
     * 
     * Mapping two functions in sequence should be the same as
     * mapping their composition.
     *
     * @param Kind $fa The functor to test
     * @param Function1 $f First function to compose
     * @param Function1 $g Second function to compose
     * @return bool True if the law holds
     */
    public function covariantComposition(Kind $fa, Function1 $f, Function1 $g): bool
    {
        return $fa->map($f)->map($g)->eqv($fa->map($f->andThen($g)), Some(42));
    }
}
