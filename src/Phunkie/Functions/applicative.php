<?php

namespace Phunkie\Functions\applicative;

use Phunkie\Cats\Applicative;
use Phunkie\Cats\Apply;
use function Phunkie\Functions\currying\applyPartially;
use Phunkie\Types\Kind;

/**
 * F<A -> B> -> F<A> -> F<B>
 */
const ap = "\\Phunkie\\Functions\\applicative\\ap";
function ap(Kind $f)
{
    return applyPartially([$f],func_get_args(),function(Applicative $applicative) use ($f) {
        return $applicative->apply($f);
    });
}

const pure = "\\Phunkie\\Functions\\applicative\\pure";
function pure($context)
{
    return applyPartially([$context],func_get_args(),function($a) use ($context) {
        if (($fa = $context($a)) instanceof Applicative) {
            return $fa;
        }
        throw new \Error("$context is not an applicative context");
    });
}

const map2 = "\\Phunkie\\Functions\\applicative\\map2";
function map2(callable $f)
{
    return applyPartially([$f],func_get_args(), function(Apply $fa) use ($f) {
        return function(Apply $fb) use ($fa, $f) {
            return $fa->map2($fb, $f);
        };
    });
}