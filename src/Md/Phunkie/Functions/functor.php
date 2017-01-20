<?php

namespace {
    use Md\Phunkie\Cats\Functor\FunctorComposite;

    function Functor($type)  { return new FunctorComposite($type); }
}

namespace Md\Phunkie\Functions\functor {

    use Md\Phunkie\Types\Kind;
    use function Md\Phunkie\Functions\currying\curry;

    const fmap = "\\Md\\Phunkie\\Functions\\functor\\fmap";
    function fmap(callable $f) {
        return curry([$f],func_get_args(),function(Kind $kind) use ($f) {
            return $kind->map($f);
        });
    }
}