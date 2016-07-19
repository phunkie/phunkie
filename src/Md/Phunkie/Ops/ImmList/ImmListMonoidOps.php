<?php

namespace Md\Phunkie\Ops\ImmList;

use function Md\Phunkie\Functions\immlist\concat;
use Md\Phunkie\Types\ImmList;

trait ImmListMonoidOps
{
    public function zero()
    {
        return ImmList();
    }

    public function combine(ImmList $b)
    {
        return concat($this, $b);
    }
}