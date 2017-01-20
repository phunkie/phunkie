<?php

namespace Md\Phunkie\Functions\assertion;

use function Md\Phunkie\Functions\show\showArrayType;
use function Md\Phunkie\Functions\show\showType;
use Md\Phunkie\Validation\Validation;

const assertSameTypeAsCollectionType = "\\Md\\Phunkie\\Functions\\assertion\\assertSameTypeAsCollectionType";

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