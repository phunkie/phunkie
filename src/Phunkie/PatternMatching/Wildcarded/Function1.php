<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\PatternMatching\Wildcarded;

/**
 * Wildcard pattern matching for Function1.
 * 
 * This class enables pattern matching against any Function1 value,
 * ignoring the specific function implementation. It acts as a type-level
 * pattern match.
 *
 * Example:
 * ```php
 * // Match any Function1
 * $f = Function1(fn($x) => $x + 1);
 * $match = new PMatch($f);
 * 
 * if ($match(new Function1())) {
 *     // Matches any Function1 value
 * }
 * 
 * // Combined with other patterns
 * $match = new PMatch(Some($f));
 * if ($match(Some(new Function1()))) {
 *     // Matches Some containing any Function1
 * }
 * ```
 *
 * @see \Phunkie\Types\Function1 The Function1 type being matched
 * @see \Phunkie\PatternMatching\PMatch The pattern matcher
 */
class Function1
{
    // Empty class - used only as a type marker for pattern matching
}
