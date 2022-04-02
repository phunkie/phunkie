<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace {
    use Phunkie\Cats\Functor\FunctorComposite;

    function Functor($type)
    {
        return new FunctorComposite($type);
    }
}

namespace Phunkie\Functions\functor {

    use Phunkie\Cats\Functor;
    use function Phunkie\Functions\currying\applyPartially;

    const fmap = "\\Phunkie\\Functions\\functor\\fmap";
    function fmap(callable $f)
    {
        return applyPartially([$f], func_get_args(), function (Functor $functor) use ($f) {
            return $functor->map($f);
        });
    }

    const allAs = "\\Phunkie\\Functions\\functor\\allAs";
    function allAs($b)
    {
        return applyPartially([$b], func_get_args(), function (Functor $functor) use ($b) {
            return $functor->as($b);
        });
    }

    const asVoid = "\\Phunkie\\Functions\\functor\\asVoid";
    function asVoid(Functor $functor)
    {
        return $functor->void();
    }

    const zipWith = "\\Phunkie\\Functions\\functor\\zipWith";
    function zipWith($f)
    {
        return applyPartially([$f], func_get_args(), function (Functor $functor) use ($f) {
            return $functor->zipWith($f);
        });
    }
}
