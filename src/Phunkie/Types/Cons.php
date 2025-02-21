<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\Types;

/**
 * Represents a non-empty list constructed with a head and tail.
 * 
 * Cons (::) is a fundamental list constructor that combines a single element (head)
 * with another list (tail) to create a new list. It's part of the ImmList hierarchy
 * and provides the basic building block for creating linked lists.
 *
 * Example:
 * ```php
 * $list = new Cons(1, ImmList(2, 3)); // List(1, 2, 3)
 * // Or using the :: operator:
 * $list = 1 :: ImmList(2, 3);         // List(1, 2, 3)
 * ```
 *
 * @template A
 * @extends ImmList<A>
 */
final class Cons extends ImmList
{
}
