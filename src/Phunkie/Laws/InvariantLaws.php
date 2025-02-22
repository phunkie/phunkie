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
use const Phunkie\Functions\function1\identity;

/**
 * Laws that every Invariant functor must satisfy.
 * 
 * An invariant functor is a type that implements an imap operation which:
 * - Takes both forward and backward transformations
 * - Preserves roundtrip conversions
 * - Maintains composition properties
 *
 * These laws ensure that imap behaves consistently:
 * 
 * 1. Identity:     fa.imap(id, id) = fa
 * 2. Composition:  fa.imap(f1, f2).imap(g1, g2) = fa.imap(g1 ∘ f1, f2 ∘ g2)
 *
 * Example:
 * ```php
 * class JsonFormat implements Kind {
 *     use InvariantLaws;
 * 
 *     public function test(): void {
 *         $format = new JsonFormat();
 *         
 *         // Verify identity law
 *         assert($this->invariantIdentity($format));
 * 
 *         // Verify composition law
 *         assert($this->invariantComposition(
 *             $format,
 *             Function1(fn(int $i): string => (string)$i),    // int -> string
 *             Function1(fn(string $s): int => (int)$s),       // string -> int
 *             Function1(fn(string $s): bool => $s === "1"),   // string -> bool
 *             Function1(fn(bool $b): string => $b ? "1" : "0") // bool -> string
 *         ));
 *     }
 * }
 * ```
 */
trait InvariantLaws
{
    /**
     * Identity law: fa.imap(id, id) = fa
     * 
     * Mapping with identity functions in both directions should
     * return an equivalent functor.
     *
     * @param Kind $fa The invariant functor to test
     * @return bool True if the law holds
     */
    public function invariantIdentity(Kind $fa): bool
    {
        return $fa->eqv($fa->imap(identity, identity), Some(42));
    }

    /**
     * Composition law: fa.imap(f1, f2).imap(g1, g2) = fa.imap(g1 ∘ f1, f2 ∘ g2)
     * 
     * Composing two imaps should be the same as imapping with the composed functions.
     * This ensures that transformations can be chained consistently.
     *
     * @param Kind $fa The invariant functor to test
     * @param Function1<Int,String> $f1 First forward function
     * @param Function1<String,Int> $f2 First backward function
     * @param Function1<String,Bool> $g1 Second forward function
     * @param Function1<Bool,String> $g2 Second backward function
     * @return bool True if the law holds
     */
    public function invariantComposition(Kind $fa, Function1 $f1, Function1 $f2, Function1 $g1, Function1 $g2): bool
    {
        return $fa->imap($f1, $f2)->imap($g1, $g2)->eqv(
            $fa->imap($g1->compose($f1), $f2->compose($g2)),
            Some(42)
        );
    }
}
