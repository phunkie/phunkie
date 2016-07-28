<?php

namespace Md\Phunkie\Functions\currying;

function curry($declaredArgs, $passedArgs, $f) {
    $countOfPassedArgs = count($passedArgs);
    $countOfDeclaredArgs = count($declaredArgs);

    if ($countOfDeclaredArgs == $countOfPassedArgs) {
        return function ($x) use ($f) {
            return $x == _ ? $f : $f($x);
        };
    }

    if ($countOfDeclaredArgs == $countOfPassedArgs - 1) {
        return $f($passedArgs[$countOfPassedArgs - 1]);
    }

    throw new \BadFunctionCallException("Wrong number of arguments in curried function: " .
        "expected: $countOfDeclaredArgs or " . ($countOfDeclaredArgs + 1) .
        " found: " . $countOfPassedArgs);
}