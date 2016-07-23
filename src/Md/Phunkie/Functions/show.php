<?php

namespace Md\Phunkie\Functions\show {

    use Md\Phunkie\Cats\Show;
    use function Md\Phunkie\Functions\pattern_matching\matching;
    use function Md\Phunkie\Functions\pattern_matching\on;
    use Md\Phunkie\Types\Lazy;

    function show($value)
    {
        echo get_value_to_show($value);
    }

    function get_value_to_show($value)
    {
        return matching(
            on(is_showable($value))->returns(new Lazy(function() use($value) { return $value->show(); })),
            on(is_object($value) && method_exists($value, '__toString'))->returns(new Lazy(function() use($value) { return (string)$value;})),
            on(is_string($value))->returns(new Lazy(function() use($value) { return '"' . $value . '"';})),
            on(is_int($value))->or(is_double($value))->or(is_float($value))->or(is_long($value))->returns($value),
            on(is_resource($value))->returns(new Lazy(function() use($value) { return (string)$value;})),
            on(is_bool($value))->returns(new Lazy(function() use($value) { return $value ? 'true' : 'false';})),
            on(is_null($value))->returns('null'),
            on(is_array($value))->returns(new Lazy(function() use($value) { return "[" . implode(",", array_map(function ($e) {
                    return get_value_to_show($e);
                }, $value)) . "]";})),
            on(is_callable($value))->returns("<function>"),
            on(is_object($value))->returns(new Lazy(function() use($value) { return get_class($value) . "@" . substr(ltrim(spl_object_hash($value), "0"), 0, 7);})),
            on(_)->returns($value)
        );
    }

    function is_showable($value): bool
    {
        if (!is_object($value)) {
            return false;
        }
        return object_class_uses_trait($value, Show::class);
    }

    function object_class_uses_trait($object, $trait): bool
    {
        if (!is_object($object)) {
            return false;
        }

        $usesTrait = function ($class) use ($trait) {
            return in_array($trait, class_uses($class));
        };

        $classAndParents = array_merge([get_class($object)], class_parents($object));
        $allParentsAndTraits = $classAndParents;
        array_map(function($x) use (&$allParentsAndTraits) { $allParentsAndTraits = array_merge($allParentsAndTraits, class_uses($x)); }, $classAndParents);
        $countOfShowTraitUsage = array_sum(array_map($usesTrait, $allParentsAndTraits));

        return (bool)$countOfShowTraitUsage;
    }
}