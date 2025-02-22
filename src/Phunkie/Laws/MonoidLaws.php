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

use Phunkie\Algebra\Eq;
use function Phunkie\Functions\semigroup\combine;
use function Phunkie\Functions\semigroup\zero;
use function Phunkie\Functions\show\usesTrait;

/**
 * Laws that every Monoid must satisfy.
 * 
 * A monoid extends a semigroup by adding an identity element (zero).
 * This means it has:
 * - An associative binary operation (combine)
 * - An identity element (zero) that is neutral under the operation
 *
 * These laws ensure that the monoid operations behave consistently:
 * 
 * 1. Right Identity: combine(x, zero) = x
 *    Combining with zero on the right has no effect
 * 
 * 2. Left Identity:  combine(zero, x) = x
 *    Combining with zero on the left has no effect
 * 
 * 3. Associativity: (inherited from Semigroup)
 *    combine(combine(x, y), z) = combine(x, combine(y, z))
 *
 * Example:
 * ```php
 * class Sum implements Eq {
 *     use MonoidLaws;
 * 
 *     private $value;
 * 
 *     public function __construct(int $n) {
 *         $this->value = $n;
 *     }
 * 
 *     public static function zero(): self {
 *         return new self(0);
 *     }
 * 
 *     public function combine(self $that): self {
 *         return new self($this->value + $that->value);
 *     }
 * 
 *     public function test(): void {
 *         $x = new Sum(42);
 *         assert($this->combineRightIdentity($x));
 *         assert($this->combineLeftIdentity($x));
 *     }
 * }
 * ```
 */
trait MonoidLaws
{
    use SemigroupLaws;

    /**
     * Right Identity law: combine(x, zero) = x
     * 
     * Combining any value with zero on the right should return
     * the original value. This ensures zero is a right identity.
     *
     * @param mixed $x The value to test
     * @return bool True if the law holds
     */
    public function combineRightIdentity($x): bool
    {
        if (usesTrait($x, Eq::class)) {
            return combine($x, zero($x))->eqv($x, Some(42));
        } else {
            if (is_callable($x)) {
                return call_user_func(combine($x, zero($x)), 42) == $x(42);
            }
            return combine($x, zero($x)) == $x;
        }
    }

    /**
     * Left Identity law: combine(zero, x) = x
     * 
     * Combining any value with zero on the left should return
     * the original value. This ensures zero is a left identity.
     *
     * @param mixed $x The value to test
     * @return bool True if the law holds
     */
    public function combineLeftIdentity($x): bool
    {
        if (usesTrait($x, Eq::class)) {
            return combine(zero($x), $x)->eqv($x, Some(42));
        } else {
            if (is_callable($x)) {
                return call_user_func(combine($x, zero($x)), 42) == $x(42);
            }
            return combine(zero($x), $x) == $x;
        }
    }
}
