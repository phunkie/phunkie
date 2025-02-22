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

use Phunkie\Cats\Applicative;
use Phunkie\Cats\Foldable;
use Phunkie\Cats\Monad;
use Phunkie\Cats\Show;
use Phunkie\Ops\Option\OptionApplicativeOps;
use Phunkie\Ops\Option\OptionEqOps;
use Phunkie\Ops\Option\OptionFoldableOps;
use Phunkie\Ops\Option\OptionMonadOps;
use Phunkie\Ops\Option\OptionMonoidOps;
use Phunkie\Ops\Option\OptionOps;
use Phunkie\Utils\Traversable;
use function Phunkie\Functions\show\showType;
use function Phunkie\Functions\show\showValue;

/**
 * A container type that may or may not contain a value.
 * 
 * Option represents optional values in a type-safe way. It can either be:
 * - Some(value): Contains a value of type A
 * - None: Represents no value
 *
 * Example:
 * ```php
 * $name = Option("John");          // Some("John")
 * $missing = Option(null);         // None
 * $upper = $name->map('strtoupper'); // Some("JOHN")
 * ```
 *
 * @template A The type of the optional value
 * @implements Kind<Option, A>
 * @implements Applicative<Option, A>
 * @implements Monad<Option, A>
 * @implements Foldable<A>
 * @implements Traversable<A>
 */
abstract class Option implements Kind, Applicative, Monad, Foldable, Traversable
{
    use Show;
    use OptionApplicativeOps;
    use OptionOps;
    use OptionEqOps;
    use OptionMonadOps;
    use OptionFoldableOps;
    use OptionMonoidOps;
    public const kind = "Option";
    protected $t;

    /**
     * Protected constructor for Option types.
     * 
     * This constructor is used internally by Some and None classes.
     * To create Options, use the global Option() function instead:
     * ```php
     * $some = Option("value");  // Some("value")
     * $none = Option(null);     // None
     * ```
     *
     * @param mixed|null $t The value to wrap (for Some) or null (for None)
     * @throws \Error if attempting to create an Option that is neither Some nor None
     */
    final protected function __construct($t = null)
    {
        if ($this instanceof Some) {
            $this->t = $t;
        } elseif (!$this instanceof None) {
            throw new \Error("Option can only be Some or None");
        }
    }

    /**
     * Returns the value if present, otherwise returns the provided default.
     *
     * Example:
     * ```php
     * Some(42)->getOrElse(0);  // 42
     * None()->getOrElse(0);    // 0
     * ```
     *
     * @template B
     * @param B $t The default value
     * @return A|B The contained value or default
     */
    abstract public function getOrElse($t);

    /**
     * Checks if this Option contains a value.
     *
     * @return bool true if this is a Some, false if this is None
     */
    abstract public function isDefined(): bool;

    /**
     * Checks if this Option contains no value.
     *
     * @return bool true if this is None, false if this is a Some
     */
    abstract public function isEmpty(): bool;

    /**
     * Returns a string representation of this Option.
     *
     * Example:
     * ```php
     * Some(42)->toString();  // "Some(42)"
     * None()->toString();    // "None"
     * ```
     *
     * @return string The string representation
     */
    public function toString(): string
    {
        return $this->isEmpty() ? "None" : "Some(". showValue($this->get()) . ")";
    }

    /**
     * Returns the number of type parameters this type takes.
     *
     * @return int Returns 0 for None, 1 for Some
     */
    public function getTypeArity(): int
    {
        return $this->isEmpty() ? 0 : 1;
    }

    /**
     * Returns an array of type variables for this option.
     *
     * Example:
     * ```php
     * Some(42)->getTypeVariables();    // ["Int"]
     * None()->getTypeVariables();      // []
     * ```
     *
     * @return array<string> Empty array for None, [type] for Some
     */
    public function getTypeVariables(): array
    {
        return $this->isEmpty() ? [] : [showType($this->get())];
    }
}
