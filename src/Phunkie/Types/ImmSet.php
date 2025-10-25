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
use Phunkie\Ops\ImmSet\ImmSetApplicativeOps;
use Phunkie\Ops\ImmSet\ImmSetEqOps;
use Phunkie\Ops\ImmSet\ImmSetFoldableOps;
use Phunkie\Ops\ImmSet\ImmSetFunctorOps;
use Phunkie\Ops\ImmSet\ImmSetMonadOps;
use Phunkie\Ops\ImmSet\ImmSetMonoidOps;
use Phunkie\Ops\ImmSet\ImmSetOps;
use Phunkie\Ops\ImmSet\ImmSetTraverseOps;
use Phunkie\Utils\Iterator;
use function Phunkie\Functions\show\showArrayType;
use function Phunkie\Functions\show\showValue;
use function Phunkie\Functions\type\promote;

/**
 * An immutable set implementation.
 *
 * ImmSet provides a type-safe, immutable collection of unique elements.
 * It implements Applicative, Monad, Foldable, and Monoid for functional
 * transformations while maintaining uniqueness of elements.
 *
 * Example:
 * ```php
 * $set = ImmSet(1, 2, 2, 3); // ImmSet(1, 2, 3)
 * $set->contains(2);         // true
 * $set->map(fn($x) => $x * 2); // ImmSet(2, 4, 6)
 * $set->filter(fn($x) => $x > 1); // ImmSet(2, 3)
 * $set->foldLeft(0)(fn($acc, $x) => $acc + $x); // 6
 * ```
 *
 * @template A
 * @implements Kind<ImmSet, A>
 * @implements Applicative<ImmSet, A>
 * @implements Monad<ImmSet, A>
 * @implements Foldable<A>
 */
class ImmSet implements Kind, Applicative, Monad, Foldable
{
    use Show;
    use ImmSetFunctorOps;
    use ImmSetApplicativeOps;
    use ImmSetEqOps;
    use ImmSetMonadOps;
    use ImmSetFoldableOps;
    use ImmSetMonoidOps;
    use ImmSetTraverseOps;
    use ImmSetOps;
    private $elements = [];

    /**
     * Creates a new ImmSet with unique elements.
     * Duplicate elements are automatically removed.
     *
     * Example:
     * ```php
     * ImmSet(1, 2, 2, 3); // ImmSet(1, 2, 3)
     * ImmSet();           // Empty set
     * ```
     *
     * @param A ...$elements The elements to include in the set
     */
    public function __construct(...$elements)
    {
        foreach ($elements as $element) {
            if (!$this->contains($element)) {
                $this->elements[] = $element;
            }
        }
    }

    /**
     * Checks if an element exists in the set.
     *
     * @param A $element The element to check
     * @return bool True if the element exists
     */
    public function contains($element): bool
    {
        return ((!is_object($element) && in_array($element, $this->elements, true)) ||
            (is_object($element) && in_array($element, $this->elements)));
    }

    /**
     * Returns a new set with an element removed.
     *
     * Example:
     * ```php
     * $set = ImmSet(1, 2, 3);
     * $set->minus(2); // ImmSet(1, 3)
     * ```
     *
     * @param A $element The element to remove
     * @return ImmSet<A> A new set without the element
     */
    public function minus($element): ImmSet
    {
        if (!$this->contains($element)) {
            return ImmSet(...$this->elements);
        }

        $elements = [];
        foreach ($this->elements as $el) {
            if ((is_object($el) && $element == $el) || (!is_object($el) && $element === $el)) {
                continue;
            }
            $elements[] = $el;
        }
        return ImmSet(...$elements);
    }

    /**
     * Returns a new set with an element added.
     * If the element already exists, returns an identical set.
     *
     * @param A $element The element to add
     * @return ImmSet<A> A new set with the element added
     */
    public function plus($element): ImmSet
    {
        if ($this->contains($element)) {
            return ImmSet(...$this->elements);
        }

        return ImmSet(...array_merge($this->elements, [$element]));
    }

    /**
     * Converts the set to an array.
     *
     * @return array<A> Array containing the set elements
     */
    public function toArray(): array
    {
        return $this->elements;
    }

    /**
     * Returns a string representation of the set.
     *
     * Example:
     * ```php
     * $set = ImmSet(1, 2, 3);
     * echo $set->toString(); // "Set(1, 2, 3)"
     * ```
     *
     * @return string The string representation
     */
    public function toString(): string
    {
        return "Set(" . implode(", ", array_map(fn ($e) => showValue($e), $this->elements)) . ")";
    }

    /**
     * Returns the number of type parameters this type takes.
     *
     * @return int Always returns 1 (the element type)
     */
    public function getTypeArity(): int
    {
        return 1;
    }

    /**
     * Returns an array of type variables for this set.
     *
     * @return array<string> [element_type]
     */
    public function getTypeVariables(): array
    {
        return [showArrayType($this->elements)];
    }

    /**
     * Returns an iterator over the set elements.
     *
     * @return Iterator<A> Iterator over the elements
     */
    public function iterator()
    {
        $storage = new \SplObjectStorage();
        foreach ($this->toArray() as $k => $v) {
            $storage[promote($k)] = $v;
        }
        return new Iterator($storage);
    }

    /**
     * Returns a new set containing elements from both sets.
     *
     * Example:
     * ```php
     * $s1 = ImmSet(1, 2);
     * $s2 = ImmSet(2, 3);
     * $s1->union($s2); // ImmSet(1, 2, 3)
     * ```
     *
     * @param ImmSet<A> $set The set to union with
     * @return ImmSet<A> The union of both sets
     */
    public function union(ImmSet $set): ImmSet
    {
        return ImmSet(...array_merge($this->elements, $set->elements));
    }

    /**
     * Returns a new set containing elements present in both sets.
     *
     * Example:
     * ```php
     * $s1 = ImmSet(1, 2, 3);
     * $s2 = ImmSet(2, 3, 4);
     * $s1->intersect($s2); // ImmSet(2, 3)
     * ```
     *
     * @param ImmSet<A> $set The set to intersect with
     * @return ImmSet<A> The intersection of both sets
     */
    public function intersect(ImmSet $set)
    {
        $new = [];
        foreach ($this->elements as $element) {
            if ($set->contains($element)) {
                $new[] = $element;
            }
        }
        return ImmSet(...$new);
    }

    /**
     * Returns a new set containing elements present in either set but not both.
     *
     * Example:
     * ```php
     * $s1 = ImmSet(1, 2);
     * $s2 = ImmSet(2, 3);
     * $s1->diff($s2); // ImmSet(1, 3)
     * ```
     *
     * @param ImmSet<A> $set The set to diff with
     * @return ImmSet<A> The symmetric difference of both sets
     */
    public function diff(ImmSet $set)
    {
        $new = [];
        foreach ($this->elements as $element) {
            if (!$set->contains($element)) {
                $new[] = $element;
            }
        }
        foreach ($set->elements as $element) {
            if (!$this->contains($element)) {
                $new[] = $element;
            }
        }
        return ImmSet(...$new);
    }

    /**
     * Checks if the set is empty.
     *
     * @return bool True if the set contains no elements
     */
    public function isEmpty(): bool
    {
        return [] === $this->elements;
    }
}
