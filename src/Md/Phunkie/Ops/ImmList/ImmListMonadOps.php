<?php

namespace Md\Phunkie\Ops\ImmList;

use function Md\Phunkie\Functions\pattern_matching\matching;
use function Md\Phunkie\Functions\pattern_matching\on;
use Md\Phunkie\Types\ImmList;
use Md\Phunkie\Types\Kind;
use Md\Phunkie\Types\Lazy;
use Md\Phunkie\Types\None;
use Md\Phunkie\Types\Option;

trait ImmListMonadOps
{
    public function flatMap(callable $f): Kind
    {
        $b = [];
        foreach ($this->toArray() as $a) {
            $tmp = $f($a);
            matching(
                on($f instanceof None)->or($tmp instanceof None)->returns(Unit()),
                on($tmp instanceof ImmList)->returns(
                    new Lazy(function() use (&$b, $tmp){
                        foreach ($tmp->toArray() as $value) $b[] = $value;
                    })
                ),
                on($tmp instanceof Option)->returns(
                    new Lazy(function() use (&$b, $tmp) {
                        $b[] = $tmp->get();
                    })
                ),
                on(_)->throws(new \BadMethodCallException("Type mismatch"))
            );
        }

        return ImmList(...$b);
    }

    public function flatten(): Kind
    {
        return $this->flatMap(function($x) { return $x; });
    }
}