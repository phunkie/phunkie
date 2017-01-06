<?php

namespace Md\Phunkie\Functions\assertion;

use function Md\Phunkie\Functions\show\get_collection_type;
use function Md\Phunkie\Functions\show\get_type_to_show;
use Md\Phunkie\Validation\Validation;

function assertSameTypeAsCollectionType($a, $collection, $message = None): Validation
{
    if ($message === None) {
        $message = "Failed to assert that " . get_collection_type($collection) . " is the same as " . get_type_to_show($a);
    }

    if (get_collection_type($collection) === "Mixed" || get_collection_type($collection) === get_type_to_show($a)) {
        return Success($a);
    }

    return Failure(new \Error($message));
}