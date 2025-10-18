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
use Phunkie\Cats\Applicative;
use Phunkie\Cats\Foldable;
use Phunkie\Cats\Monad;
use Phunkie\Cats\Show;
use Phunkie\Cats\Traverse;
use Phunkie\Ops\ImmList\ImmListApplicativeOps;
use Phunkie\Ops\ImmList\ImmListEqOps;
use Phunkie\Ops\ImmList\ImmListFoldableOps;
use Phunkie\Ops\ImmList\ImmListMonadOps;
use Phunkie\Ops\ImmList\ImmListMonoidOps;
use Phunkie\Ops\ImmList\ImmListOps;
use Phunkie\Ops\ImmList\ImmListTraverseOps;
use Phunkie\Utils\Iterator;
use Phunkie\Utils\Traversable;
use TypeError;
use function Phunkie\Functions\show\showArrayType;
use function Phunkie\Functions\type\promote;
use const Phunkie\Functions\show\showValue;

/**
 * An immutable list implementation that preserves type information.
 * 
 * ImmList provides a type-safe, immutable sequence of elements with consistent
 * functional operations. It implements various typeclasses including Applicative,
 * Monad, Traverse, and Foldable.
 *
 * Example:
 * ```php
 * $list = ImmList(1, 2, 3);
 * $doubled = $list->map(fn($x) => $x * 2); // ImmList(2, 4, 6)
 * $sum = $list->fold(0)(fn($acc, $x) => $acc + $x); // 6
 * ```
 *
 * @template A The type of elements in the list
 * @implements Kind<ImmList, A>
 * @implements Applicative<ImmList, A>
 * @implements Monad<ImmList, A>
 * @implements Traverse<ImmList, A>
 * @implements Foldable<A>
 * @implements Traversable<A>
 */
class ImmList implements Kind, Applicative, Monad, Traverse, Foldable, Traversable
{
    use Show;
    use ImmListOps;
    use ImmListApplicativeOps;
    use ImmListEqOps;
    use ImmListMonadOps;
    use ImmListFoldableOps;
    use ImmListMonoidOps;
    use ImmListTraverseOps;

    public const kind = ImmList;
    private array $values;

    /**
     * Constructs a new ImmList.
     * 
     * The constructor behavior depends on the concrete class:
     * - ImmList: Creates a list with the given arguments
     * - NonEmptyList: Creates a non-empty list (requires at least one argument)
     * - Cons: Creates a cons cell (requires head and tail)
     * - Nil: Creates an empty list (requires no arguments)
     *
     * Example:
     * ```php
     * $list = new ImmList(1, 2, 3);    // List(1, 2, 3)
     * $nil = new Nil();                 // List()
     * $cons = new Cons(1, ImmList(2));  // List(1, 2)
     * ```
     *
     * @throws Error if constructor constraints are violated
     * @throws TypeError if attempting to extend ImmList outside namespace
     */
    final public function __construct() { match (get_class($this)) {
        NonEmptyList::class => $this->constructNonEmptyList(func_num_args(), func_get_args()),
        Cons::class => $this->constructCons(func_num_args(), func_get_args()),
        Nil::class => $this->constructNil(func_num_args()),
        ImmList::class => $this->values = func_get_args(),
        default => throw $this->listIsSealed()};
    }

    /**
     * Returns a string representation of the list.
     *
     * Example:
     * ```php
     * $list = ImmList(1, 2, 3);
     * echo $list->toString(); // "List(1, 2, 3)"
     * ```
     *
     * @return string The string representation
     */
    public function toString(): string
    {
        return $this->map(showValue)->mkString("List(", ', ', ')');
    }

    /**
     * Converts the list to a PHP array.
     *
     * @return array<A> The list elements as an array
     */
    public function toArray(): array
    {
        return $this->values;
    }

    /**
     * Returns an iterator over the list elements.
     *
     * @return Iterator<A>
     */
    public function iterator(): Iterator
    {
        $storage = new \SplObjectStorage();
        foreach ($this->toArray() as $k => $v) {
            $storage[promote($k)] = $v;
        }
        return new Iterator($storage);
    }

    /**
     * Returns the number of type parameters this type takes.
     *
     * @return int Always returns 1 as ImmList is a type constructor with one parameter
     */
    public function getTypeArity(): int
    {
        return 1;
    }

    /**
     * Returns an array of type variables for this list.
     *
     * @return array<string> Returns ["Nothing"] for empty lists, or [type] for non-empty lists
     */
    public function getTypeVariables(): array
    {
        return $this->isEmpty() ? ["Nothing"] : [showArrayType($this->toArray())];
    }

    /**
     * Returns a string representation of the list's type.
     *
     * Example:
     * ```php
     * ImmList(1, 2, 3)->showType(); // "List<Int>"
     * ImmList()->showType();        // "List<Nothing>"
     * ```
     *
     * @return string The type representation
     */
    public function showType(): string
    {
        return sprintf("List<%s>", $this->getTypeVariables()[0]);
    }

    /**
     * Constructs a non-empty list.
     *
     * @param int $argc Number of arguments
     * @param array $argv Array of arguments
     * @throws Error if no arguments are provided
     */
    private function constructNonEmptyList(int $argc, array $argv): void
    {
        if ($argc == 0) {
            throw new Error("not enough arguments for constructor Nel");
        }
        $this->values = $argv;
    }

    /**
     * Constructs a cons cell (head::tail structure).
     *
     * @param int $argc Number of arguments
     * @param array $argv Array of arguments
     * @throws Error if wrong number of arguments
     * @throws TypeError if tail is not an ImmList
     */
    private function constructCons(int $argc, array $argv): void
    {
        if ($argc != 2) {
            throw new Error(($argc < 2 ? "not enough" : "too many") . " arguments for constructor List");
        }
        $head = $argv[0];
        $tail = $argv[1];
        if (!$tail instanceof ImmList) {
            throw new TypeError("type mismatch 2nd argument List: expected List, found " .
                ((gettype($tail) == "object") ? get_class($tail) : gettype($tail)));
        }
        $this->values = array_merge([$head], $tail->toArray());
    }

    /**
     * Constructs an empty list.
     *
     * @param int $argc Number of arguments
     * @throws Error if any arguments are provided
     */
    private function constructNil(int $argc): void
    {
        if ($argc > 0) {
            throw new Error("too many arguments for constructor Nil");
        }
        $this->values = [];
    }

    /**
     * Prevents extending ImmList outside its namespace.
     *
     * @return TypeError
     */
    private function listIsSealed(): TypeError
    {
        return new TypeError("List cannot be extended outside namespace");
    }
}
