<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\PatternMatching;

use Phunkie\PatternMatching\Referenced\GenericReferenced;
use Phunkie\PatternMatching\Referenced\ListWithTail;
use Phunkie\PatternMatching\Referenced\Some as ReferencedSome;
use Phunkie\PatternMatching\Wildcarded\Function1 as WildcardedFunction1;
use Phunkie\PatternMatching\Wildcarded\ImmList as WildcardedCons;
use Phunkie\PatternMatching\Referenced\ListNoTail;
use Phunkie\PatternMatching\Referenced\NonEmptyList as ReferencedNel;
use Phunkie\PatternMatching\Referenced\Cons as ReferencedCons;
use Phunkie\Types\Cons as ConsType;
use Phunkie\Types\Function1;
use Phunkie\Types\ImmList;
use Phunkie\Types\NonEmptyList;
use Phunkie\Types\Option;
use Phunkie\Types\Some;
use Phunkie\Validation\Failure;
use Phunkie\Validation\Success;

/**
 * Pattern matching implementation for PHP.
 * 
 * PMatch provides Scala-like pattern matching capabilities, allowing you to:
 * - Match values against patterns
 * - Extract values using references
 * - Use wildcards for partial matching
 * - Match against complex data structures
 *
 * Example:
 * ```php
 * // Simple value matching
 * $match = new PMatch($value);
 * if ($match(42)) { ... }
 * 
 * // Pattern matching with extraction
 * $x = null;
 * $match = new PMatch(Some(42));
 * if ($match(Some($x))) {
 *     echo $x; // 42
 * }
 * 
 * // List pattern matching
 * $head = null; $tail = null;
 * $match = new PMatch(ImmList(1,2,3));
 * if ($match(ListWithTail($head, $tail))) {
 *     echo "$head and " . $tail->mkString(); // "1 and 2,3"
 * }
 * 
 * // Wildcard matching
 * $match = new PMatch(Some(42));
 * if ($match(Some(_))) { // matches any Some value
 *     ...
 * }
 * ```
 */
class PMatch
{
    private $values;

    /**
     * Creates a new pattern matcher for the given values.
     *
     * @param mixed ...$values Values to match against
     */
    public function __construct(...$values)
    {
        $this->values = $values;
    }

    /**
     * Attempts to match the values against the given conditions.
     * 
     * The number of conditions must match the number of values
     * (except when using a single wildcard).
     *
     * @param mixed ...$conditions Patterns to match against
     * @return bool True if all patterns match
     * @throws \Error If number of conditions doesn't match values
     */
    public function __invoke(...$conditions): bool
    {
        $conditions = $this->wildcardGuard($conditions);
        $this->guardNumberOfConditionsAndValuesNotEqual($conditions);

        for ($position = 0; $position < count($conditions); $position++) {
            if (!$this->conditionIsValid($conditions[$position], $this->values[$position])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Expands a single wildcard to match multiple values.
     */
    private function wildcardGuard($conditions)
    {
        if (count($conditions) == 1 && $conditions[0] == _ && count($conditions) < count($this->values)) {
            return array_fill(0, count($this->values), _);
        }
        return $conditions;
    }

    /**
     * Ensures the number of conditions matches the number of values.
     * 
     * @throws \Error If counts don't match
     */
    private function guardNumberOfConditionsAndValuesNotEqual($conditions)
    {
        if (count($conditions) != count($this->values)) {
            throw new \Error("number of conditions must equal number of arguments in match.");
        }
    }

    /**
     * Validates if a condition matches a value.
     * 
     * Checks various pattern matching conditions including:
     * - Wildcards
     * - Reference extractions
     * - Type-specific matches (None, Nil, etc)
     * - Value equality
     *
     * @param mixed $condition The pattern to match against
     * @param mixed $value The value to match
     * @return bool True if the pattern matches
     */
    private function conditionIsValid($condition, $value): bool 
    { 
        return match (true) {
            $condition === _,
            $this->matchSomeByReference($condition, $value),
            $this->matchByReference($condition, $value),
            $this->matchesNone($condition, $value),
            $this->matchesNil($condition, $value),
            $this->matchesWildcardedNel($condition, $value),
            $this->matchesConsWildcardedHead($condition, $value),
            $this->matchesConsWildcardedTail($condition, $value),
            $this->matchesWildcardedFunction1($condition, $value),
            $this->matchesWildcardedSome($condition, $value),
            $this->matchesWildcardedFailure($condition, $value),
            $this->matchesWildcardedSuccess($condition, $value),
            $this->sameTypeSameValue($condition, $value) => true,
            default => false 
        };
    }

    /**
     * Checks if a condition matches a Some value with wildcard.
     *
     * @param mixed $condition Pattern to match
     * @param mixed $value Value to match against
     * @return bool True if matches Some(_)
     */
    private function matchesWildcardedSome($condition, $value): bool
    {
        return $condition instanceof Some && $condition == Some(_) && $value instanceof Some;
    }

    /**
     * Checks if a condition matches a Function1 with wildcard.
     *
     * @param mixed $condition Pattern to match
     * @param mixed $value Value to match against
     * @return bool True if matches Function1(_)
     */
    private function matchesWildcardedFunction1($condition, $value): bool
    {
        return $condition instanceof WildcardedFunction1 && $value instanceof Function1;
    }

    /**
     * Checks if two values are of the same type and equal.
     * Special handling for ImmList using eqv comparison.
     *
     * @param mixed $condition Pattern to match
     * @param mixed $value Value to match against
     * @return bool True if types match and values are equal
     */
    private function sameTypeSameValue($condition, $value): bool
    {
        return gettype($condition) == gettype($value) &&
               ($value == $condition || ($condition instanceof ImmList && $condition->eqv($value)));
    }

    /**
     * Extracts value from Some into a reference.
     *
     * @param mixed $condition Referenced Some pattern
     * @param mixed $value Some value to extract from
     * @return bool True if extraction succeeded
     */
    private function matchSomeByReference($condition, $value): bool
    {
        if ($condition instanceof ReferencedSome && $value instanceof Some) {
            $condition->value = $value->get();
            return true;
        }
        return false;
    }

    /**
     * Checks if a condition matches None.
     *
     * @param mixed $condition Pattern to match
     * @param mixed $value Value to match against
     * @return bool True if matches None
     */
    private function matchesNone($condition, $value): bool
    {
        return $condition == None && $value instanceof Option && $value == None();
    }

    /**
     * Checks if a condition matches Nil (empty list).
     *
     * @param mixed $condition Pattern to match
     * @param mixed $value Value to match against
     * @return bool True if matches Nil
     */
    private function matchesNil($condition, $value): bool
    {
        return $condition == Nil && $value instanceof ImmList && $value == Nil();
    }

    /**
     * Checks if a condition matches a Failure with wildcard.
     *
     * @param mixed $condition Pattern to match
     * @param mixed $value Value to match against
     * @return bool True if matches Failure(_)
     */
    private function matchesWildcardedFailure($condition, $value): bool
    {
        return $condition instanceof Failure && $condition == Failure(_) && $value instanceof Failure;
    }

    /**
     * Checks if a condition matches a Success with wildcard.
     *
     * @param mixed $condition Pattern to match
     * @param mixed $value Value to match against
     * @return bool True if matches Success(_)
     */
    private function matchesWildcardedSuccess($condition, $value): bool
    {
        return $condition instanceof Success && $condition == Success(_) && $value instanceof Success;
    }

    /**
     * Checks if a condition matches a list with wildcarded head.
     * Recursively matches the tail.
     *
     * @param mixed $condition Pattern to match
     * @param mixed $value Value to match against
     * @return bool True if matches Cons(_, tail)
     */
    private function matchesConsWildcardedHead($condition, $value): bool
    {
        if ($condition instanceof WildcardedCons && $condition->head == _ && $value instanceof ImmList) {
            return (new self($value->tail()))($condition->tail);
        }
        return false;
    }

    /**
     * Checks if a condition matches a list with wildcarded tail.
     * Recursively matches the head.
     *
     * @param mixed $condition Pattern to match
     * @param mixed $value Value to match against
     * @return bool True if matches Cons(head, _)
     */
    private function matchesConsWildcardedTail($condition, $value): bool
    {
        if ($condition instanceof WildcardedCons && $condition->tail == _ && $value instanceof ImmList) {
            return (new self($value->head))($condition->head);
        }
        return false;
    }

    /**
     * Checks if a condition matches a non-empty list with wildcard.
     *
     * @param mixed $condition Pattern to match
     * @param mixed $value Value to match against
     * @return bool True if matches Nel(_)
     */
    private function matchesWildcardedNel($condition, $value): bool
    {
        return $condition instanceof NonEmptyList && $condition == Nel(_) &&
               $value instanceof NonEmptyList && $value->length > 0;
    }

    /**
     * Handles reference-based pattern matching.
     * Delegates to specific reference matchers based on type.
     *
     * @param mixed $condition Referenced pattern
     * @param mixed $value Value to match against
     * @return bool True if reference matching succeeded
     */
    private function matchByReference($condition, $value): bool
    {
        if ($condition instanceof GenericReferenced) {
            return $this->matchGenericByReference($condition, $value, $condition->class);
        }
        return match (true) {
            $this->matchListByReference($condition, $value),
            $this->matchNelByReference($condition, $value),
            $this->matchConsByReference($condition, $value),
            $this->matchListHeadByReference($condition, $value) => true,
            default => false
        };
    }

    /**
     * Matches a cons cell by reference.
     * Extracts both head and tail into references.
     *
     * @param mixed $condition Referenced pattern
     * @param mixed $value Value to match against
     * @return bool True if matching succeeded
     */
    private function matchConsByReference($condition, $value): bool
    {
        if ($condition instanceof ReferencedCons && $value instanceof ConsType) {
            $condition->head = $value->head;
            $condition->tail = $value->tail;

            return true;
        }

        return false;
    }

    /**
     * Matches a non empty list by reference.
     * Extracts both head and tail into references.
     *
     * @param mixed $condition Referenced pattern
     * @param mixed $value Value to match against
     * @return bool True if matching succeeded
     */
    private function matchNelByReference($condition, $value): bool
    {
        if ($condition instanceof ReferencedNel && $value instanceof NonEmptyList) {
            $condition->head = $value->head;
            $condition->tail = $value->tail;

            return true;
        }

        return false;
    }

    /**
     * Matches generic class instances by reference.
     * Extracts constructor parameters into references.
     *
     * @param GenericReferenced $condition Referenced pattern
     * @param object $object Object to match against
     * @param string $class Expected class name
     * @return bool True if matching succeeded
     * @throws \Error If constructor param names don't match properties
     */
    private function matchGenericByReference($condition, $object, $class): bool
    {
        if ($condition instanceof GenericReferenced && is_object($object) && get_class($object) === $class) {
            $reflected = new \ReflectionClass($object);
            $parameters = $reflected->getConstructor()->getParameters();

            if (count($parameters) === 1 && $parameters[0]->isVariadic()) {
                return $this->matchVariadicByReference($condition, $object, $reflected, $parameters[0]->getName());
            }

            // The pattern has to account for every part the class is built from.
            // A class that is built from none, such as one inheriting a
            // constructor that declares no parameters, can be taken apart into
            // nothing, and matches no pattern that asks for a part.
            if (count($parameters) !== $condition->arity) {
                return false;
            }

            for ($i = 1; $i <= count($parameters); $i++) {
                $property = $this->propertyNamed($reflected, $parameters[$i - 1]->getName());

                if ($property === null) {
                    throw new \Error("To use generic pattern matching you have to name the constructor argument as you ".
                        "have named the class property");
                }

                $condition->{"_$i"} = $property->getValue($object);
            }

            return true;
        }

        return false;
    }

    /**
     * Matches a class built from a variadic constructor, such as a Tuple.
     *
     * The values are held together in one property, so they are taken apart and
     * bound one by one. The pattern matches only when the object holds as many
     * values as the pattern has references for, so that Pair($x, $y) does not
     * match a tuple of three.
     *
     * @param GenericReferenced $condition Referenced pattern
     * @param object $object Object to match against
     * @param \ReflectionClass $reflected The reflected object
     * @param string $name Name of the property holding the values
     * @return bool True if matching succeeded
     * @throws \Error If the variadic constructor param does not name a property
     */
    private function matchVariadicByReference($condition, $object, \ReflectionClass $reflected, string $name): bool
    {
        $property = $this->propertyNamed($reflected, $name);

        if ($property === null) {
            throw new \Error("To use generic pattern matching you have to name the constructor argument as you ".
                "have named the class property");
        }

        $values = $property->getValue($object);

        if (!is_array($values) || count($values) !== $condition->arity) {
            return false;
        }

        for ($i = 1; $i <= count($values); $i++) {
            $condition->{"_$i"} = $values[$i - 1];
        }

        return true;
    }

    /**
     * Finds a property by name on a class or on any of its parents.
     *
     * A property is looked up through the class hierarchy, and read whatever
     * its visibility, so that a value can be extracted from a class that keeps
     * it protected, or that inherits it, as Right and Left inherit theirs
     * from Either.
     *
     * @param \ReflectionClass $reflected The class to look the property up on
     * @param string $name The name of the property
     * @return \ReflectionProperty|null The property, or null if there is none
     */
    private function propertyNamed(\ReflectionClass $reflected, string $name): ?\ReflectionProperty
    {
        for ($class = $reflected; $class !== false; $class = $class->getParentClass()) {
            if ($class->hasProperty($name)) {
                return $class->getProperty($name);
            }
        }

        return null;
    }

    /**
     * Matches list with tail by reference.
     * Extracts both head and tail into references.
     *
     * @param mixed $condition Referenced pattern
     * @param mixed $value Value to match against
     * @return bool True if matching succeeded
     */
    private function matchListByReference($condition, $value): bool
    {
        if ($condition instanceof ListWithTail && $value instanceof ImmList) {
            if ($condition->head == null) {
                $condition->head = $value->head;
            } elseif ($condition->head != $value->head) {
                return false;
            }
            if ($condition->tail == null) {
                $condition->tail = $value->tail;
            } elseif ($condition->tail != $value->tail) {
                return false;
            }
            return true;
        }
        return false;
    }

    /**
     * Matches list head by reference.
     * Extracts head and ensures tail is Nil.
     *
     * @param mixed $condition Referenced pattern
     * @param mixed $value Value to match against
     * @return bool True if matching succeeded
     */
    private function matchListHeadByReference($condition, $value): bool
    {
        if ($condition instanceof ListNoTail && $value instanceof ImmList) {
            $condition->head = $value->head;
            if ($value->tail != Nil()) {
                return false;
            }
            return true;
        }
        return false;
    }
}
