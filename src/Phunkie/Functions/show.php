<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\Functions\show {

    use Phunkie\Cats\Show;
    use Phunkie\Types\Option;
    use function Phunkie\Functions\type\normaliseType;

    /**
     * Functions for converting values to strings.
     * 
     * This module provides functions for getting human-readable
     * string representations of values, particularly useful for
     * debugging and testing.
     */

    /**
     * Converts a value to a string representation.
     * 
     * Returns a readable string format of the value.
     * Uses the type's show implementation if available.
     *
     * Example:
     * ```php
     * show(42);                    // "42"
     * show("hello");              // "\"hello\""
     * show(ImmList(1,2,3));       // "List(1, 2, 3)"
     * show(Some("value"));        // "Some(\"value\")"
     * show(Pair("key", 123));     // "Pair(\"key\", 123)"
     * ```
     *
     * @template A
     * @param A $value Value to convert
     * @return string String representation
     */
    const show = "\\Phunkie\\Functions\\show\\show";
    function show(...$values)
    {
        array_map(function ($x) {
            echo showValue($x);
        }, $values);
    }

    /**
     * Gets a function that shows a value.
     * 
     * Returns a function that converts values to strings.
     * Useful for composing with other functions.
     *
     * Example:
     * ```php
     * $showList = showValue();
     * $showList(ImmList(1,2,3));  // "List(1, 2, 3)"
     * 
     * $numbers = ImmList(1,2,3);
     * $numbers->map(showValue()); // List("1", "2", "3")
     * ```
     *
     * @template A
     * @return callable(A):string Function that converts to string
     */
    const showValue = "\\Phunkie\\Functions\\show\\showValue";
    function showValue($value): string { return match(true) {
        is_showable($value) => $value->show(),
        is_object($value) && method_exists($value, '__toString') && !(new \ReflectionClass($value))->isAnonymous() =>
            $value instanceof \Throwable ? get_class($value) . "(" . $value->getMessage() . ")" : (string)$value,
        is_double($value), is_float($value) => ($value == floor($value)) ? sprintf("%.01f", $value) : $value,
        is_string($value) => $value == "\n" ? $value : '"' . $value . '"',
        is_int($value), is_long($value) => $value,
        is_resource($value) => (string)$value,
        is_bool($value) => $value ? 'true' : 'false',
        is_null($value) => 'null',
        is_array($value) => showArrayValue($value),
        is_object($value) && (new \ReflectionClass($value))->isAnonymous() =>
            get_parent_class($value) === false ? "Anonymous@" . substr(ltrim(spl_object_hash($value), "0"), 0, 8) :
                "Anonymous < " . get_parent_class($value) . "@" . substr(ltrim(spl_object_hash($value), "0"), 0, 8),
        is_callable($value) => "<function>",
        is_object($value) => get_class($value) . "@" . substr(ltrim(spl_object_hash($value), "0"), 0, 8),
        default => $value};
    }

    /**
     * Shows array values.
     * 
     * Returns a string representation of array contents.
     * Handles both associative and sequential arrays.
     *
     * Example:
     * ```php
     * showArrayValue([1, 2, 3]);              // "[1, 2, 3]"
     * showArrayValue(["a" => 1, "b" => 2]);   // '["a" => 1, "b" => 2]'
     * showArrayValue([]);                      // "[]"
     * ```
     *
     * @param array<mixed> $value Array to convert
     * @return string String representation
     */
    function showArrayValue(array $value): string
    {
        return is_assoc($value) ?
            '[' . implode(", ", array_map(fn ($key, $value) => (is_string($key) ? '"' . $key . '"' : $key) . " => " . showValue($value), array_keys($value), $value)) . ']':
            "[" . implode(", ", array_map(showValue, $value)) . "]";
    }

    /**
     * Shows a value with type information.
     * 
     * Like show(), but includes the type name.
     * Useful for debugging type issues.
     *
     * Example:
     * ```php
     * showType(42);              // "Int(42)"
     * showType("hello");         // "String(\"hello\")"
     * showType(Some(1));         // "Option(Some(1))"
     * showType(ImmList(1,2));    // "List(List(1, 2))"
     * ```
     *
     * @template A
     * @param A $value Value to show with type
     * @return string Type and value representation
     */
    const showType = "\\Phunkie\\Functions\\show\\showType";
    function showType($value): string {return match (true) {
        is_showable($value) => $value->showType(),
        is_integer($value) => "Int",
        is_float($value), is_double($value) => "Double",
        is_string($value) => "String",
        is_resource($value) => "Resource",
        is_bool($value) => "Boolean",
        is_null($value) => "Null",
        is_array($value) => is_assoc($value) ? "Array<" . showArrayType(array_keys($value)) . ", " . showArrayType($value) . ">" : "Array<" . showArrayType($value) . ">",
        is_callable($value) => "Callable",
        is_object($value) && (new \ReflectionClass($value))->isAnonymous() =>
            get_parent_class($value) === false ? "AnonymousClass" : "AnonymousClass < " . get_parent_class($value),
        is_object($value) => get_class($value) };
    }

    /**
     * Shows array type information.
     * 
     * Returns the type of elements in an array.
     * For mixed types, returns "Mixed".
     * For empty arrays, returns "Nothing".
     *
     * Example:
     * ```php
     * showArrayType([1, 2, 3]);           // "Int"
     * showArrayType([1, "hello"]);        // "Mixed"
     * showArrayType([Some(1), None()]);   // "Option<Int>"
     * showArrayType([]);                  // "Nothing"
     * ```
     *
     * @param array<mixed> $value Array to check type
     * @return string Type description
     */
    const showArrayType = "\\Phunkie\\Functions\\show\\showArrayType";
    function showArrayType($value): string
    {
        $combineTypes = fn (string $a, string $b): string => match (true) {
            $a === $b => $a,
            strpos($a, "Option") === 0 && $b === "None" => $a,
            $a === "None" && strpos($b, "Option") === 0  => $b,
            $a === "Nothing" => $b,
            $b === "Nothing" => $a,
            default => "Mixed"};

        // Commenting out this recursive version for now.
        // Using this as is would result in stack overflow when printing bigger arrays and lists.
        // Need some time to optimise the tail call and/or add a trampoline.
        // So for now, using the iterative approach below.
        //
        // $showArrayType = function($value) use ($combineTypes, &$showArrayType) {
        //     return match (count($value)) {
        //         0 => "Nothing",
        //         1 => showType(array_values($value)[0]),
        //         2 => $combineTypes(showType(array_values($value)[0]), showType(array_values($value)[1])),
        //         default => $combineTypes(showType(array_values($value)[0]), $showArrayType(array_slice($value, 1))), };
        // };
        // return $showArrayType($value);

        $showArrayTypeIterative = function ($value) use ($combineTypes, &$showArrayTypeIterative) {
            $arrayValues = array_values($value);
            $type = 'Nothing';
            $showType = function($arrayValues, $combineTypes, $type) {
                for ($i = 0; $i < count($arrayValues); isset($arrayValues[$i+2]) ? $i = $i + 2 : $i++) {
                    $type = $combineTypes($type, showType($arrayValues[$i]));
                    if (isset($arrayValues[$i + 1])) {
                        $type = $combineTypes($type, showType($arrayValues[$i + 1]));
                    }
                    if ($type == 'Mixed') {
                        return $type;
                    }
                }
                return $type;
            };
            return match (count($arrayValues)) {
                0 => $type,
                1 => showType($arrayValues[0]),
                default => $showType($arrayValues, $combineTypes, $type)
            };
        };
        return $showArrayTypeIterative($value);
    }

    const showKind = "\\Phunkie\\Functions\\show\\showKind";
    function showKind($typeSignature): Option
    {
        // Internal helper to reduce kind based on consumed arguments
        $reduceKind = function (string $kind, int $consumed) use (&$reduceKind): string {
            if ($consumed <= 0) return $kind;

            // Remove the first argument type from the left
            $depth = 0;
            $splitPos = -1;
            for ($i = 0; $i < strlen($kind); $i++) {
                $char = $kind[$i];
                if ($char == '(') $depth++;
                if ($char == ')') $depth--;
                if ($char == '-' && $depth == 0 && $i + 1 < strlen($kind) && $kind[$i + 1] == '>') {
                    $splitPos = $i;
                    break;
                }
            }

            if ($splitPos !== -1) {
                $remainder = trim(substr($kind, $splitPos + 2));
                return $reduceKind($remainder, $consumed - 1);
            }

            return $kind;
        };

        // Internal helper to parse generic arguments
        $parseGenerics = function (string $str): array {
            $args = [];
            $depth = 0;
            $current = '';
            for ($i = 0; $i < strlen($str); $i++) {
                $char = $str[$i];
                if ($char == '<') $depth++;
                if ($char == '>') $depth--;
                if ($char == ',' && $depth == 0) {
                    $args[] = trim($current);
                    $current = '';
                } else {
                    $current .= $char;
                }
            }
            if ($current !== '') $args[] = trim($current);
            return $args;
        };

        // Known kinds table: [Kind Signature, Arity]
        $kinds = [
            'Option' => ['* -> *', 1],
            'List' => ['* -> *', 1],
            'ImmList' => ['* -> *', 1],
            'Set' => ['* -> *', 1],
            'ImmSet' => ['* -> *', 1],
            'Map' => ['* -> * -> *', 2],
            'ImmMap' => ['* -> * -> *', 2],
            'Validation' => ['* -> * -> *', 2],
            'Either' => ['* -> * -> *', 2],
            'IO' => ['* -> *', 1],
            'State' => ['* -> * -> *', 2],
            'Reader' => ['* -> * -> *', 2],
            'Writer' => ['* -> * -> *', 2],
            'Id' => ['* -> *', 1],
            'Function1' => ['* -> * -> *', 2],
            'Functor' => ['(* -> *) -> *', 1],
            'Monad' => ['(* -> *) -> *', 1],
            'Applicative' => ['(* -> *) -> *', 1],
            'Show' => ['* -> *', 1],
            'Eq' => ['* -> *', 1],
            'Semigroup' => ['* -> *', 1],
            'Monoid' => ['* -> *', 1],
            'Int' => ['*', 0],
            'String' => ['*', 0],
            'Boolean' => ['*', 0],
            'Bool' => ['*', 0],
            'Float' => ['*', 0],
            'Double' => ['*', 0],
            'Mixed' => ['*', 0],
            'Void' => ['*', 0], 
            'Null' => ['*', 0],
            'Callable' => ['*', 0], 
            'Resource' => ['*', 0]
        ];

        $typeSignature = trim($typeSignature);
        $normalized = normaliseType($typeSignature);

        // 1. Direct match in table
        if (isset($kinds[$normalized])) {
             return Some($kinds[$normalized][0]);
        }
        
        // 2. Direct match with alias (e.g. Int vs Integer if normaliseType handled it)
        // normaliseType already converts Int -> Int, etc.
        
        // 3. Parse Generics: Type<A, B>
        if (preg_match('/^([a-zA-Z0-9_]+)\s*<(.*)>$/', $typeSignature, $matches)) {
            $base = $matches[1];
            $argsStr = $matches[2];
            
            // Normalize base
            $base = normaliseType($base);
            
            if (!isset($kinds[$base])) {
                 return None();
            }
            
            [$baseKind, $arity] = $kinds[$base];
            
            $args = $parseGenerics($argsStr);
            
            if (count($args) > $arity) {
                 // In showKind we probably just fail or return None if invalid
                 return None();
            }
            
            $consumed = 0;
            foreach ($args as $arg) {
                $arg = trim($arg);
                // Hole _
                if ($arg === '_') {
                    continue;
                }
                // Type Variable (Single Uppercase Letter) - does not consume
                if (preg_match('/^[A-Z]$/', $arg)) {
                    continue;
                }
                
                // Otherwise, it consumes
                $consumed++;
            }
            
            return Some($reduceKind($baseKind, $consumed));
        }

        // 4. Default fallbacks from original logic
        return match ($normalized) {
            "Int", "String", "Boolean", "Callable", "Null", "Double", "Float", "Resource"
                => Some("*"),
            "List", "Map", "Set", "Option", "ImmList", "ImmMap", "ImmSet"
                => Some("* -> *"),
            "Pair", "Either"
                => Some("* -> * -> *"),
            "Functor", "Applicative", "Monad", "Apply", "Foldable", "Kleisli", "State", "Show", "Validation", "Id", "Lens", "Monoid", "Semigroup", "Eq", "Flatmap"
                => Some("(* -> *) -> Constraint"),
            "StateT", "OptionT"
                => Some("(* -> *) -> * -> *"),
            default => class_exists($typeSignature) ? Some("*") : None()
        };
    }

    /**
     * Checks if an object uses a trait.
     * 
     * Recursively checks if a trait is used by an object's class
     * or any of its parent classes. Also checks traits used by traits.
     *
     * Example:
     * ```php
     * class MyClass {
     *     use Show;
     * }
     * 
     * usesTrait(new MyClass(), Show::class);     // true
     * usesTrait(new MyClass(), SomeTrait::class); // false
     * usesTrait("not an object", Show::class);    // false
     * ```
     *
     * @param mixed $object Object to check
     * @param class-string $trait Trait class name to look for
     * @return bool True if object uses trait
     */
    const usesTrait = "\\Phunkie\\Functions\\show\\usesTrait";
    function usesTrait($object, $trait): bool
    {
        if (!is_object($object)) {
            return false;
        }

        $usesTrait = fn ($c) => in_array($trait, class_uses($c));

        $classAndParents = array_merge([get_class($object)], class_parents($object));

        $allParentsAndTraits = $classAndParents;
        array_map(function ($x) use (&$allParentsAndTraits) {
            $allParentsAndTraits = array_merge($allParentsAndTraits, class_uses($x));
        }, $classAndParents);

        $countOfShowTraitUsage = array_sum(array_map($usesTrait, $allParentsAndTraits));

        return (bool)$countOfShowTraitUsage;
    }

    /**
     * Checks if a value implements Show.
     * 
     * Returns true if the value has a show() method.
     *
     * Example:
     * ```php
     * is_showable(Some(42));     // true
     * is_showable(ImmList(1,2)); // true
     * is_showable(42);           // false
     * ```
     *
     * @param mixed $value Value to check
     * @return bool True if showable
     */
    const is_showable = "\\Phunkie\\Functions\\show\\is_showable";
    function is_showable($value): bool
    {
        return !is_object($value) ? false : usesTrait($value, Show::class);
    }

    /**
     * Checks if an array is associative.
     * 
     * Returns true if array has string keys.
     *
     * Example:
     * ```php
     * is_assoc([1, 2, 3]);           // false
     * is_assoc(["a" => 1, "b" => 2]); // true
     * ```
     *
     * @param array<mixed> $arr Array to check
     * @return bool True if associative
     */
    const is_assoc = "\\Phunkie\\Functions\\show\\is_assoc";
    function is_assoc(array $value): bool
    {
        return [] !== array_diff_key($value, array_keys(array_keys($value)));
    }
}
