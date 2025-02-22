<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Phunkie\Types\Unit;

/**
 * Creates a Unit value.
 * 
 * Unit represents the type with exactly one value, similar to void or null
 * but in a more type-safe way. It's often used as a placeholder when no
 * meaningful value needs to be returned.
 *
 * Example:
 * ```php
 * Unit();           // Unit
 * 
 * // Common usage in side-effecting functions
 * function log($msg) {
 *     echo $msg;
 *     return Unit();
 * }
 * 
 * // Or as default value in Options
 * Option(null)->getOrElse(Unit());  // Unit
 * ```
 *
 * @return Unit The Unit value
 */
function Unit(): Unit
{
    return new Unit();
}
