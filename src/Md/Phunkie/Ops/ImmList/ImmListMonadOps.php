<?php

namespace Md\Phunkie\Ops\ImmList;

use Md\Phunkie\Types\ImmList;
use Md\Phunkie\Types\Kind;
use Md\Phunkie\Types\None;
use Md\Phunkie\Types\Option;

trait ImmListMonadOps
{
    public function flatMap(callable $f): Kind
    {
        $b = [];
        foreach ($this->toArray() as $a) {
            $tmp = $f($a);
            switch (true) {
                case $f instanceof None:
                case $tmp instanceof None:
                    break;
                case $tmp instanceof ImmList:
                    foreach ($tmp->toArray() as $value) $b[] = $value;
                    break;
                case $tmp instanceof Option:
                    $b[] = $tmp->get();
                    break;
                default:
                    throw new \BadMethodCallException("Type mismatch");
            }
        }

        return ImmList(...$b);
    }

    public function flatten(): Kind
    {
        return $this->flatMap(function($x) { return $x; });
    }
}