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

const promote = "Md\\Phunkie\\Functions\\type\\promote";
function promote($value)
{
    switch (gettype($value)) {
        case "int":
        case "integer":
            return new ImmInteger($value);
        case "string": return new ImmString($value);
        default: return $value;
    }
}

const normaliseType = "Md\\Phunkie\\Functions\\type\\normaliseType";
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
        "double" => "Double",
        "float" => "Float",
        "resource" => "Resource",
        "mixed" => "Mixed",
        "void" => "Void",
        "array" => "Array",
        "object" => "Object",
    ];

    return is_string($type) && isset($scalars[$type]) ? $scalars[$type] : $type;
}