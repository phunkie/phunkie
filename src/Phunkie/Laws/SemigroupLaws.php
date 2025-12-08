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
use function Phunkie\Functions\show\usesTrait;

/**
 * Laws that every Semigroup must satisfy.
 * 
 * A semigroup is a type with an associative binary operation (combine).
 * This means you can combine elements in any grouping and get the same result.
 *
 * The core law is:
 * 
 * Associativity: combine(combine(x, y), z) = combine(x, combine(y, z))
 * 
 * This ensures that operations can be chained reliably regardless of grouping:
 * - (a + b) + c = a + (b + c)
 * - (xs ++ ys) ++ zs = xs ++ (ys ++ zs)
 *
 * Example:
 * ```php
 * class StringConcat implements Eq {
 *     use SemigroupLaws;
 * 
 *     private $value;
 * 
 *     public function __construct(string $s) {
 *         $this->value = $s;
 *     }
 * 
 *     public function combine(self $that): self {
 *         return new self($this->value . $that->value);
 *     }
 * 
 *     public function test(): void {
 *         $x = new StringConcat("Hello");
 *         $y = new StringConcat(" ");
 *         $z = new StringConcat("World");
 * 
 *         assert($this->combineAssociativity($x, $y, $z));
 *         // (Hello + " ") + World = Hello + (" " + World)
 *     }
 * }
 * ```
 */
trait SemigroupLaws
{
    /**
     * Associativity law: combine(combine(x, y), z) = combine(x, combine(y, z))
     * 
     * Tests that combining elements is associative - the grouping of operations
     * doesn't affect the result. Special handling is provided for:
     * - Types implementing Eq (using eqv)
     * - Callable values (comparing results when applied)
     * - Other values (using == comparison)
     *
     * @param mixed $x First value
     * @param mixed $y Second value
     * @param mixed $z Third value
     * @return bool True if the law holds
     */
    public function combineAssociativity($x, $y, $z): bool
    {
        if (usesTrait($x, Eq::class)) {
            return combine(combine($x, $y), $z)->eqv(combine($x, combine($y, $z)), 42);
        } else {
            if (is_callable($x) && (!is_string($x) || function_exists($x))) {
                return call_user_func(combine(combine($x, $y), $z), 42) ==
                       call_user_func(combine($x, combine($y, $z)), 42);
            }
            return combine(combine($x, $y), $z) == combine($x, combine($y, $z));
        }
    }
}
