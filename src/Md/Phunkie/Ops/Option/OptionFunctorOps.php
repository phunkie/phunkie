<?php

namespace Md\Phunkie\Ops\Option;

use Md\Phunkie\Cats\Functor;
use function Md\Phunkie\Functions\pattern_matching\matching;
use function Md\Phunkie\Functions\pattern_matching\on;
use Md\Phunkie\Ops\FunctorOps;
use Md\Phunkie\Types\Kind;
use Md\Phunkie\Types\Lazy;
use Md\Phunkie\Types\None;

/**
 * @mixin \Md\Phunkie\Types\Some
 */
trait OptionFunctorOps
{
    use FunctorOps;
    public function map(callable $f): Kind
    {
        switch (true) {
            case $this->isEmpty(): return None();
            case ($f($this->get()) instanceof None) : return None();
            default: return Some($f($this->get()));
        }
    }

    public function imap(callable $f,callable $g): Kind
    {
        return $this->map($f);
    }
}