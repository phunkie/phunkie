<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace {

    use Phunkie\Types\None;
    use Phunkie\Types\Option;
    use Phunkie\Types\Some;

    /**
     * Creates an Option from a value.
     * 
     * Returns Some if value is not null,
     * None if value is null.
     *
     * Example:
     * ```php
     * Option(42);      // Some(42)
     * Option(null);    // None
     * Option("test");  // Some("test")
     * ```
     *
     * @template A
     * @param A|null $value Value to wrap
     * @return Option<A> Optional value
     */
    function Option($t)
    {
        return $t === null ? None() : Some($t);
    }

    /**
     * Creates a Some containing a value.
     * 
     * Wraps a value in Some to indicate presence.
     * Use when you know a value exists.
     *
     * Example:
     * ```php
     * Some(42);        // Some(42)
     * Some("hello");   // Some("hello")
     * Some([1,2,3]);   // Some([1,2,3])
     * ```
     *
     * @template A
     * @param A $value Value to wrap
     * @return Option<A> Some containing value
     */
    function Some(...$t): Option
    {
        return Some::instance(...$t);
    }

    /**
     * Creates a None value.
     * 
     * Represents absence of a value.
     * Use when a value doesn't exist.
     *
     * Example:
     * ```php
     * None();          // None
     * Option(null);    // None
     * 
     * // Common usage with conditionals
     * $result = $value > 0 ? Some($value) : None();
     * ```
     *
     * @template A
     * @return Option<A> None value
     */
    function None(): Option
    {
        return None::instance();
    }
}

namespace Phunkie\Functions\option {

    use Phunkie\Types\ImmList;
    use Phunkie\Types\Option;

    const isDefined = "\\Phunkie\\Functions\\option\\isDefined";
    const isJust = "\\Phunkie\\Functions\\option\\isDefined";
    const isSome = "\\Phunkie\\Functions\\option\\isDefined";
    /**
     * Checks if an Option contains a value.
     * 
     * Returns true for Some, false for None.
     * Also available as isJust() and isSome().
     *
     * Example:
     * ```php
     * isDefined(Some(42));  // true
     * isDefined(None());    // false
     * ```
     *
     * @template A
     * @param Option<A> $x Option to check
     * @return bool True if Some
     */
    function isDefined(Option $x): bool
    {
        return $x->isDefined();
    }

    const isNone = "\\Phunkie\\Functions\\option\\isNone";
    const isNothing = "\\Phunkie\\Functions\\option\\isNone";
    const isEmpty = "\\Phunkie\\Functions\\option\\isNone";
    /**
     * Checks if an Option is empty.
     * 
     * Returns true for None, false for Some.
     * Also available as isNothing() and isEmpty().
     *
     * Example:
     * ```php
     * isNone(None());      // true
     * isNone(Some(42));    // false
     * ```
     *
     * @template A
     * @param Option<A> $x Option to check
     * @return bool True if None
     */
    function isNone(Option $x): bool
    {
        return $x->isEmpty();
    }

    const fromSome = "\\Phunkie\\Functions\\option\\fromSome";
    const fromJust = "\\Phunkie\\Functions\\option\\fromSome";
    /**
     * Gets the value from a Some.
     * 
     * Returns the contained value if Some,
     * throws Error if None.
     * Also available as fromJust().
     *
     * Example:
     * ```php
     * fromSome(Some(42));  // 42
     * fromSome(None());    // throws Error
     * ```
     *
     * @template A
     * @param Option<A> $x Option to get value from
     * @return A The contained value
     * @throws \Error if None
     */
    function fromSome(Option $x)
    {
        if (isNone($x)) {
            throw new \Error("Can not get a value from None.");
        }
        return $x->get();
    }

    const listToOption = "\\Phunkie\\Functions\\option\\listToOption";
    /**
     * Converts a list to an Option.
     * 
     * Returns Some containing the first element if list is non-empty,
     * None if list is empty.
     *
     * Example:
     * ```php
     * listToOption(ImmList(1,2,3));  // Some(1)
     * listToOption(ImmList());       // None
     * ```
     *
     * @template A
     * @param ImmList<A> $xs List to convert
     * @return Option<A> Optional first element
     */
    function listToOption(ImmList $xs): Option
    {
        return $xs->isEmpty() ? None() : Some($xs->head);
    }

    const optionToList = "\\Phunkie\\Functions\\option\\optionToList";
    /**
     * Converts an Option to a list.
     * 
     * Returns singleton list containing the value if Some,
     * empty list if None.
     *
     * Example:
     * ```php
     * optionToList(Some(42));  // ImmList(42)
     * optionToList(None());    // ImmList()
     * ```
     *
     * @template A
     * @param Option<A> $x Option to convert
     * @return ImmList<A> Resulting list
     */
    function optionToList(Option $x): ImmList
    {
        return $x->isEmpty() ? Nil() : ImmList($x->get());
    }
}
