<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\Functions\assertion;

use Phunkie\Validation\Validation;
use function Phunkie\Functions\show\showArrayType;
use function Phunkie\Functions\show\showType;

/**
 * Type validation functions.
 * 
 * This module provides functions for validating types in collections,
 * returning Validation results to handle type mismatches safely.
 */

/**
 * Asserts that a value matches a collection's type.
 * 
 * Validates that a value has the same type as elements in a collection.
 * Returns Success for matching types or Mixed collections, and Failure
 * with an error message for mismatches.
 *
 * Example:
 * ```php
 * $list = ImmList(1, 2, 3);
 * 
 * assertSameTypeAsCollectionType(4, $list);      // Success(4)
 * assertSameTypeAsCollectionType("str", $list);  // Failure(Error)
 * 
 * // Mixed collections accept any type
 * $mixed = ImmList(1, "str");
 * assertSameTypeAsCollectionType(true, $mixed);  // Success(true)
 * 
 * // Custom error message
 * assertSameTypeAsCollectionType(
 *     "str", 
 *     $list, 
 *     Some("Invalid type")
 * ); // Failure(Error("Invalid type"))
 * ```
 *
 * @template A
 * @param A $a Value to validate
 * @param mixed $collection Collection to check against
 * @param Option<string> $message Optional error message
 * @return Validation<\Error,A> Success with value or Failure with error
 */
const assertSameTypeAsCollectionType = "\\Phunkie\\Functions\\assertion\\assertSameTypeAsCollectionType";
function assertSameTypeAsCollectionType($a, $collection, $message = None): Validation
{
    if ($message === None) {
        $message = "Failed to assert that " . showArrayType($collection) . " is the same as " . showType($a);
    }

    if (showArrayType($collection) === "Mixed" || showArrayType($collection) === showType($a)) {
        return Success($a);
    }

    return Failure(new \Error($message));
}
