<?php

namespace Md\Phunkie\Functions\currying;

function curry($declaredArgs, $passedArgs, $f) {
    $countOfPassedArgs = count($passedArgs);
    $countOfDeclaredArgs = count($declaredArgs);
    $lastArgument = $passedArgs[$countOfPassedArgs - 1];
    $passedSameNumber = count($declaredArgs) == $countOfPassedArgs;
    $moreArgsDeclaredThanPassed = $countOfPassedArgs - $countOfDeclaredArgs == 1;

    if ($passedSameNumber) {
        return function($x) use ($f) {
            if ($x == _) return $f;
            else return $f($x);
        };

    } else if($moreArgsDeclaredThanPassed) {
        return $f($lastArgument);
    }

    throw new \BadFunctionCallException("Wrong number of arguments in curried function: " .
        "expected: $countOfDeclaredArgs or " . ($countOfDeclaredArgs + 1) .
        " found: " . $countOfPassedArgs);
}