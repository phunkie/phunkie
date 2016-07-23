<?php

namespace Md\Phunkie\Functions\semigroup;

use function Md\Phunkie\Functions\pattern_matching\matching;
use function Md\Phunkie\Functions\pattern_matching\on;
use Md\Phunkie\Types\Lazy;
use Md\Phunkie\Types\Unit;
use TypeError;

function combine($a, $b) {
    $combineForObjects = new Lazy(function() use($a, $b) {
        if (method_exists($a, 'combine')) return $a->combine($b);
        if (is_callable($a)) { return function () use ($a, $b) { return $a($b(...func_get_args())); }; }
        foreach (array_intersect(get_parent_classes($a), get_parent_classes($b)) as $parent)
            if (method_exists($parent, 'combine')) return $a->combine($b);
    });

    return matching(
        on($a instanceof Unit)->returns($b),
        on($b instanceof Unit)->returns($a),
        on(gettype($a) != gettype($b) && is_object($a))->throws(new Lazy(function()use($a,$b){ return new TypeError("combine is not defined for " . get_class($a)); })),
        on(gettype($a) != gettype($b))->throws(new TypeError("combine is not defined for type " . gettype($a))),
        on(gettype($a) == gettype($b))->returns(
            matching(gettype($a),
                on("int")->or("integer")->or("double")->or("float")->returns(new Lazy(function()use($a,$b){ return $a + $b;})),
                on("string")->returns(new Lazy(function()use($a,$b){ return $a . $b;})),
                on("bool")->or("boolean")->returns(new Lazy(function()use($a,$b){ return $a && $b;})),
                on("array")->returns(new Lazy(function()use($a,$b){ return array_merge($a, $b);})),
                on("object")->returns($combineForObjects)
            )
        ),
        on(_)->throws(new TypeError("combining members of different semigroups"))
    );
}

function zero($a) {
    $zeroForObjects = function($a) {
        if (method_exists($a, "zero")) return $a->zero();
        if (is_callable($a)) return function($x) { return $x; };
    };

    return matching(gettype($a),
        on("int")->or("integer")->returns(0),
        on("double")->or("float")->returns(0.0),
        on("string")->returns(""),
        on("bool")->or("boolean")->returns(true),
        on("array")->returns([]),
        on("object")->returns($zeroForObjects($a)),
        on(_)->throws(new TypeError("zero is not defined for type " . gettype($a)))
    );
}

function get_parent_classes($object)
{
    $parents = [];
    $parent = $object;
    while (false !== $parent) {
        $parent = get_parent_class($parent);
        $parents[] = $parent;
    }
    return $parents;
}