<?php

namespace Md\Phunkie\Functions\currying;

function curry($declaredArgs, $passedArgs, $f) {
    $countOfPassedArgs = count($passedArgs);
    $countOfDeclaredArgs = count($declaredArgs);
    $passedSameNumber = count($declaredArgs) == $countOfPassedArgs;

    if ($passedSameNumber) {
        return function ($x) use ($f) {
            return $x == _ ? $f : $f($x);
        };
    }

    throw new \BadFunctionCallException("Wrong number of arguments in curried function: " .
        "expected: $countOfDeclaredArgs or " . ($countOfDeclaredArgs + 1) .
        " found: " . $countOfPassedArgs);
}