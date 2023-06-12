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

    const show = "\\Phunkie\\Functions\\show\\show";
    function show(...$values)
    {
        array_map(function ($x) {
            echo showValue($x);
        }, $values);
    }

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

    function showArrayValue(array $value): string
    {
        return is_assoc($value) ?
            '[' . implode(", ", array_map(fn ($key, $value) => (is_string($key) ? '"' . $key . '"' : $key) . " => " . showValue($value), array_keys($value), $value)) . ']':
            "[" . implode(", ", array_map(showValue, $value)) . "]";
    }

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
    function showKind($type): Option { return match (normaliseType($type)) {
        "Int", "String", "Boolean", "Callable", "Null", "Double", "Float", "Resource"
            => Some("proper: " . normaliseType($type) . " :: *"),
        "List", "Map", "Set", "Option", "ImmList", "ImmMap", "ImmSet"
            => Some("first-order: " . normaliseType($type) . " :: * -> *"),
        "Pair", "Either"
            => Some("first-order: " . normaliseType($type) . " :: * -> * -> *"),
        "Functor", "Applicative", "Monad", "Apply", "Foldable", "Kleisli", "State", "Show", "Validation", "Id", "Lens", "Monoid", "Semigroup", "Eq", "Flatmap"
            => Some("higher-order: " . normaliseType($type) . " :: (* -> *) -> Constraint"),
        "StateT", "OptionT"
            => Some("higher-order: " . normaliseType($type) . " :: (* -> *) -> * -> *"),
        default => class_exists($type) ?
            Some("proper: " . $type . " :: *") :
            None() };
    }

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

    const is_showable = "\\Phunkie\\Functions\\show\\is_showable";
    function is_showable($value): bool
    {
        return !is_object($value) ? false : usesTrait($value, Show::class);
    }

    const is_assoc = "\\Phunkie\\Functions\\show\\is_assoc";
    function is_assoc(array $value): bool
    {
        return [] !== array_diff_key($value, array_keys(array_keys($value)));
    }
}
