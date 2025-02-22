<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\Types\ImmList;

use Exception;

/**
 * Exception thrown when attempting to access a non-existent element.
 * 
 * This exception is thrown by ImmList operations that require an element to exist,
 * such as accessing head() or last() on an empty list, or when trying to access
 * elements beyond the list's bounds.
 *
 * Example:
 * ```php
 * $empty = ImmList();
 * try {
 *     $empty->head(); // Throws NoSuchElementException
 * } catch (NoSuchElementException $e) {
 *     // Handle empty list case
 * }
 * ```
 */
class NoSuchElementException extends Exception
{
}
