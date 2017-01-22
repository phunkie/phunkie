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

use function Phunkie\Functions\show\showArrayType;
use function Phunkie\Functions\show\showType;
use Phunkie\Validation\Validation;

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