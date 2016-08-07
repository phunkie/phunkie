<?php

namespace Md\Phunkie\Types;

use function Md\Phunkie\Functions\semigroup\combine;

final class NonEmptyList extends ImmList
{
    public function combine(ImmList $another) {
        return Nel(...combine($this->toArray(), $another->toArray()));
    }

    public function failure()
    {
        return Failure($this);
    }

    public function success()
    {
        return Success($this);
    }
}