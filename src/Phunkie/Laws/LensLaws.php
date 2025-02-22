<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\Laws;

use Phunkie\Cats\Lens;

/**
 * Laws that every Lens must satisfy.
 * 
 * A lens is a bidirectional data accessor that combines:
 * - A getter that extracts a value
 * - A setter that updates a value
 *
 * These laws ensure that getting and setting behave intuitively:
 * 
 * 1. Identity:   get(set(b, a)) = b
 *    Getting what you just set returns the value you set
 * 
 * 2. Retention:  set(c, set(b, a)) = set(c, a)
 *    Setting twice is the same as setting once with the last value
 * 
 * 3. DoubleSet:  set(get(a), a) = a
 *    Setting a value you just got doesn't change anything
 *
 * Example:
 * ```php
 * class Person {
 *     public function __construct(
 *         private string $name,
 *         private int $age
 *     ) {}
 * 
 *     public static function ageLens(): Lens {
 *         return new Lens(
 *             fn(Person $p) => $p->age,
 *             fn(int $age, Person $p) => new Person($p->name, $age)
 *         );
 *     }
 * }
 * 
 * class PersonLens implements Lens {
 *     use LensLaws;
 * 
 *     public function test(): void {
 *         $person = new Person("Alice", 30);
 *         $lens = Person::ageLens();
 * 
 *         assert($this->identityLaw($lens, $person, 25));
 *         assert($this->retentionLaw($lens, $person, 25, 35));
 *         assert($this->doubleSetLaw($lens, $person));
 *     }
 * }
 * ```
 */
trait LensLaws
{
    /**
     * Identity law: get(set(b, a)) = b
     * 
     * If you set a value through a lens, getting it back should
     * return that same value.
     *
     * @param Lens $l The lens to test
     * @param mixed $a The original structure
     * @param mixed $b The value to set
     * @return bool True if the law holds
     */
    public function identityLaw(Lens $l, $a, $b): bool
    {
        return $l->get($l->set($b, $a)) == $b;
    }

    /**
     * Retention law: set(c, set(b, a)) = set(c, a)
     * 
     * Setting a value twice is the same as setting it once with
     * the last value. The intermediate value is discarded.
     *
     * @param Lens $l The lens to test
     * @param mixed $a The original structure
     * @param mixed $b The first value to set
     * @param mixed $c The second value to set
     * @return bool True if the law holds
     */
    public function retentionLaw(Lens $l, $a, $b, $c): bool
    {
        return $l->set($c, $l->set($b, $a)) == $l->set($c, $a);
    }

    /**
     * Double Set law: set(get(a), a) = a
     * 
     * Setting a value to what you just got from it shouldn't
     * change anything about the structure.
     *
     * @param Lens $l The lens to test
     * @param mixed $a The structure to test with
     * @return bool True if the law holds
     */
    public function doubleSetLaw(Lens $l, $a): bool
    {
        return $l->set($l->get($a), $a) == $a;
    }
}
