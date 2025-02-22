<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\Cats;

use Phunkie\Types\Kind;

/**
 * Provides string representation capabilities for types.
 * 
 * Show is a trait that standardizes how types are converted to strings for
 * display and debugging. Types using this trait must implement toString()
 * to define their specific string representation.
 *
 * Example:
 * ```php
 * class Point {
 *     use Show;
 *     
 *     private $x;
 *     private $y;
 *     
 *     public function __construct(int $x, int $y) {
 *         $this->x = $x;
 *         $this->y = $y;
 *     }
 *     
 *     public function toString(): string {
 *         return "Point($this->x, $this->y)";
 *     }
 * }
 * 
 * $point = new Point(1, 2);
 * echo $point->show();      // "Point(1, 2)"
 * echo $point->showType(); // "Point"
 * ```
 */
trait Show
{
    /**
     * Returns a string representation of this value.
     * Must be implemented by classes using this trait.
     *
     * @return string The string representation
     */
    abstract public function toString(): string;

    /**
     * Returns a string representation of this value.
     * Alias for toString() for more idiomatic usage.
     *
     * @return string The string representation
     */
    public function show(): string
    {
        return $this->toString();
    }

    /**
     * Returns a string representation of this type.
     * For Kind types, includes type parameters.
     *
     * @return string The type representation
     */
    public function showType(): string
    {
        if ($this instanceof Kind) {
            return sprintf($this::kind . "<%s>", implode(", ", $this->getTypeVariables()));
        }
        return get_class($this);
    }
}
