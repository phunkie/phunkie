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
    use Phunkie\Cats\StateT as StateTType;
    use Phunkie\Cats\Id as IdType;
    use Phunkie\Cats\IO as IOType;
    use Phunkie\Cats\State as StateType;
    use Phunkie\Cats\Reader as ReaderType;
    use Phunkie\Cats\Kleisli as KleisliType;
    use Phunkie\Cats\OptionT as OptionTType;
    use Phunkie\Cats\EitherT as EitherTType;
    use Phunkie\Types\ImmString as ImmStringType;
    use Phunkie\Types\ImmInteger as ImmIntegerType;
    use Phunkie\Types\ImmSet as ImmSetType;

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
     * Creates a pattern that matches an Id and binds the value it holds.
     *
     * Example:
     * ```php
     * $on = pmatch(new Id(42));
     * $result = match (true) {
     *     $on(Id($value)) => $value  // $value is 42
     * };
     * ```
     *
     * @param mixed $value Variable that receives the value held by the Id
     * @return GenericReferenced Pattern matching an Id
     */
    function Id(&$value): GenericReferenced
    {
        return new GenericReferenced(IdType::class, $value);
    }

    /**
     * Creates a pattern that matches an ImmString and binds the string it holds.
     *
     * Example:
     * ```php
     * $on = pmatch(new ImmString("hi"));
     * $result = match (true) {
     *     $on(ImmString($s)) => $s  // $s is "hi"
     * };
     * ```
     *
     * @param mixed $value Variable that receives the string held by the ImmString
     * @return GenericReferenced Pattern matching an ImmString
     */
    function ImmString(&$value): GenericReferenced
    {
        return new GenericReferenced(ImmStringType::class, $value);
    }

    /**
     * Creates a pattern that matches an ImmInteger and binds the number it holds.
     *
     * Example:
     * ```php
     * $on = pmatch(new ImmInteger(7));
     * $result = match (true) {
     *     $on(ImmInteger($i)) => $i  // $i is 7
     * };
     * ```
     *
     * @param mixed $value Variable that receives the number held by the ImmInteger
     * @return GenericReferenced Pattern matching an ImmInteger
     */
    function ImmInteger(&$value): GenericReferenced
    {
        return new GenericReferenced(ImmIntegerType::class, $value);
    }

    /**
     * Creates a pattern that matches an ImmSet and binds each of its elements.
     *
     * The set must hold as many elements as the pattern names, so a pattern of
     * two does not match a set of three.
     *
     * Example:
     * ```php
     * $on = pmatch(ImmSet(1, 2));
     * $result = match (true) {
     *     $on(ImmSet($a, $b)) => $a + $b  // 3
     * };
     * ```
     *
     * @param mixed ...$elements Variables that receive the elements of the ImmSet
     * @return GenericReferenced Pattern matching an ImmSet
     */
    function ImmSet(&...$elements): GenericReferenced
    {
        return new GenericReferenced(ImmSetType::class, ...$elements);
    }

    /**
     * Creates a pattern that matches an IO and binds the thunk it wraps.
     *
     * Example:
     * ```php
     * $on = pmatch(new IO(fn () => 42));
     * $result = match (true) {
     *     $on(IO($thunk)) => $thunk()  // $thunk is the wrapped thunk, so 42
     * };
     * ```
     *
     * @param mixed $thunk Variable that receives the thunk wrapped by the IO
     * @return GenericReferenced Pattern matching an IO
     */
    function IO(&$thunk): GenericReferenced
    {
        return new GenericReferenced(IOType::class, $thunk);
    }

    /**
     * Creates a pattern that matches a State and binds the transition it wraps.
     *
     * Example:
     * ```php
     * $on = pmatch(new State(fn ($s) => Pair($s, $s + 1)));
     * $result = match (true) {
     *     $on(State($run)) => $run(1)->_2  // $run is the transition, so 2
     * };
     * ```
     *
     * @param mixed $run Variable that receives the transition wrapped by the State
     * @return GenericReferenced Pattern matching a State
     */
    function State(&$run): GenericReferenced
    {
        return new GenericReferenced(StateType::class, $run);
    }

    /**
     * Creates a pattern that matches a Reader and binds the function it wraps.
     *
     * Example:
     * ```php
     * $on = pmatch(new Reader(fn ($r) => $r * 2));
     * $result = match (true) {
     *     $on(Reader($run)) => $run(21)  // $run is the wrapped function, so 42
     * };
     * ```
     *
     * @param mixed $run Variable that receives the function wrapped by the Reader
     * @return GenericReferenced Pattern matching a Reader
     */
    function Reader(&$run): GenericReferenced
    {
        return new GenericReferenced(ReaderType::class, $run);
    }

    /**
     * Creates a pattern that matches a Kleisli and binds the function it wraps.
     *
     * Example:
     * ```php
     * $on = pmatch(new Kleisli(fn ($x) => new Id($x + 1)));
     * $result = match (true) {
     *     $on(Kleisli($run)) => ($run(1))()  // $run is the wrapped function, so 2
     * };
     * ```
     *
     * @param mixed $run Variable that receives the function wrapped by the Kleisli
     * @return GenericReferenced Pattern matching a Kleisli
     */
    function Kleisli(&$run): GenericReferenced
    {
        return new GenericReferenced(KleisliType::class, $run);
    }

    /**
     * Creates a pattern that matches an OptionT and binds the monad it wraps.
     *
     * Example:
     * ```php
     * $on = pmatch(new OptionT(new Id(Some(42))));
     * $result = match (true) {
     *     $on(OptionT($monad)) => $monad()->get()  // $monad is Id(Some(42))
     * };
     * ```
     *
     * @param mixed $monad Variable that receives the monad wrapped by the OptionT
     * @return GenericReferenced Pattern matching an OptionT
     */
    function OptionT(&$monad): GenericReferenced
    {
        return new GenericReferenced(OptionTType::class, $monad);
    }

    /**
     * Creates a pattern that matches an EitherT and binds the monad it wraps.
     *
     * Example:
     * ```php
     * $on = pmatch(new EitherT(new Id(Right(42))));
     * $result = match (true) {
     *     $on(EitherT($monad)) => $monad()->getOrElse(0)  // $monad is Id(Right(42))
     * };
     * ```
     *
     * @param mixed $monad Variable that receives the monad wrapped by the EitherT
     * @return GenericReferenced Pattern matching an EitherT
     */
    function EitherT(&$monad): GenericReferenced
    {
        return new GenericReferenced(EitherTType::class, $monad);
    }

    /**
     * Creates a pattern that matches a StateT and binds the transition it wraps.
     *
     * Example:
     * ```php
     * $on = pmatch(new StateT(fn ($s) => new Id(Pair($s, $s + 1))));
     * $result = match (true) {
     *     $on(StateT($run)) => $run(1)  // $run is the state transition
     * };
     * ```
     *
     * @param mixed $run Variable that receives the transition wrapped by the StateT
     * @return GenericReferenced Pattern matching a StateT
     */
    function StateT(&$run): GenericReferenced
    {
        return new GenericReferenced(StateTType::class, $run);
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
     * Creates a pattern that matches a cons cell, binding head and tail.
     *
     * Matches a Cons and nothing else: an ordinary list is matched with
     * ListWithTail, even when it holds something.
     *
     * Example:
     * ```php
     * $on = pmatch(Cons(1, ImmList(2, 3)));
     * $result = match (true) {
     *     $on(Cons($x, $xs)) => $x + $xs->head  // $x is 1, $xs is ImmList(2, 3)
     * };
     * ```
     *
     * @param mixed $head Variable that receives the head of the list
     * @param mixed $tail Variable that receives the list after the head
     * @return Cons Pattern matching a cons cell
     */
    function Cons(&$head, &$tail): Cons
    {
        return new Cons($head, $tail);
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
