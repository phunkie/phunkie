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

    use function Phunkie\Functions\assertion\local\asTypeNames;
    use function Phunkie\Functions\assertion\local\promisedType;
    use function Phunkie\Functions\assertion\local\typeArgumentsSatisfy;
    use function Phunkie\Functions\show\showType;

    /**
     * Asserts that an argument carries the type arguments its signature promised.
     *
     * This is what a generic parameter compiles to. PHP enforces the constructor
     * from the native declaration beside it; this covers the part PHP has no way
     * to say, that it is a list *of integers*.
     *
     * It is global, and it throws rather than returning a Validation, because
     * compiled code calls it unqualified as a statement and nothing downstream
     * would look at a result.
     *
     * Example:
     * ```php
     * function doubleAll(ImmList $numbers): ImmList
     * {
     *     assertTypeArguments($numbers, ['Int'], 'doubleAll', 1, 'numbers');
     *
     *     return $numbers->map(fn($n) => $n * 2);
     * }
     * ```
     *
     * @param mixed $value Value to check
     * @param list<string> $expected Type arguments the signature promised
     * @param string $function Function the argument was passed to
     * @param int $position Position of the argument, counting from one
     * @param string $parameter Name of the parameter, without its dollar
     *
     * @throws TypeError when the value carries other type arguments
     */
    function assertTypeArguments(mixed $value, array $expected, string $function, int $position, string $parameter): void
    {
        $expected = asTypeNames($expected);

        if (typeArgumentsSatisfy($value, $expected)) {
            return;
        }

        // Worded as PHP words its own TypeError, because as far as the caller is
        // concerned nothing unusual has happened: they passed the wrong type and
        // were told so. That is also why there is no full stop.
        throw new TypeError(sprintf(
            '%s(): Argument #%d ($%s) must be of type %s, %s given',
            $function,
            $position,
            $parameter,
            promisedType($expected, showType($value)),
            showType($value)
        ));
    }

    /**
     * Asserts that a returned value carries the type arguments its signature
     * promised, and gives it back.
     *
     * This is what a generic return type compiles to. It returns the value so
     * that it can wrap the expression of a `return` in place, and every `return`
     * in a body is wrapped, since any of them can be the one that lies.
     *
     * Example:
     * ```php
     * return assertReturnTypeArguments($numbers->map($double), ['Int'], 'doubleAll');
     * ```
     *
     * @template A
     * @param A $value Value being returned
     * @param list<string> $expected Type arguments the signature promised
     * @param string $function Function returning the value
     *
     * @return A The value, unchanged
     *
     * @throws TypeError when the value carries other type arguments
     */
    function assertReturnTypeArguments(mixed $value, array $expected, string $function): mixed
    {
        $expected = asTypeNames($expected);

        if (typeArgumentsSatisfy($value, $expected)) {
            return $value;
        }

        // PHP says "returned" rather than "given" in this position.
        throw new TypeError(sprintf(
            '%s(): Return value must be of type %s, %s returned',
            $function,
            promisedType($expected, showType($value)),
            showType($value)
        ));
    }
}

namespace Phunkie\Functions\assertion {

    use Phunkie\Validation\Validation;
    use function Phunkie\Functions\show\showArrayType;
    use function Phunkie\Functions\show\showType;

    /**
     * Type validation functions.
     *
     * This module provides functions for validating types in collections,
     * returning Validation results to handle type mismatches safely.
     */

    /**
     * Asserts that a value matches a collection's type.
     *
     * Validates that a value has the same type as elements in a collection.
     * Returns Success for matching types or Mixed collections, and Failure
     * with an error message for mismatches.
     *
     * Example:
     * ```php
     * $list = ImmList(1, 2, 3);
     *
     * assertSameTypeAsCollectionType(4, $list);      // Success(4)
     * assertSameTypeAsCollectionType("str", $list);  // Failure(Error)
     *
     * // Mixed collections accept any type
     * $mixed = ImmList(1, "str");
     * assertSameTypeAsCollectionType(true, $mixed);  // Success(true)
     *
     * // Custom error message
     * assertSameTypeAsCollectionType(
     *     "str",
     *     $list,
     *     Some("Invalid type")
     * ); // Failure(Error("Invalid type"))
     * ```
     *
     * @template A
     * @param A $a Value to validate
     * @param mixed $collection Collection to check against
     * @param Option<string> $message Optional error message
     * @return Validation<\Error,A> Success with value or Failure with error
     */
    const assertSameTypeAsCollectionType = "\\Phunkie\\Functions\\assertion\\assertSameTypeAsCollectionType";
    function assertSameTypeAsCollectionType($a, $collection, $message = None): Validation
    {
        if ($message === None) {
            $message = "Failed to assert that " . showArrayType($collection) . " is the same as " . showType($a);
        }

        if (showArrayType($collection) === "Mixed" || showArrayType($collection) === showType($a)) {
            return Success($a);
        }

        return Failure(new \Error($message));
    }
}

namespace Phunkie\Functions\assertion\local {

    use Phunkie\Types\ImmList;
    use Phunkie\Types\ImmMap;
    use Phunkie\Types\ImmSet;
    use Phunkie\Types\Kind;

    /**
     * The bottom type. A container reporting it has committed to nothing, so it
     * satisfies whatever was asked of it.
     */
    const NOTHING = "Nothing";

    /**
     * The type names of the classes that are not called what they are.
     *
     * Read off `kind`, which is where a type's name is written down, so this
     * follows those classes rather than repeating them. Only the ones that
     * differ get an entry: a class already called by its type name needs none,
     * and drops out of here the day it is renamed.
     *
     * @return array<string, string>
     */
    function typeNames(): array
    {
        $names = [];

        foreach ([ImmList::class, ImmMap::class, ImmSet::class] as $class) {
            $written = substr((string) strrchr($class, '\\'), 1);

            if ($written !== $class::kind) {
                $names[$written] = $class::kind;
            }
        }

        return $names;
    }

    /**
     * Reads what a signature promised in the names values answer in.
     *
     * A signature names a class, because the class is what PHP enforces, and a
     * value reports a type: `ImmMap<String, Int>` and `Map<String, Int>` are
     * one type written two ways. At the top level this never showed, the guard
     * taking the constructor from the value and comparing only the arguments,
     * but one level down the written text is the whole of what gets compared,
     * and `ImmList<ImmMap<String, Int>>` promised something no value could ever
     * report.
     *
     * Every name in the argument is read, however deep, so a type nested inside
     * a type is covered by the same pass.
     *
     * @param list<string> $expected
     *
     * @return list<string>
     */
    function asTypeNames(array $expected): array
    {
        $names = typeNames();

        return array_map(
            static fn (string $argument): string => (string) preg_replace_callback(
                '/[A-Za-z_][A-Za-z0-9_]*/',
                static fn (array $match): string => $names[$match[0]] ?? $match[0],
                $argument
            ),
            $expected
        );
    }

    /**
     * Whether a value carries the type arguments a signature promised.
     *
     * Only the arguments are compared, never the whole type. The constructor
     * belongs to PHP, and keeping them apart is what lets a subtype through:
     * NonEmptyList reports itself as List<Int>, so a guard looking for the
     * rendered NonEmptyList<Int> would never match, while one looking for Int
     * matches exactly as it should.
     *
     * Mixed needs no rule of its own. It is the top type, so a heterogeneous
     * container is not a container of any one thing, and it fails the same
     * comparison every other wrong argument fails.
     *
     * @param list<string> $expected
     */
    function typeArgumentsSatisfy(mixed $value, array $expected): bool
    {
        if (!$value instanceof Kind) {
            return true;
        }

        $actual = $value->getTypeVariables();

        // An empty container named no arguments at all. None is this case
        // rather than the Nothing one, because Option's arity follows whether
        // it holds anything, and both mean the same thing to a guard.
        if ($actual === []) {
            return true;
        }

        if (count($actual) !== count($expected)) {
            return false;
        }

        foreach ($actual as $position => $argument) {
            if ($argument !== $expected[$position] && $argument !== NOTHING) {
                return false;
            }
        }

        return true;
    }

    /**
     * Renders what a signature promised, in the shape the value reports itself.
     *
     * The constructor is taken from the value because the guard was only ever
     * given the arguments, and reading it back off the value is what keeps the
     * message honest for a subtype: a NonEmptyList is told it must be a
     * List<Int>, which is what it calls itself.
     *
     * @param list<string> $expected
     */
    function promisedType(array $expected, string $actualType): string
    {
        $constructor = strstr($actualType, '<', true);

        return ($constructor === false ? $actualType : $constructor) . '<' . implode(', ', $expected) . '>';
    }
}
