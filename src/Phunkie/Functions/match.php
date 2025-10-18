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

    use Phunkie\PatternMatching\PMatch;
    use Phunkie\PatternMatching\Referenced\GenericReferenced;
    use Phunkie\PatternMatching\Underscore;

    /**
     * Creates a pattern matcher for a value.
     * 
     * Returns a function that accepts pattern matching cases.
     * Each case is a pair of pattern and handler function.
     *
     * Example:
     * ```php
     * $list = ImmList(1, 2, 3);
     * $sum = pmatch($list)(
     *     Nil() ==> fn() => 0,
     *     Cons($x, $xs) ==> fn() => $x + $xs->sum()
     * );
     * ```
     *
     * @template A,B
     * @param A $value Value to match on
     * @return callable(array<callable>):B|GenericReferenced Function accepting cases
     */
    function pmatch(...$values)
    {
        return new PMatch(...$values);
    }

    /**
     * Creates a wildcard pattern.
     * 
     * Used in pattern matching to match any value.
     *
     * Example:
     * ```php
     * pmatch($value)(
     *     1 ==> fn() => "one",
     *     _ ==> fn() => "other"  // matches anything else
     * );
     * ```
     */
    function underscore()
    {
        return new Underscore();
    }
}

namespace Phunkie\PatternMatching\Referenced {

    use Phunkie\Validation\Success as Valid;
    use Phunkie\Validation\Failure as Invalid;

    /**
     * Creates a reference pattern.
     * 
     * Used to capture matched values in variables.
     *
     * Example:
     * ```php
     * pmatch($pair)(
     *     Pair($x, $y) ==> fn() => $x + $y  // captures both elements
     * );
     * ```
     *
     * @param mixed $value Variable to store matched value
     * @return Reference Pattern reference
     */
    function ListWithTail(&$head, &$tail)
    {
        return new ListWithTail($head, $tail);
    }

    /**
     * Creates a list pattern with specific length.
     * 
     * Used to match lists with exact number of elements.
     *
     * Example:
     * ```php
     * pmatch($list)(
     *     List($x, $y) ==> fn() => "$x and $y",  // matches list of 2
     *     _ ==> fn() => "other length"
     * );
     * ```
     *
     * @param mixed ...$elements Element patterns
     * @return ListMatch Pattern for fixed-length list
     */
    function ListNoTail(&$head, $tail)
    {
        return new ListNoTail($head, $tail);
    }

    /**
     * Creates a type pattern.
     * 
     * Used to match values of a specific type.
     *
     * Example:
     * ```php
     * pmatch($value)(
     *     typeOf("string") ==> fn() => "got string",
     *     typeOf("int") ==> fn() => "got number"
     * );
     * ```
     *
     * @param string $type Type name to match
     * @return Type Pattern for type matching
     */
    function Some(&$value)
    {
        return new Some($value);
    }

    /**
     * Creates a type pattern.
     * 
     * Used to match values of a specific type.
     *
     * Example:
     * ```php
     * pmatch($value)(
     *     typeOf("string") ==> fn() => "got string",
     *     typeOf("int") ==> fn() => "got number"
     * );
     * ```
     *
     * @param string $value Type name to match
     * @return GenericReferenced Pattern for type matching
     */
    function Success(&$value): GenericReferenced
    {
        return new GenericReferenced(Valid::class, $value);
    }

    /**
     * Creates a type pattern.
     * 
     * Used to match values of a specific type.
     *
     * Example:
     * ```php
     * pmatch($value)(
     *     typeOf("string") ==> fn() => "got string",
     *     typeOf("int") ==> fn() => "got number"
     * );
     * ```
     *
     * @param string $value Type name to match
     * @return GenericReferenced Pattern for type matching
     */
    function Failure(&$value): GenericReferenced
    {
        return new GenericReferenced(Invalid::class, $value);
    }
}

namespace Phunkie\PatternMatching\Wildcarded {

    /**
     * Creates a wildcard pattern for matching ImmLists.
     * 
     * Used to match list structure with wildcards for head and tail.
     * Automatically converts Nil arguments to empty lists.
     *
     * Example:
     * ```php
     * pmatch($list)(
     *     ImmList(_, Nil) ==> fn() => "single element list",
     *     ImmList(1, _) ==> fn() => "list starting with 1",
     *     ImmList(_, _) ==> fn() => "list with at least 2 elements"
     * );
     * ```
     *
     * @param mixed $head Head pattern (can be wildcard)
     * @param mixed $tail Tail pattern (can be wildcard)
     * @return ImmList Pattern for list matching with wildcards
     */
    function ImmList($head, $tail)
    {
        if ($head == Nil) {
            $head = Nil();
        }
        if ($tail == Nil) {
            $tail = Nil();
        }
        return new ImmList($head, $tail);
    }
}
