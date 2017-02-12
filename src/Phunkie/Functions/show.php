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
    use function Phunkie\Functions\type\normaliseType;
    use Phunkie\Types\Function1;
    use Phunkie\Types\ImmList;
    use Phunkie\Types\ImmMap;
    use Phunkie\Types\ImmSet;
    use Phunkie\Types\Option;
    use Phunkie\Types\Pair;
    use Phunkie\Types\Tuple;
    use Phunkie\Types\Unit;
    use Phunkie\Validation\Failure;
    use Phunkie\Validation\Success;

    const show = "\\Phunkie\\Functions\\show\\show";
    function show(...$values)
    {
        array_map(function($x) { echo showValue($x); }, $values);
    }

    const showValue = "\\Phunkie\\Functions\\show\\showValue";
    function showValue($value): string { switch (true) {
        case is_showable($value):
            return $value->show();
        case is_object($value) && method_exists($value, '__toString') && !(new \ReflectionClass($value))->isAnonymous():
            return $value instanceof \Throwable ? get_class($value) . "(" . $value->getMessage() . ")" : (string)$value;
        case is_double($value):
        case is_float($value):
            return ($value == floor($value)) ? sprintf("%.01f", $value) : $value;
        case is_string($value): return $value == "\n" ? $value : '"' . $value . '"';
        case is_int($value):
        case is_long($value):
            return $value;
        case is_resource($value): return (string)$value;
        case is_bool($value): return $value ? 'true' : 'false';
        case is_null($value): return 'null';
        case is_array($value):
            if (is_assoc($value)) {
                $valueToShow = [];
                foreach ($value as $key => $elem) {
                    $valueToShow[] = (is_string($key) ? '"' . $key . '"' : $key) . " => " . showValue($elem);
                }
                return '[' . implode(", ", $valueToShow) . ']';
            }
            return "[" . implode(", ", array_map(function ($e) { return showValue($e); }, $value)) . "]";
        case is_object($value) && (new \ReflectionClass($value))->isAnonymous():
            return get_parent_class($value) === false ? "Anonymous@" . substr(ltrim(spl_object_hash($value), "0"), 0, 8) :
                "Anonymous < " . get_parent_class($value) . "@" . substr(ltrim(spl_object_hash($value), "0"), 0, 8);
        case is_callable($value): return "<function>";
        case is_object($value): return get_class($value) . "@" . substr(ltrim(spl_object_hash($value), "0"), 0, 8);
        default: return $value;}
    }

    const showType = "\\Phunkie\\Functions\\show\\showType";
    function showType($value) { switch (true) {
        case is_integer($value): return "Int";
        case is_float($value):
        case is_double($value): return "Double";
        case is_string($value): return "String";
        case is_resource($value): return "Resource";
        case is_bool($value): return "Boolean";
        case is_null($value): return "Null";
        case is_array($value): return is_assoc($value) ? "Array<" . showArrayType(array_keys($value)) . ", " . showArrayType($value) . ">" : "Array<" . showArrayType($value) . ">";
        case is_object($value) && $value instanceof Function1: return "Function1";
        case is_callable($value): return "Callable";        case is_object($value) && $value instanceof Unit: return "Unit";
        case is_object($value) && $value == None(): return "None";
        case is_object($value) && $value instanceof Option: return "Option<" . showType($value->get()) . ">";
        case is_object($value) && $value instanceof Pair: return "(" . showType($value->_1) . ", " . showType($value->_2) . ")";
        case is_object($value) && $value instanceof ImmList: return "List<" . showArrayType($value->toArray()) . ">";
        case is_object($value) && $value instanceof ImmSet: return "Set<" . showArrayType($value->toArray()) . ">";
        case is_object($value) && $value instanceof ImmMap: return "Map<" . showArrayType($value->keys()) . ", " . showArrayType($value->values()) . ">";
        case is_object($value) && $value instanceof Success: return "Validation<E, " . showType($value->getOrElse("")) . ">";
        case is_object($value) && $value instanceof Failure: return "Validation<" . showType(($value->fold(Function1::identity()))(_)) . ", A>";
        case is_object($value) && $value instanceof Tuple:
            $types = [];
            for ($i = 1; $i <= $value->getArity(); $i++) {
                $types[] = showType($value->{"_$i"});
            }
            return "(" . implode(", ", $types) . ")";
        case is_object($value) && (new \ReflectionClass($value))->isAnonymous():
            return get_parent_class($value) === false ? "AnonymousClass" : "AnonymousClass < " . get_parent_class($value);
        case is_object($value): return get_class($value); }
    }

    const showArrayType = "\\Phunkie\\Functions\\show\\showArrayType";
    function showArrayType($value): string {

        $combineTypes = function (string $a, string $b): string { switch (true) {
            case $a === $b: return $a;
            case strpos($a, "Option") === 0 && $b === "None": return $a;
            case $a === "None" && strpos($b, "Option") === 0 : return $b;
            case $a === "Nothing": return $b;
            case $b === "Nothing": return $a;
            default: return "Mixed";}
        };

        switch (count($value)) {
            case 0: return "Nothing";
            case 1: return showType(array_values($value)[0]);
            case 2: return $combineTypes(showType(array_values($value)[0]), showType(array_values($value)[1]));
            default: return $combineTypes(showType(array_values($value)[0]), showArrayType(array_slice($value, 1))); }
    }

    const showKind = "\\Phunkie\\Functions\\show\\showKind";
    function showKind($type): Option { switch (normaliseType($type)) {
        case "Int":
        case "String":
        case "Boolean":
        case "Callable":
        case "Null":
        case "Double":
        case "Float":
        case "Resource":
            return Some("proper: " . normaliseType($type) . " :: *");
        case "List":
        case "Map":
        case "Set":
        case "Option":
        case "ImmList":
        case "ImmMap":
        case "ImmSet":
            return Some("first-order: " . normaliseType($type) . " :: * -> *");
        case "Pair":
        case "Either":
            return Some("first-order: " . normaliseType($type) . " :: * -> * -> *");
        case "Functor":
        case "Applicative":
        case "Monad":
        case "Apply":
        case "Foldable":
        case "Kleisli":
        case "State":
        case "Show":
        case "Validation":
        case "Id":
        case "Lens":
        case "Monoid":
        case "Semigroup":
        case "Eq":
        case "Flatmap":
            return Some("higher-order: " . normaliseType($type) . " :: (* -> *) -> Constraint");
        case "StateT":
        case "OptionT":
            return Some("higher-order: " . normaliseType($type) . " :: (* -> *) -> * -> *");
        default:
            if (class_exists($type))  return Some("proper: " . $type . " :: *");
            else return None();
    }}

    const usesTrait = "\\Phunkie\\Functions\\show\\usesTrait";
    function usesTrait($object, $trait): bool
    {
        if (!is_object($object))  return false;

        $usesTrait = function ($c) use ($trait) { return in_array($trait, class_uses($c)); };

        $classAndParents = array_merge([get_class($object)], class_parents($object));

        $allParentsAndTraits = $classAndParents;
        array_map(function($x) use (&$allParentsAndTraits) {
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
        return array() !== array_diff_key($value, array_keys(array_keys($value)));
    }
}