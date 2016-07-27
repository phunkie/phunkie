<?php

namespace Md\Phunkie\Functions\show {

    use Md\Phunkie\Cats\Show;

    function show(...$values)
    {
        array_map(function($x) { echo get_value_to_show($x); }, $values);
    }

    function get_value_to_show($value) { switch (true) {
        case is_showable($value): return $value->show();
        case is_object($value) && method_exists($value, '__toString'): return (string)$value;
        case is_string($value): return $value == "\n" ? $value : '"' . $value . '"';
        case is_int($value):case is_double($value): case is_float($value): case is_long($value): return $value;
        case is_resource($value): return (string)$value;
        case is_bool($value): return $value ? 'true' : 'false';
        case is_null($value): return 'null';
        case is_array($value): return "[" . implode(",", array_map(function ($e) { return get_value_to_show($e); }, $value)) . "]";
        case is_callable($value): return "<function>";
        case is_object($value): return get_class($value) . "@" . substr(ltrim(spl_object_hash($value), "0"), 0, 7);
        default: return $value;}
    }

    function is_showable($value): bool
    {
        return !is_object($value) ? false : object_class_uses_trait($value, Show::class);
    }

    function object_class_uses_trait($object, $trait): bool
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
}