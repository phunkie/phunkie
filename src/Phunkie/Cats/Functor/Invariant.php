<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\Cats\Functor;

use Phunkie\Types\Kind;

/**
 * Represents functors that can be mapped covariantly and contravariantly.
 * 
 * Invariant functors support bidirectional mapping through imap, which requires
 * both a forward and reverse function. This is useful for types that must
 * maintain round-trip conversions.
 *
 * Laws:
 * 1. Identity: fa.imap(identity, identity) == fa
 * 2. Composition: fa.imap(f1, g1).imap(f2, g2) == fa.imap(x => f2(f1(x)), x => g1(g2(x)))
 *
 * Example:
 * ```php
 * // Codec example
 * $codec->imap(
 *     fn($s) => json_decode($s, true),  // String -> Array
 *     fn($a) => json_encode($a)         // Array -> String
 * );
 * ```
 *
 * @template A
 */
interface Invariant
{
    /**
     * Maps a pair of functions over this functor.
     * 
     * The first function maps forward (A -> B), while the second maps backward (B -> A).
     * Both functions must be provided to maintain the invariant property.
     *
     * @template B
     * @param callable(A):B $f The forward mapping function
     * @param callable(B):A $g The reverse mapping function
     * @return static<B> A new functor with transformed values
     */
    public function imap(callable $f, callable $g): Kind;
}
