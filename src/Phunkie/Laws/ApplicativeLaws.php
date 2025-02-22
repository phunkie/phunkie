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

use Phunkie\Cats\Applicative;
use Phunkie\Algebra\Eq;
use const Phunkie\Functions\function1\identity;

/**
 * Laws that every Applicative functor must satisfy.
 * 
 * Applicative functors extend regular functors by adding the ability to:
 * - Lift values into the functor context (pure)
 * - Apply functions wrapped in the functor context (apply)
 *
 * These laws ensure that the applicative operations behave consistently:
 * 
 * 1. Identity:     pure(id) <*> v = v
 * 2. Homomorphism: pure(f) <*> pure(x) = pure(f(x))
 * 3. Interchange:  u <*> pure(y) = pure(($f) => f(y)) <*> u
 * 4. Map:          fa.map(f) = fa.apply(pure(f))
 *
 * Example:
 * ```php
 * class OptionApplicative implements Applicative, Eq {
 *     use ApplicativeLaws;
 * 
 *     public function test(): void {
 *         // Verify laws
 *         assert($this->applicativeIdentity(Some(1)));
 *         assert($this->applicativeHomomorphism(
 *             Some(1), 
 *             1, 
 *             fn($x) => $x + 1
 *         ));
 *     }
 * }
 * ```
 */
trait ApplicativeLaws
{
    /**
     * Identity law: pure(id) <*> v = v
     * 
     * Applying the pure identity function should have no effect.
     *
     * @param Eq&Applicative $fa The applicative value to test
     * @return bool True if the law holds
     */
    public function applicativeIdentity(Eq|Applicative $fa): bool
    {
        return $fa->apply($fa->pure(identity))->eqv($fa, Some(42));
    }

    /**
     * Homomorphism law: pure(f) <*> pure(x) = pure(f(x))
     * 
     * Applying a pure function to a pure value should be the same as
     * applying the function and then lifting the result.
     *
     * @param Eq&Applicative $fa The applicative context
     * @param mixed $a The value to lift
     * @param callable $f The function to apply
     * @return bool True if the law holds
     */
    public function applicativeHomomorphism(Eq|Applicative $fa, $a, callable $f): bool
    {
        return $fa->pure($a)->apply($fa->pure($f))->eqv($fa->pure($f($a)), Some(42));
    }

    /**
     * Interchange law: u <*> pure(y) = pure(($f) => f(y)) <*> u
     * 
     * The order of application shouldn't matter when one side is pure.
     *
     * @param Eq&Applicative $fa The applicative context
     * @param mixed $a The value to lift
     * @param Eq&Applicative $fab The wrapped function to apply
     * @return bool True if the law holds
     */
    public function applicativeInterchange(Eq|Applicative $fa, $a, Eq|Applicative $fab): bool
    {
        return $fa->pure($a)
                    ->apply($fab)->eqv(
                        $fab->apply(
                            $fa->pure(
                                fn ($f) => $f($a)
                            )
                        ),
                        Some(42)
                    );
    }

    /**
     * Map-Apply consistency: fa.map(f) = fa.apply(pure(f))
     * 
     * Mapping a function should be the same as applying its pure version.
     *
     * @param Eq&Applicative $fa The applicative value
     * @param callable $f The function to map/apply
     * @return bool True if the law holds
     */
    public function applicativeMap(Eq|Applicative $fa, callable $f): bool
    {
        return $fa->map($f)->eqv($fa->apply($fa->pure($f)), Some(42));
    }
}
