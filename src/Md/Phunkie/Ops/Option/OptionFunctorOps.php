<?php

namespace Md\Phunkie\Ops\Option;

use Md\Phunkie\Cats\Functor;
use function Md\Phunkie\Functions\pattern_matching\matching;
use function Md\Phunkie\Functions\pattern_matching\on;
use Md\Phunkie\Ops\FunctorOps;
use Md\Phunkie\Types\Kind;

/**
 * @mixin \Md\Phunkie\Types\Some
 */
trait OptionFunctorOps
{
    use FunctorOps;
    public function map(callable $f): Kind
    {
        return matching(
            on($this->isEmpty())->returns(None()),
            on(Lazy(function()use($f){ return $f($this->get());}))->returns(None()),
            on(_)->returns(Lazy(function() use ($f) { return Some($f($this->get()));}))
        );
    }

    public function imap(callable $f,callable $g): Kind
    {
        return $this->map($f);
    }
}