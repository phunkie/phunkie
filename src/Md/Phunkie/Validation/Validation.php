<?php

namespace Md\Phunkie\Validation;

use function Md\Phunkie\Functions\pattern_matching\matching;
use function Md\Phunkie\Functions\pattern_matching\on;
use TypeError;

abstract class Validation
{
    public function isRight(): bool
    {
        return matching($this,
            on(Failure(_))->returns(false),
            on(Success(_))->returns(true),
            on(_)->throws(new TypeError("Validation cannot be extended outside namespace"))
        );
    }

    public function isLeft(): bool
    {
        return matching($this,
            on(Success(_))->returns(false),
            on(Failure(_))->returns(true),
            on(_)->throws(new TypeError("Validation cannot be extended outside namespace"))
        );
    }
}