<?php

namespace Md\Phunkie\Functions;

use Md\Phunkie\Cats\Show;

function get_value_to_show($value)
{
    switch (true) {
        case(is_showable($value)):
            return $value->show();
        case(is_object($value) && method_exists($value, '__toString')):
            return (string)$value;
        case(is_string($value)):
            return '"' . $value . '"';
        case(is_int($value)):
        case(is_double($value)):
        case(is_float($value)):
        case(is_long($value)):
            return $value;
        case(is_resource($value));
            return (string)$value;
        case(is_bool($value)):
            return $value ? 'true' : 'false';
        case(is_null($value)):
            return 'null';
        case(is_array($value)):
            return "[" . implode(",", array_map(function($e) { return get_value_to_show($e);}, $value)) . "]";
        case(is_callable($value)):
            return "<function>";
        case(is_object($value)):
            return get_class($value) . "@" . substr(ltrim(spl_object_hash($value), "0"), 0 , 7);
        default:
            return $value;
    }
}

function is_showable($value): bool {
    if (!is_object($value)) return false;
    return object_class_uses_trait($value, Show::class);
}

function object_class_uses_trait($object, $trait): bool {
    $usesShowTrait = function($class) use ($trait) {return in_array($trait, class_uses($class));};
    $classAndParents = array_merge([get_class($object)], class_parents($object));
    $countOfShowTraitUsage = array_sum(array_map($usesShowTrait , $classAndParents));
    return (bool)$countOfShowTraitUsage;
}
