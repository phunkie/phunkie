<?php

namespace Phunkie\Cats;

use Phunkie\Cats\Free\Pure;

abstract class Free
{
    public function pure($a)
    {
        return new Pure($a);
    }
}
