<?php

namespace Md\Phunkie\Ops\ImmList;

use Md\Phunkie\Ops\FunctorOps;
use Md\Phunkie\PatternMatching\Underscore;
use Md\Phunkie\PatternMatching\Wildcard;
use Md\Phunkie\Types\Kind;

trait ImmListFunctorOps
{
    use FunctorOps;
    public function map(callable $f): Kind
    {
        return ImmList(...array_map(function($element) use ($f){
            return $f($element);
        }, $this->values));
    }

    public function imap(callable $f,callable $g): Kind
    {
        return $this->map($f);
    }
}