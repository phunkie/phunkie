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

use Error;
use Phunkie\Cats\Show;
use Phunkie\Ops\Unit\UnitEqOps;
use Phunkie\Ops\Unit\UnitMonoidOps;

/**
 * Represents the Unit type - a type with exactly one value.
 *
 * Unit is used when a function must return something but has no meaningful
 * value to return. It's similar to void in other languages, but is an actual
 * type with a value. In functional programming, Unit is often written as '()'.
 *
 * Unit forms a trivial monoid where combining any two Units produces Unit,
 * and all Units are equal to each other.
 *
 * Example:
 * ```php
 * $unit = Unit();
 * echo $unit->toString(); // "()"
 *
 * // Monoid operations
 * Unit()->combine(Unit());  // Unit()
 * Unit()->zero();           // Unit()
 *
 * // Equality
 * Unit()->eqv(Unit());      // true
 * ```
 *
 * @extends Tuple<never>
 */
final class Unit extends Tuple
{
    use Show;
    use UnitEqOps;
    use UnitMonoidOps;

    /**
     * Returns the string representation of Unit.
     *
     * @return string Always returns "()"
     */
    public function toString(): string
    {
        return '()';
    }

    /**
     * Magic method for string conversion.
     *
     * @return string Always returns "()"
     */
    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * Returns the type representation.
     *
     * @return string Always returns "Unit"
     */
    public function showType(): string
    {
        return 'Unit';
    }

    /**
     * Attempting to access members of Unit throws an error.
     *
     * @param string $member The member name
     * @throws Error Always throws as Unit has no members
     */
    public function __get($member)
    {
        throw new Error("$member is not a member of Unit");
    }

    /**
     * Attempting to set members of Unit throws an error.
     *
     * @param string $member The member name
     * @param mixed $value The value to set
     * @throws Error Always throws as Unit has no members
     */
    public function __set($member, $value): void
    {
        throw new Error("$member is not a member of Unit");
    }

    /**
     * Attempting to copy Unit throws an error.
     *
     * @param array<string,mixed> $fields Unused
     * @throws Error Always throws as Unit cannot be copied
     */
    public function copy(array $fields): Tuple
    {
        throw new Error("copy is not a member of Unit");
    }
}
