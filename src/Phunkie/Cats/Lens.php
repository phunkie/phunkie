<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\Cats;

/**
 * A functional abstraction for accessing and updating immutable data structures.
 * 
 * A Lens is a first-class reference to a subpart of a data type. It provides a way
 * to view, update, and transform deeply nested data structures in a composable way.
 * Lenses must satisfy certain laws to be well-behaved.
 *
 * Laws:
 * 1. Identity: lens.get(lens.set(a, s)) == a
 * 2. Retention: lens.set(lens.get(s), s) == s
 * 3. Double Set: lens.set(b, lens.set(a, s)) == lens.set(b, s)
 *
 * Example:
 * ```php
 * class Address { 
 *     public function __construct(
 *         private string $street,
 *         private string $city
 *     ) {}
 * }
 * 
 * class User {
 *     public function __construct(
 *         private string $name,
 *         private Address $address
 *     ) {}
 * }
 * 
 * // Create lenses for accessing nested data
 * $addressLens = new Lens(
 *     fn(User $u) => $u->address,
 *     fn(Address $a, User $u) => new User($u->name, $a)
 * );
 * 
 * $streetLens = new Lens(
 *     fn(Address $a) => $a->street,
 *     fn(string $s, Address $a) => new Address($s, $a->city)
 * );
 * 
 * // Compose lenses for deep access
 * $userStreetLens = $addressLens->andThen($streetLens);
 * // Or compose multiple lenses at once
 * $userStreetLens = $addressLens->combine($streetLens, $otherLens);
 * ```
 *
 * @template S The source type
 * @template A The focus type
 */
class Lens
{
    private $g;
    private $s;

    /**
     * Creates a new lens from getter and setter functions.
     *
     * @param callable(S):A $g Function to extract the focused value
     * @param callable(A,S):S $s Function to update the focused value
     */
    public function __construct(callable $g, callable $s)
    {
        $this->g = $g;
        $this->s = $s;
    }

    /**
     * Gets the focused value from the source structure.
     *
     * @param S $a The source structure
     * @return A The focused value
     */
    public function get($a)
    {
        return call_user_func($this->g, $a);
    }

    /**
     * Sets a new value for the focused part, returning a new source structure.
     *
     * @param A $b The new value
     * @param S $a The source structure
     * @return S A new source structure with the updated value
     */
    public function set($b, $a)
    {
        return call_user_func_array($this->s, [$b, $a]);
    }

    /**
     * Modifies the focused value using a function.
     *
     * @template B
     * @param callable(A):B $f The function to apply
     * @param S $a The source structure
     * @return S A new source structure with the modified value
     */
    public function mod(callable $f, $a)
    {
        return $this->set($f($this->get($a)), $a);
    }

    /**
     * Composes multiple lenses together.
     * With no arguments, returns this lens.
     * With one argument, equivalent to andThen.
     * With multiple arguments, composes all lenses left-to-right.
     *
     * @param Lens ...$other The lenses to compose with
     * @return Lens The composed lens
     */
    public function combine(Lens ...$other): Lens
    {
        if (func_num_args() == 0) {
            return $this;
        }
        if (func_num_args() == 1) {
            return $this->andThen($other[0]);
        }
        return $this->andThen($other[0])->combine(array_slice($other, 1));
    }

    /**
     * Composes this lens with another lens.
     * The resulting lens focuses on a part of this lens's focused value.
     *
     * @param Lens<A,B> $l The lens to compose with
     * @return Lens<S,B> A new lens focusing on the nested value
     */
    public function andThen(Lens $l): Lens
    {
        return new Lens(
            fn ($a) => $l->get($this->get($a)),
            fn ($c, $a) => $this->mod(fn ($b) => $l->set($c, $b), $a)
        );
    }

    /**
     * Composes this lens with another lens in reverse order.
     *
     * @param Lens $that The lens to compose with
     * @return Lens The composed lens
     */
    public function compose(Lens $that): Lens
    {
        return $that->andThen($this);
    }
}
