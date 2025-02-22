<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\Ops\Function1;

use Phunkie\Algebra\Eq;
use Phunkie\Types\Function1;
use Phunkie\Types\Option;

/**
 * Equality operations for Function1.
 * 
 * This trait implements the Eq interface for functions, allowing them to be
 * compared for equality. Since functions can't be directly compared, they
 * are considered equal if they produce the same output for a given input.
 *
 * Example:
 * ```php
 * $f = Function1(fn($x) => $x + 1);
 * $g = Function1(fn($x) => 1 + $x);
 * $h = Function1(fn($x) => $x * 2);
 * 
 * // Functions are equal if they produce same output for test input
 * $f->eqv($g, Some(42)); // true
 * $f->eqv($h, Some(42)); // false
 * ```
 *
 * @template A The input type
 * @template B The output type
 * @mixin Function1
 */
trait Function1EqOps
{
    use Eq;

    /**
     * Tests if two functions are equivalent.
     * 
     * Functions are considered equal if they return the same value
     * when applied to the test argument. If no test argument is provided,
     * 42 is used as a default test value.
     *
     * @param Function1<A,B> $rhs The function to compare with
     * @param mixed $arg Optional test argument (will be wrapped in Option)
     * @return bool True if both functions return the same value
     */
    public function eqv(self $rhs, $arg = 42): bool
    {
        if ($rhs instanceof Function1) {
            $testArg = $arg instanceof Option ? $arg : Option($arg);
            return $this->__invoke($testArg->getOrElse(42)) == $rhs->__invoke($testArg->getOrElse(42));
        }
        return false;
    }
}
