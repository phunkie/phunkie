<?php

namespace {
    use Md\Phunkie\Cats\Functor\FunctorComposite;

    function Functor($type)  { return new FunctorComposite($type); }
}

namespace Md\Phunkie\Functions\functor {

    use Md\Phunkie\Cats\Functor;
    use function Md\Phunkie\Functions\currying\applyPartially;

    const fmap = "\\Md\\Phunkie\\Functions\\functor\\fmap";
    function fmap(callable $f) {
        return applyPartially([$f],func_get_args(),function(Functor $functor) use ($f) {
            return $functor->map($f);
        });
    }
}