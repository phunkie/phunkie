<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\Functions\type;

use Phunkie\Types\ImmInteger;
use Phunkie\Types\ImmString;

const promote = "\\Phunkie\\Functions\\type\\promote";
/**
 * Promotes primitive values to their immutable object equivalents.
 * 
 * Converts basic PHP types to their corresponding immutable types.
 * Leaves other values unchanged.
 *
 * Example:
 * ```php
 * promote(42);       // ImmInteger(42)
 * promote("hello");  // ImmString("hello")
 * promote(Some(1));  // Some(1) (unchanged)
 * ```
 *
 * @param mixed $value Value to promote
 * @return mixed Promoted value or original if no promotion needed
 */
function promote($value) { return match (gettype($value)) {
    "int", "integer" => new ImmInteger($value),
    "string" => new ImmString($value),
    default => $value };
}

const normaliseType = "\\Phunkie\\Functions\\type\\normaliseType";
/**
 * Normalizes a type name.
 * 
 * Converts various type representations to a standard format.
 * Handles primitive types, aliases, and class names.
 *
 * Example:
 * ```php
 * normaliseType("integer");     // "Int"
 * normaliseType("boolean");     // "Boolean"
 * normaliseType("double");      // "Float" (gettype spells floats "double")
 * normaliseType("array");       // "Array"
 * normaliseType("callable");    // "Callable"
 * normaliseType(Option::class); // "Option" (unchanged)
 * ```
 *
 * The lookup is case insensitive, so an already normalised name normalises to
 * itself and the legacy spelling "Double" resolves to "Float".
 *
 * @param string $type Type name to normalize
 * @return string Normalized type name
 */
function normaliseType($type)
{
    $scalars = [
        "int" => "Int",
        "integer" => "Int",
        "string" => "String",
        "bool" => "Boolean",
        "boolean" => "Boolean",
        "callable" => "Callable",
        "null" => "Null",
        "double" => "Float",
        "float" => "Float",
        "resource" => "Resource",
        "mixed" => "Mixed",
        "void" => "Void",
        "array" => "Array",
        "object" => "Object",
    ];

    if (!is_string($type)) {
        return $type;
    }

    return $scalars[strtolower($type)] ?? $type;
}
