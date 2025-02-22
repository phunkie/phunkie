<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\Functions\currying;

/**
 * Functions for currying and partial application.
 * 
 * This module provides utilities for transforming functions into curried form
 * and handling partial application of arguments.
 */

/**
 * Converts a binary function into curried form.
 * 
 * Takes a function of two arguments and returns a function that takes
 * one argument and returns a function that takes the second argument.
 *
 * Example:
 * ```php
 * $add = fn($a, $b) => $a + $b;
 * $curriedAdd = curry($add);
 * 
 * $add5 = $curriedAdd(5);    // Returns fn($b) => 5 + $b
 * $result = $add5(3);        // Returns 8
 * 
 * // Or use directly
 * $result = curry($add)(5)(3); // Returns 8
 * ```
 *
 * @template A,B,C
 * @param callable(A,B):C $f Binary function to curry
 * @return callable(A):callable(B):C Curried function
 */
const curry = "\\Phunkie\\Functions\\currying\\curry";
function curry($f)
{
    return fn ($a) => fn ($b) => $f($a, $b);
}

/**
 * Converts a curried function back to binary form.
 * 
 * Takes a curried function and returns a function that takes
 * both arguments at once.
 *
 * Example:
 * ```php
 * $curriedAdd = fn($a) => fn($b) => $a + $b;
 * $add = uncurry($curriedAdd);
 * 
 * $result = $add(5, 3); // Returns 8
 * ```
 *
 * @template A,B,C
 * @param callable(A):callable(B):C $f Curried function
 * @return callable(A,B):C Binary function
 */
const uncurry = "\\Phunkie\\Functions\\currying\\uncurry";
function uncurry($f)
{
    return fn ($a, $b) => ($f($a))($b);
}

/**
 * Handles partial application of arguments.
 * 
 * Used internally to implement currying with optional partial application.
 * Supports the underscore (_) placeholder for deferred arguments.
 *
 * Example:
 * ```php
 * $f = fn($a, $b) => $a + $b;
 * $partial = applyPartially([$a], [$a, $b], $f);
 * 
 * // With placeholder
 * $result = $partial(_); // Returns fn($x) => $f($x)
 * 
 * // Without placeholder
 * $result = $partial($b); // Returns $f($a, $b)
 * ```
 *
 * @param array $declaredArgs Expected arguments
 * @param array $passedArgs Actual arguments passed
 * @param callable $f Function to apply
 * @return mixed Result or partially applied function
 * @throws \BadFunctionCallException If wrong number of arguments
 */
function applyPartially($declaredArgs, $passedArgs, $f)
{
    $countOfPassedArgs = count($passedArgs);
    $countOfDeclaredArgs = count($declaredArgs);

    if ($countOfDeclaredArgs == $countOfPassedArgs) {
        return fn ($x) => $x === _ ? $f : $f($x);
    }

    if ($countOfDeclaredArgs == $countOfPassedArgs - 1) {
        return $f($passedArgs[$countOfPassedArgs - 1]);
    }

    throw new \BadFunctionCallException("Wrong number of arguments in curried function: " .
        "expected: $countOfDeclaredArgs or " . ($countOfDeclaredArgs + 1) .
        " found: " . $countOfPassedArgs);
}
