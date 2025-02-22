<?php

namespace Phunkie\PatternMatching\Referenced;

/**
 * Generic pattern matching with value extraction.
 * 
 * This class enables pattern matching and value extraction for any class
 * by capturing constructor parameters into reference variables. The constructor
 * parameter names must match the class property names.
 *
 * Example:
 * ```php
 * class Person {
 *     private $name;
 *     private $age;
 * 
 *     public function __construct(string $name, int $age) {
 *         $this->name = $name;
 *         $this->age = $age;
 *     }
 * }
 * 
 * // Pattern matching with extraction
 * $name = $age = null;
 * $person = new Person("Alice", 30);
 * $match = new PMatch($person);
 * 
 * if ($match(new GenericReferenced(Person::class, $name, $age))) {
 *     echo "$name is $age"; // prints "Alice is 30"
 * }
 * ```
 *
 * The class supports up to 21 constructor parameters, accessed as _1 through _21.
 * 
 * @see \Phunkie\PatternMatching\PMatch The pattern matcher
 */
class GenericReferenced
{
    /** @var string Special value to mark unused constructor arguments */
    public const UNUSED_ARGUMENT = "GenericReferenced::UNUSED_ARGUMENT";

    /** @var mixed Reference for 1st constructor parameter */
    public $_1;
    /** @var mixed Reference for 2nd constructor parameter */
    public $_2;
    /** @var mixed Reference for 3rd constructor parameter */
    public $_3;
    /** @var mixed Reference for 4th constructor parameter */
    public $_4;
    /** @var mixed Reference for 5th constructor parameter */
    public $_5;
    /** @var mixed Reference for 6th constructor parameter */
    public $_6;
    /** @var mixed Reference for 7th constructor parameter */
    public $_7;
    /** @var mixed Reference for 8th constructor parameter */
    public $_8;
    /** @var mixed Reference for 9th constructor parameter */
    public $_9;
    /** @var mixed Reference for 10th constructor parameter */
    public $_10;
    /** @var mixed Reference for 11th constructor parameter */
    public $_11;
    /** @var mixed Reference for 12th constructor parameter */
    public $_12;
    /** @var mixed Reference for 13th constructor parameter */
    public $_13;
    /** @var mixed Reference for 14th constructor parameter */
    public $_14;
    /** @var mixed Reference for 15th constructor parameter */
    public $_15;
    /** @var mixed Reference for 16th constructor parameter */
    public $_16;
    /** @var mixed Reference for 17th constructor parameter */
    public $_17;
    /** @var mixed Reference for 18th constructor parameter */
    public $_18;
    /** @var mixed Reference for 19th constructor parameter */
    public $_19;
    /** @var mixed Reference for 20th constructor parameter */
    public $_20;
    /** @var mixed Reference for 21st constructor parameter */
    public $_21;
    /** @var string The class name to match against */
    public $class;

    /**
     * Creates a new generic reference pattern.
     * 
     * Takes a class name and up to 21 references that will receive
     * the constructor parameter values during matching.
     *
     * @param string $class The class name to match
     * @param mixed &$_1 Reference for 1st parameter
     * @param mixed &$_2 Reference for 2nd parameter
     * @param mixed &$_3 Reference for 3rd parameter
     * @param mixed &$_4 Reference for 4th parameter
     * @param mixed &$_5 Reference for 5th parameter
     * @param mixed &$_6 Reference for 6th parameter
     * @param mixed &$_7 Reference for 7th parameter
     * @param mixed &$_8 Reference for 8th parameter
     * @param mixed &$_9 Reference for 9th parameter
     * @param mixed &$_10 Reference for 10th parameter
     * @param mixed &$_11 Reference for 11th parameter
     * @param mixed &$_12 Reference for 12th parameter
     * @param mixed &$_13 Reference for 13th parameter
     * @param mixed &$_14 Reference for 14th parameter
     * @param mixed &$_15 Reference for 15th parameter
     * @param mixed &$_16 Reference for 16th parameter
     * @param mixed &$_17 Reference for 17th parameter
     * @param mixed &$_18 Reference for 18th parameter
     * @param mixed &$_19 Reference for 19th parameter
     * @param mixed &$_20 Reference for 20th parameter
     * @param mixed &$_21 Reference for 21st parameter
     */
    public function __construct(
        $class,
        &$_1 = self::UNUSED_ARGUMENT,
        &$_2 = self::UNUSED_ARGUMENT,
        &$_3 = self::UNUSED_ARGUMENT,
        &$_4 = self::UNUSED_ARGUMENT,
        &$_5 = self::UNUSED_ARGUMENT,
        &$_6 = self::UNUSED_ARGUMENT,
        &$_7 = self::UNUSED_ARGUMENT,
        &$_8 = self::UNUSED_ARGUMENT,
        &$_9 = self::UNUSED_ARGUMENT,
        &$_10 = self::UNUSED_ARGUMENT,
        &$_11 = self::UNUSED_ARGUMENT,
        &$_12 = self::UNUSED_ARGUMENT,
        &$_13 = self::UNUSED_ARGUMENT,
        &$_14 = self::UNUSED_ARGUMENT,
        &$_15 = self::UNUSED_ARGUMENT,
        &$_16 = self::UNUSED_ARGUMENT,
        &$_17 = self::UNUSED_ARGUMENT,
        &$_18 = self::UNUSED_ARGUMENT,
        &$_19 = self::UNUSED_ARGUMENT,
        &$_20 = self::UNUSED_ARGUMENT,
        &$_21 = self::UNUSED_ARGUMENT
    )
    {
        for ($i = 1; $i <= 21; $i++) {
            $this->{"_$i"} = &${"_$i"};
        }
        $this->class = $class;
    }
}
