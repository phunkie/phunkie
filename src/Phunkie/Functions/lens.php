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

    use Phunkie\Cats\Lens;

    /**
     * 
     * This module provides various pre-built lenses and lens constructors
     * for common operations like:
     * - Accessing pair components (fst, snd)
     * - Working with sets (contains)
     * - Working with maps (member)
     * - Generating lenses for object fields (makeLenses)
     */
     
     /** 
     * Creates a lens from getter and setter functions.
     * 
     * Basic lens constructor taking explicit get and set functions.
     *
     * Example:
     * ```php
     * $lens = Lens(
     *     fn($data) => $data['name'],           // getter
     *     fn($name, $data) => ['name' => $name] // setter
     * );
     * ```
     *
     * @template S,A
     * @param callable(S):A $getter Function to get value
     * @param callable(A,S):S $setter Function to set value
     * @return Lens<S,A> The lens
     */
    function Lens(callable $g, callable $s)
    {
        return new Lens($g, $s);
    }
}

namespace Phunkie\Functions\lens {

    use Phunkie\Types\ImmMap;
    use Phunkie\Types\ImmSet;
    use Phunkie\Types\Option;
    use Phunkie\Types\Pair;
    use Phunkie\Utils\GenLens;
    use function Phunkie\PatternMatching\Referenced\Some as Maybe;
    use const Phunkie\Functions\function1\identity;

    /**
     * Creates a trivial lens that ignores the structure.
     * 
     * Returns a lens where the getter always returns Unit and
     * the setter always returns the new value.
     *
     * Example:
     * ```php
     * $lens = trivial();
     * $lens->get($anything);  // Unit
     * $lens->set(42, $old);   // 42
     * ```
     *
     * @template A
     * @return Lens<A,Unit> The trivial lens
     */
    const trivial = "\\Phunkie\\Functions\\lens\\lens";
    function trivial()
    {
        return Lens(
            fn ($a) => Unit(),
            fn ($ignore, $a) => $a
        );
    }

    /**
     * Creates an identity lens.
     * 
     * Returns a lens where the getter returns the input unchanged
     * and the setter returns the new value.
     *
     * Example:
     * ```php
     * $lens = self();
     * $lens->get(42);       // 42
     * $lens->set(43, $old); // 43
     * ```
     *
     * @template A
     * @return Lens<A,A> The identity lens
     */
    const self = "\\Phunkie\\Functions\\lens\\self";
    function self()
    {
        return Lens(
            identity,
            fn ($a, $ignore) => $a
        );
    }

    /**
     * Creates a lens focusing on the first element of a Pair.
     * 
     * Example:
     * ```php
     * $pair = Pair(1, "a");
     * $lens = fst();
     * 
     * $lens->get($pair);      // 1
     * $lens->set(2, $pair);   // Pair(2, "a")
     * ```
     *
     * @template A,B
     * @return Lens<Pair<A,B>,A> Lens for first element
     */
    const fst = "\\Phunkie\\Functions\\lens\\fst";
    function fst()
    {
        return Lens(
            fn (Pair $p) => $p->_1,
            fn ($a, Pair $p) => $p->copy(["_1" => $a])
        );
    }

    /**
     * Creates a lens focusing on the second element of a Pair.
     * 
     * Example:
     * ```php
     * $pair = Pair(1, "a");
     * $lens = snd();
     * 
     * $lens->get($pair);      // "a"
     * $lens->set("b", $pair); // Pair(1, "b")
     * ```
     *
     * @template A,B
     * @return Lens<Pair<A,B>,B> Lens for second element
     */
    const snd = "\\Phunkie\\Functions\\lens\\snd";
    function snd()
    {
        return Lens(
            fn (Pair $p) => $p->_2,
            fn ($b, Pair $p) => $p->copy(["_2" => $b])
        );
    }

    /**
     * Creates a lens focusing on set membership.
     * 
     * Returns a lens that views whether an element is in a set,
     * and adds/removes the element when setting true/false.
     *
     * Example:
     * ```php
     * $set = ImmSet(1, 2, 3);
     * $lens = contains(2);
     * 
     * $lens->get($set);        // true
     * $lens->set(false, $set); // ImmSet(1, 3)
     * ```
     *
     * @template A
     * @param A $element Element to check/modify
     * @return Lens<ImmSet<A>,bool> Lens for element membership
     */
    const contains = "\\Phunkie\\Functions\\lens\\contains";
    function contains($element)
    {
        return Lens(
            fn (ImmSet $s) => $s->contains($element),
            fn (ImmSet $s, bool $plusOrMinus) => match ($plusOrMinus) {
                true => $s->plus($element),
                false => $s->minus($element) }
        );
    }

    /**
     * Creates a lens focusing on a map entry.
     * 
     * Returns a lens that views the value at a key,
     * and updates/removes the entry when setting Some/None.
     *
     * Example:
     * ```php
     * $map = ImmMap("a" => 1, "b" => 2);
     * $lens = member("a");
     * 
     * $lens->get($map);           // Some(1)
     * $lens->set(Some(3), $map);  // ImmMap("a" => 3, "b" => 2)
     * $lens->set(None(), $map);   // ImmMap("b" => 2)
     * ```
     *
     * @template K,V
     * @param K $k Key to focus on
     * @return Lens<ImmMap<K,V>,Option<V>> Lens for map entry
     */
    const member = "\\Phunkie\\Functions\\lens\\member";
    function member($k)
    {
        return Lens(
            fn (ImmMap $m) => $m->get($k),
            function (ImmMap $m, Option $v) use ($k) { $on = pmatch($v); return match (true) {
                $on(None) => $m->minus($k),
                $on(Maybe($v)) => $m->minus($k)->plus($k, $v)};
            }
        );
    }

    /**
     * Creates lenses for object fields.
     * 
     * Generates lens objects for accessing multiple fields
     * of a data structure.
     *
     * Example:
     * ```php
     * $lenses = makeLenses('name', 'age');
     * $data = ['name' => 'Alice', 'age' => 30];
     * 
     * $lenses->name->get($data);     // "Alice"
     * $lenses->age->set(31, $data);  // ['name' => 'Alice', 'age' => 31]
     * ```
     *
     * @param string ...$fields Field names to create lenses for
     * @return GenLens Object containing lenses for each field
     */
    const makeLenses = "\\Phunkie\\Functions\\lens\\makeLenses";
    function makeLenses(...$fields)
    {
        return new GenLens(...$fields);
    }
}
