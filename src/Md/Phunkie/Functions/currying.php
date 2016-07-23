<?php

namespace Md\Phunkie\Functions\currying;

use function Md\Phunkie\Functions\pattern_matching\matching;
use function Md\Phunkie\Functions\pattern_matching\on;

function curry($declaredArgs, $passedArgs, $f) {
    $countOfPassedArgs = count($passedArgs);
    $countOfDeclaredArgs = count($declaredArgs);
    $passedSameNumber = count($declaredArgs) == $countOfPassedArgs;

    return matching(
        on($passedSameNumber)->returns(function($x) use ($f) {
            return matching(true,
                on($x == _)->returns($f),
                on(_)->returns($f($x))
            );
        }),
        on(_)->throws(new \BadFunctionCallException("Wrong number of arguments in curried function: " .
            "expected: $countOfDeclaredArgs or " . ($countOfDeclaredArgs + 1) .
            " found: " . $countOfPassedArgs))
    );
}