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
    use Phunkie\Types\Right as RightType;
    use Phunkie\Types\Left as LeftType;
    use Phunkie\Types\Pair as PairType;
    use Phunkie\Types\Tuple as TupleType;
    use Phunkie\Types\Function1 as Function1Type;

    /**
     * Creates a pattern that matches a Right and binds the value it holds.
     *
     * Example:
     * ```php
     * $on = pmatch(Right(42));
     * $result = match (true) {
     *     $on(Right($value)) => $value  // $value is 42
     * };
     * ```
     *
     * @param mixed $value Variable that receives the value held by the Right
     * @return GenericReferenced Pattern matching a Right
     */
    function Right(&$value): GenericReferenced
    {
        return new GenericReferenced(RightType::class, $value);
    }

    /**
     * Creates a pattern that matches a Left and binds the value it holds.
     *
     * Example:
     * ```php
     * $on = pmatch(Left("boom!"));
     * $result = match (true) {
     *     $on(Right($value)) => "right: " . $value,
     *     $on(Left($value)) => "left: " . $value  // $value is "boom!"
     * };
     * ```
     *
     * @param mixed $value Variable that receives the value held by the Left
     * @return GenericReferenced Pattern matching a Left
     */
    function Left(&$value): GenericReferenced
    {
        return new GenericReferenced(LeftType::class, $value);
    }

    /**
     * Creates a pattern that matches a Pair and binds both of its values.
     *
     * Example:
     * ```php
     * $on = pmatch(Pair(1, 2));
     * $result = match (true) {
     *     $on(Pair($x, $y)) => $x + $y  // $x is 1, $y is 2
     * };
     * ```
     *
     * @param mixed $_1 Variable that receives the first value of the Pair
     * @param mixed $_2 Variable that receives the second value of the Pair
     * @return GenericReferenced Pattern matching a Pair
     */
    function Pair(&$_1, &$_2): GenericReferenced
    {
        return new GenericReferenced(PairType::class, $_1, $_2);
    }

    /**
     * Creates a pattern that matches a Tuple and binds each of its values.
     *
     * The tuple must hold as many values as the pattern names, so a pattern of
     * three does not match a tuple of four. Note that a tuple of two is a Pair,
     * and is matched with the Pair pattern.
     *
     * Example:
     * ```php
     * $on = pmatch(Tuple(1, 2, 3));
     * $result = match (true) {
     *     $on(Tuple($x, $y, $z)) => $x + $y + $z  // 6
     * };
     * ```
     *
     * @param mixed ...$values Variables that receive the values of the Tuple
     * @return GenericReferenced Pattern matching a Tuple
     */
    function Tuple(&...$values): GenericReferenced
    {
        return new GenericReferenced(TupleType::class, ...$values);
    }

    /**
     * Creates a pattern that matches a Function1 and binds the function it wraps.
     *
     * Example:
     * ```php
     * $on = pmatch(Function1::identity());
     * $result = match (true) {
     *     $on(Function1($f)) => $f(42)  // $f is the wrapped function, so 42
     * };
     * ```
     *
     * @param mixed $f Variable that receives the function wrapped by the Function1
     * @return GenericReferenced Pattern matching a Function1
     */
    function Function1(&$f): GenericReferenced
    {
        return new GenericReferenced(Function1Type::class, $f);
    }

    /**
     * Creates a pattern that matches a non empty list, binding head and tail.
     *
     * Matches a NonEmptyList and nothing else: an ordinary list is matched with
     * ListWithTail, even when it happens to hold something.
     *
     * Example:
     * ```php
     * $on = pmatch(Nel(1, 2, 3));
     * $result = match (true) {
     *     $on(Nel($x, $xs)) => $x + $xs->head  // $x is 1, $xs is ImmList(2, 3)
     * };
     * ```
     *
     * @param mixed $head Variable that receives the head of the list
     * @param mixed $tail Variable that receives the tail of the list
     * @return NonEmptyList Pattern matching a non empty list
     */
    function Nel(&$head, &$tail): NonEmptyList
    {
        return new NonEmptyList($head, $tail);
    }

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
