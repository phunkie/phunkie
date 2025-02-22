<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\Algebra;

/**
 * Represents types that can be combined associatively.
 * 
 * A Semigroup is a type with an associative binary operation (combine).
 * For any values a, b, and c, the following must hold:
 * combine(combine(a, b), c) == combine(a, combine(b, c))
 *
 * Example:
 * ```php
 * // String concatenation forms a semigroup
 * $s->combine("Hello", "World"); // "HelloWorld"
 * 
 * // Integer addition forms a semigroup
 * $s->combine(1, 2); // 3
 * ```
 *
 * @template T
 */
interface Semigroup
{
    /**
     * Combines two values of type T.
     *
     * @param T $one First value
     * @param T $another Second value
     * @return T The combined result
     */
    public function combine($one, $another);
}
