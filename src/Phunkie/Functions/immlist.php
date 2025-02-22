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
    use Phunkie\Types\Cons;
    use Phunkie\Types\ImmList;
    use Phunkie\Types\Nil;
    use Phunkie\Types\NonEmptyList;

    /**
     * Creates an immutable list.
     * 
     * Creates an ImmList containing the given values, or Nil for empty list.
     *
     * Example:
     * ```php
     * ImmList(1, 2, 3);     // ImmList(1, 2, 3)
     * ImmList();            // Nil
     * ImmList("a", "b");    // ImmList("a", "b")
     * ```
     *
     * @template A
     * @param A ...$values Values to store in list
     * @return ImmList<A> The immutable list
     */
    function ImmList(...$values): ImmList { return match(count($values)) {
        0 => Nil(),
        default => new ImmList(...$values) };
    }

    /**
     * Creates an empty immutable list.
     * 
     * Returns the Nil singleton representing an empty list.
     *
     * Example:
     * ```php
     * Nil();  // Empty list
     * ```
     *
     * @template A
     * @return ImmList<A> Empty list
     */
    function Nil(): ImmList
    {
        return new Nil();
    }

    /**
     * Creates a cons cell (linked list node).
     * 
     * Constructs a list cell with a head value and tail list.
     * Nil arguments are converted to empty lists.
     *
     * Example:
     * ```php
     * Cons(1, Cons(2, Nil())); // ImmList(1, 2)
     * Cons("a", Nil);          // ImmList("a")
     * ```
     *
     * @template A
     * @param A $head The value for this cell
     * @param ImmList<A> $tail The rest of the list
     * @return Cons<A> The cons cell
     */
    function Cons($head, $tail)
    {
        if ($head == Nil) {
            $head = Nil();
        }
        if ($tail == Nil) {
            $tail = Nil();
        }
        return new Cons($head, $tail);
    }

    /**
     * Creates a non-empty list.
     * 
     * Creates a NonEmptyList that is guaranteed to contain at least one element.
     *
     * Example:
     * ```php
     * Nel(1, 2, 3);  // NonEmptyList(1, 2, 3)
     * Nel("a");      // NonEmptyList("a")
     * ```
     *
     * @template A
     * @param A ...$values Values to store (at least one required)
     * @return NonEmptyList<A> The non-empty list
     */
    function Nel(...$values): NonEmptyList
    {
        return new NonEmptyList(...$values);
    }
}

namespace Phunkie\Functions\immlist {

    use Phunkie\Types\ImmList;
    use Phunkie\Types\ImmList\NoSuchElementException;
    use function Phunkie\Functions\currying\applyPartially;
    use function Phunkie\Functions\immlist\local\assertListOrString;
    use const Phunkie\Functions\immlist\local\FIRST_ARGUMENT;
    use const Phunkie\Functions\immlist\local\SECOND_ARGUMENT;

    /**
     * Gets the first element of a list or string.
     * 
     * Returns the first element of a non-empty list or string.
     * Throws if empty.
     *
     * Example:
     * ```php
     * head(ImmList(1,2,3));  // 1
     * head("abc");           // "a"
     * head(Nil());           // throws NoSuchElementException
     * ```
     *
     * @template A
     * @param ImmList<A>|string $listOrString List or string to get head from
     * @return A The first element
     * @throws NoSuchElementException If list/string is empty
     */
    const head = "\\Phunkie\\Functions\\immlist\\head";
    function head($listOrString)
    {
        assertListOrString($listOrString, FIRST_ARGUMENT, head);

        if ($listOrString instanceof ImmList) {
            return $listOrString->head();
        }

        if (strlen($listOrString) != 0) {
            return $listOrString[0];
        }

        throw new NoSuchElementException("head of empty list");
    }

    /**
     * Gets all elements except the last.
     * 
     * Returns a new list/string containing all elements except the last.
     * Throws if empty.
     *
     * Example:
     * ```php
     * init(ImmList(1,2,3));  // ImmList(1,2)
     * init("abc");           // "ab"
     * init(Nil());           // throws BadMethodCallException
     * ```
     *
     * @template A
     * @param ImmList<A>|string $listOrString List or string to get init from
     * @return ImmList<A>|string All but last element
     * @throws \BadMethodCallException If list/string is empty
     */
    const init = "\\Phunkie\\Functions\\immlist\\init";
    function init($listOrString)
    {
        assertListOrString($listOrString, FIRST_ARGUMENT, init);
        if ($listOrString instanceof ImmList) {
            return $listOrString->init();
        }

        if (strlen($listOrString)) {
            return substr($listOrString, 0, strlen($listOrString) - 1);
        }

        throw new \BadMethodCallException("empty init");
    }

    /**
     * Gets all elements except the first.
     * 
     * Returns a new list/string containing all elements after the first.
     * Returns empty list/string if input is empty.
     *
     * Example:
     * ```php
     * tail(ImmList(1,2,3));  // ImmList(2,3)
     * tail("abc");           // "bc"
     * tail(Nil());           // Nil
     * tail("");              // ""
     * ```
     *
     * @template A
     * @param ImmList<A>|string $listOrString List or string to get tail from
     * @return ImmList<A>|string All but first element
     */
    const tail = "\\Phunkie\\Functions\\immlist\\tail";
    function tail($listOrString)
    {
        assertListOrString($listOrString, FIRST_ARGUMENT, tail);
        if ($listOrString instanceof ImmList) {
            return $listOrString->tail();
        }

        if (strlen($listOrString) > 1) {
            return substr($listOrString, 1);
        }
        if (strlen($listOrString) == 0) {
            return "";
        }
        return "";
    }

    /**
     * Gets the last element of a list or string.
     * 
     * Returns the last element of a non-empty list or string.
     * Throws if empty.
     *
     * Example:
     * ```php
     * last(ImmList(1,2,3));  // 3
     * last("abc");           // "c"
     * last(Nil());           // throws NoSuchElementException
     * last("");              // throws NoSuchElementException
     * ```
     *
     * @template A
     * @param ImmList<A>|string $listOrString List or string to get last from
     * @return A The last element
     * @throws NoSuchElementException If list/string is empty
     */
    const last = "\\Phunkie\\Functions\\immlist\\last";
    function last($listOrString)
    {
        assertListOrString($listOrString, FIRST_ARGUMENT, last);
        if ($listOrString instanceof ImmList) {
            return $listOrString->last();
        }

        if (strlen($listOrString) == 0) {
            throw new NoSuchElementException("last of empty list");
        }

        return $listOrString[strlen($listOrString) - 1];
    }

    /**
     * Reverses a list or string.
     * 
     * Returns a new list/string with elements in reverse order.
     *
     * Example:
     * ```php
     * reverse(ImmList(1,2,3));  // ImmList(3,2,1)
     * reverse("abc");           // "cba"
     * reverse(Nil());           // Nil
     * reverse("");              // ""
     * ```
     *
     * @template A
     * @param ImmList<A>|string $listOrString List or string to reverse
     * @return ImmList<A>|string Reversed list/string
     */
    const reverse = "\\Phunkie\\Functions\\immlist\\reverse";
    function reverse($listOrString)
    {
        assertListOrString($listOrString, FIRST_ARGUMENT, reverse);
        return $listOrString instanceof ImmList ? $listOrString->reverse() : strrev($listOrString);
    }

    /**
     * Gets the length of a list or string.
     * 
     * Returns the number of elements in a list or characters in a string.
     *
     * Example:
     * ```php
     * length(ImmList(1,2,3));  // 3
     * length("abc");           // 3
     * length(Nil());           // 0
     * length("");              // 0
     * ```
     *
     * @param ImmList<mixed>|string $listOrString List or string to measure
     * @return int The length
     */
    const length = "\\Phunkie\\Functions\\immlist\\length";
    function length($listOrString): int
    {
        assertListOrString($listOrString, FIRST_ARGUMENT, length);
        return $listOrString instanceof ImmList ? $listOrString->length : strlen($listOrString);
    }

    /**
     * Concatenates multiple lists or strings.
     * 
     * Combines multiple lists into a single list, or strings into a single string.
     * Lists and strings cannot be mixed.
     *
     * Example:
     * ```php
     * concat(ImmList(1,2), ImmList(3,4));  // ImmList(1,2,3,4)
     * concat("ab", "cd", "ef");            // "abcdef"
     * concat(Nil(), ImmList(1,2));         // ImmList(1,2)
     * ```
     *
     * @template A
     * @param ImmList<A>|string ...$items Lists or strings to concatenate
     * @return ImmList<A>|string Combined list/string
     */
    const concat = "\\Phunkie\\Functions\\immlist\\concat";
    function concat(...$items)
    {
        $concatLists = function (...$lists) {
            $result = [];
            foreach ($lists as $item) {
                $result = !$item instanceof ImmList ? array_merge($result, [$item]) : array_merge($result, $item->toArray());
            }
            return ImmList(...$result);
        };
        $concatStrings = fn (...$s) => array_reduce($s, fn ($a, $b) => $a . $b, "");
        if ($items[0] instanceof ImmList || is_array($items[0])) {
            return $concatLists(...$items);
        }
        return $concatStrings(...$items);
    }

    /**
     * Takes first n elements from a list or string.
     * 
     * Returns a new list/string containing only the first n elements.
     * If n is negative, returns empty list/string.
     *
     * Example:
     * ```php
     * take(2)(ImmList(1,2,3));  // ImmList(1,2)
     * take(2)("abc");           // "ab"
     * take(0)(ImmList(1,2,3));  // Nil
     * take(-1)("abc");          // ""
     * ```
     *
     * @template A
     * @param int $n Number of elements to take
     * @return callable(ImmList<A>|string):ImmList<A>|string Function expecting list/string
     */
    const take = "\\Phunkie\\Functions\\immlist\\take";
    function take(int $n)
    {
        return applyPartially([$n], func_get_args(), function ($listOrString) use ($n) {
            assertListOrString($listOrString, SECOND_ARGUMENT, take);
            if (is_string($listOrString)) {
                return substr($listOrString, 0, $n < 0 ? 0 : $n);
            }
            return ImmList(...array_slice($listOrString->toArray(), 0, $n < 0 ? 0 : $n));
        });
    }

    /**
     * Takes elements while a predicate is true.
     * 
     * Returns a new list/string containing elements from the start until
     * the predicate returns false.
     *
     * Example:
     * ```php
     * $isEven = fn($x) => $x % 2 == 0;
     * takeWhile($isEven)(ImmList(2,4,5,6));  // ImmList(2,4)
     * 
     * $isLower = fn($c) => ctype_lower($c);
     * takeWhile($isLower)("abcD");           // "abc"
     * ```
     *
     * @template A
     * @param callable(A):bool $f Predicate function
     * @return callable(ImmList<A>|string):ImmList<A>|string Function expecting list/string
     */
    const takeWhile = "\\Phunkie\\Functions\\immlist\\takeWhile";
    function takeWhile(callable $f)
    {
        return applyPartially([$f], func_get_args(), function ($listOrString) use ($f) {
            assertListOrString($listOrString, SECOND_ARGUMENT, takeWhile);
            if (is_string($listOrString)) {
                return ImmList(...str_split($listOrString))->takeWhile($f)->mkString("");
            }
            return $listOrString->takeWhile($f);
        });
    }

    /**
     * Drops first n elements from a list or string.
     * 
     * Returns a new list/string without the first n elements.
     * If n is negative, returns the original list/string.
     *
     * Example:
     * ```php
     * drop(2)(ImmList(1,2,3,4));  // ImmList(3,4)
     * drop(2)("abcd");            // "cd"
     * drop(0)(ImmList(1,2,3));    // ImmList(1,2,3)
     * drop(-1)("abc");            // "abc"
     * ```
     *
     * @template A
     * @param int $n Number of elements to drop
     * @return callable(ImmList<A>|string):ImmList<A>|string Function expecting list/string
     */
    const drop = "\\Phunkie\\Functions\\immlist\\drop";
    function drop(int $n)
    {
        return applyPartially([$n], func_get_args(), function ($listOrString) use ($n) {
            assertListOrString($listOrString, SECOND_ARGUMENT, drop);
            if (is_string($listOrString)) {
                return substr($listOrString, $n < 0 ? 0 : $n);
            }
            return ImmList(...array_slice($listOrString->toArray(), $n < 0 ? 0 : $n));
        });
    }

    /**
     * Drops elements while a predicate is true.
     * 
     * Returns a new list/string without elements from the start while
     * the predicate returns true.
     *
     * Example:
     * ```php
     * $isEven = fn($x) => $x % 2 == 0;
     * dropWhile($isEven)(ImmList(2,4,5,6));  // ImmList(5,6)
     * 
     * $isLower = fn($c) => ctype_lower($c);
     * dropWhile($isLower)("abcD");           // "D"
     * ```
     *
     * @template A
     * @param callable(A):bool $f Predicate function
     * @return callable(ImmList<A>|string):ImmList<A>|string Function expecting list/string
     */
    const dropWhile = "\\Phunkie\\Functions\\immlist\\dropWhile";
    function dropWhile(callable $f)
    {
        return applyPartially([$f], func_get_args(), function ($listOrString) use ($f) {
            assertListOrString($listOrString, SECOND_ARGUMENT, dropWhile);
            if (is_string($listOrString)) {
                return ImmList(...str_split($listOrString))->dropWhile($f)->mkString("");
            }
            return $listOrString->dropWhile($f);
        });
    }

    /**
     * Gets the element at a specific index.
     * 
     * Returns Some containing the element at index n if it exists,
     * or None if the index is out of bounds.
     *
     * Example:
     * ```php
     * nth(1)(ImmList(1,2,3));  // Some(2)
     * nth(1)("abc");           // Some("b")
     * nth(5)(ImmList(1,2));    // None
     * nth(5)("abc");           // None
     * ```
     *
     * @template A
     * @param int $nth Index to get
     * @return callable(ImmList<A>|string):Option<A> Function expecting list/string
     */
    const nth = "\\Phunkie\\Functions\\immlist\\nth";
    function nth(int $nth)
    {
        return applyPartially([$nth], func_get_args(), function ($listOrString) use ($nth) {
            assertListOrString($listOrString, SECOND_ARGUMENT, nth);
            if (is_string($listOrString)) {
                return $nth > strlen($listOrString) - 1 ? None() : Some($listOrString[$nth]);
            }
            return array_key_exists($nth, $listOrString->toArray()) ? Some($listOrString->toArray()[$nth]) : None();
        });
    }

    /**
     * Filters elements matching a predicate.
     * 
     * Returns a new list/string containing only elements that satisfy
     * the predicate function.
     *
     * Example:
     * ```php
     * $isEven = fn($x) => $x % 2 == 0;
     * filter($isEven)(ImmList(1,2,3,4));  // ImmList(2,4)
     * 
     * $isLower = fn($c) => ctype_lower($c);
     * filter($isLower)("aBcD");           // "ac"
     * ```
     *
     * @template A
     * @param callable(A):bool $f Predicate function
     * @return callable(ImmList<A>|string):ImmList<A>|string Function expecting list/string
     */
    const filter = "\\Phunkie\\Functions\\immlist\\filter";
    function filter($f)
    {
        return applyPartially([$f], func_get_args(), function ($listOrString) use ($f) {
            assertListOrString($listOrString, SECOND_ARGUMENT, filter);
            if (is_string($listOrString)) {
                return ImmList(...str_split($listOrString))->filter($f)->mkString("");
            }
            return $listOrString->filter($f);
        });
    }

    /**
     * Filters out elements matching a predicate.
     * 
     * Returns a new list/string containing only elements that do not
     * satisfy the predicate function (opposite of filter).
     *
     * Example:
     * ```php
     * $isEven = fn($x) => $x % 2 == 0;
     * reject($isEven)(ImmList(1,2,3,4));  // ImmList(1,3)
     * 
     * $isLower = fn($c) => ctype_lower($c);
     * reject($isLower)("aBcD");           // "BD"
     * ```
     *
     * @template A
     * @param callable(A):bool $f Predicate function
     * @return callable(ImmList<A>|string):ImmList<A>|string Function expecting list/string
     */
    const reject = "\\Phunkie\\Functions\\immlist\\reject";
    function reject($f)
    {
        return applyPartially([$f], func_get_args(), function ($listOrString) use ($f) {
            assertListOrString($listOrString, SECOND_ARGUMENT, reject);
            if (is_string($listOrString)) {
                return ImmList(...str_split($listOrString))->reject($f)->mkString("");
            }
            return $listOrString->reject($f);
        });
    }

    /**
     * Reduces a list or string to a single value.
     * 
     * Applies a binary function to each element and an accumulator,
     * reducing the list/string to a single value.
     *
     * Example:
     * ```php
     * $sum = fn($acc, $x) => $acc + $x;
     * reduce($sum)(ImmList(1,2,3));  // 6
     * 
     * $concat = fn($acc, $c) => $acc . strtoupper($c);
     * reduce($concat)("abc");         // "ABC"
     * ```
     *
     * @template A,B
     * @param callable(B,A):B $f Binary reduction function
     * @return callable(ImmList<A>|string):B Function expecting list/string
     */
    const reduce = "\\Phunkie\\Functions\\immlist\\reduce";
    function reduce($f)
    {
        return applyPartially([$f], func_get_args(), function ($listOrString) use ($f) {
            assertListOrString($listOrString, SECOND_ARGUMENT, reduce);
            if (is_string($listOrString)) {
                return ImmList(...str_split($listOrString))->reduce($f);
            }
            return $listOrString->reduce($f);
        });
    }

    /**
     * Transposes a list of lists.
     * 
     * Converts rows into columns and columns into rows in a list of lists.
     * All inner lists must have the same length.
     *
     * Example:
     * ```php
     * $matrix = ImmList(
     *     ImmList(1, 2, 3),
     *     ImmList(4, 5, 6)
     * );
     * transpose($matrix);
     * // ImmList(
     * //     ImmList(1, 4),
     * //     ImmList(2, 5),
     * //     ImmList(3, 6)
     * // )
     * ```
     *
     * @template A
     * @param ImmList<ImmList<A>> $list List of lists to transpose
     * @return ImmList<ImmList<A>> Transposed list of lists
     */
    const transpose = "\\Phunkie\\Functions\\immlist\\transpose";
    function transpose(ImmList $list)
    {
        return $list->transpose();
    }
}

namespace Phunkie\Functions\immlist\local {

    use Phunkie\Types\ImmList;
    use function Phunkie\Functions\type\normaliseType;

    /**
     * Constants for argument position in error messages.
     */
    const FIRST_ARGUMENT = 1;
    const SECOND_ARGUMENT = 2;
    const THIRD_ARGUMENT = 2;

    /**
     * Asserts that a value is a list or string.
     * 
     * Validates that a value is either an ImmList or string.
     * Throws TypeError if validation fails.
     *
     * @param mixed $list Value to validate
     * @param int $argument Argument position for error message
     * @param string $functionCalled Function name for error message
     * @throws \TypeError If value is not list or string
     */
    function assertListOrString($list, $argument, $functionCalled)
    {
        if ($list instanceof ImmList || is_string($list)) {
            return;
        }
        formatError(
            "Argument %s passed to %s must be an instance of ImmList or a String, " .
            (gettype($list) == 'object' ? get_class($list) : normaliseType(gettype($list))) . " given",
            $argument,
            $functionCalled
        );
    }

    /**
     * Formats and throws a type error.
     *
     * @param string $error Error message template
     * @param int $argumentNumber Argument position
     * @param string $functionCalled Function name
     * @throws \TypeError Always
     */
    function formatError($error, $argumentNumber, $functionCalled)
    {
        throw new \TypeError(sprintf($error, $argumentNumber, $functionCalled));
    }
}
