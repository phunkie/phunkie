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

use InvalidArgumentException;
use Phunkie\Cats\Show;
use TypeError;
use function Phunkie\Functions\show\showValue;

/**
 * Pairs are specialized tuples with exactly two elements.
 *
 * @template T1
 * @template T2
 * @property T1 $_1
 * @property T2 $_2
 */
final class Pair extends Tuple
{
    use Show;

    /**
     * Pairs have two members that can be accessed with _1 and _2
     *
     * $pair = Pair("name", 25);
     * echo $pair->_1; // "name"
     * echo $pair->_2; // 25
     *
     * @param string $member The property name (_1 or _2)
     * @return T1 | T2
     * @throws InvalidArgumentException if the index is invalid
     */
    public function __get(string $member) { return match ($member) {
        "_1", "_2" => parent::__get($member),
        default => throw new InvalidArgumentException("Invalid index $member for pair")};
    }

    /**
     * Pairs are immutable
     *
     * $pair = Pair("hello", 42);
     *
     * These will throw TypeError:
     * $pair->_1 = "world";     // Error: Pairs are immutable
     * $pair->_3 = true;        // Error: Invalid index _3 for pair
     *
     * @param string $member The property name (_1 or _2)
     * @throws TypeError always. Pairs are immutable
     */
    public function __set($member, $value): void
    {
        throw new TypeError("Pairs are immutable");
    }

    /**
     * Pairs are showable
     *
     * $pair = Pair("name", 25);
     * echo $pair->toString(); // "Pair(name, 25)"
     *
     * @return string
     */
    public function toString(): string
    {
        return "Pair(" . showValue(parent::__get("_1")) . ", " . showValue(parent::__get("_2")) . ")";
    }

    /**
     *  Pairs maintain type information for all elements
     *
     *  $pair = Pair("hello", 42);
     *  echo $pair->showType();  // "(String, Int)"
     *
     * @return string
     */
    public function showType(): string
    {
        return sprintf("(%s)", implode(", ", $this->getTypeVariables()));
    }
}
