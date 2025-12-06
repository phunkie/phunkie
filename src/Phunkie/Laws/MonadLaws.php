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

use Phunkie\Types\Kind;

/**
 * Laws that every Monad must satisfy.
 * 
 * A monad is a type that implements flatMap (bind) and pure operations
 * that allow sequencing computations while maintaining context.
 * These laws ensure that the sequencing behaves consistently.
 *
 * These laws are also known as the monad laws:
 * 
 * 1. Left Identity:  pure(a).flatMap(f) = f(a)
 *    Starting with a pure value is the same as applying f directly
 * 
 * 2. Right Identity: m.flatMap(pure) = m
 *    Lifting the final value back into the monad has no effect
 * 
 * 3. Associativity: m.flatMap(f).flatMap(g) = m.flatMap(x => f(x).flatMap(g))
 *    The order of nested computations doesn't matter
 *
 * Example:
 * ```php
 * class MaybeMonad implements Kind {
 *     use MonadLaws;
 * 
 *     public function test(): void {
 *         $m = Some(42);
 *         $f = fn($x) => Some($x * 2);
 *         $g = fn($x) => Some($x + 1);
 * 
 *         // Verify laws
 *         assert($this->leftIdentity($m, 42, $f));
 *         assert($this->rightIdentity($m));
 *         assert($this->flatMapAssociativity($m, $f, $g));
 *     }
 * }
 * ```
 */
trait MonadLaws
{
    /**
     * Associativity law: m.flatMap(f).flatMap(g) = m.flatMap(x => f(x).flatMap(g))
     * 
     * The order of chaining monadic computations shouldn't matter.
     * This ensures that complex sequences can be built up consistently.
     *
     * @template A The initial value type
     * @template B The intermediate value type
     * @template C The final value type
     * @param Kind<A> $fa The monadic value
     * @param callable(A):Kind<B> $f First transformation
     * @param callable(B):Kind<C> $g Second transformation
     * @return bool True if the law holds
     */
    public function flatMapAssociativity(Kind $fa, callable $f, callable $g): bool
    {
        return $fa->flatMap($f)->flatMap($g) == $fa->flatMap(fn ($a) => $f($a)->flatMap(
            fn ($b) => $g($b)
        ));
    }

    /**
     * Left Identity law: pure(a).flatMap(f) = f(a)
     * 
     * Starting with a pure value and then binding is the same as
     * applying the function directly.
     *
     * @param Kind $fa The monad implementation
     * @param mixed $a The value to lift
     * @param callable $f The function to apply
     * @return bool True if the law holds
     */
    public function leftIdentity(Kind $fa, $a, callable $f): bool
    {
        return $fa->pure($a)->flatMap($f) == $f($a);
    }

    /**
     * Right Identity law: m.flatMap(pure) = m
     * 
     * Binding a monadic value to pure should have no effect.
     * This ensures that pure is a neutral operation.
     *
     * @param Kind $fa The monadic value to test
     * @return bool True if the law holds
     */
    public function rightIdentity(Kind $fa): bool
    {
        return $fa->flatMap(fn ($a) => $fa->pure($a)) == $fa;
    }
}
