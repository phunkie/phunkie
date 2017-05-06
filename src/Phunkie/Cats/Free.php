<?php

namespace Phunkie\Cats;

use Phunkie\Cats\Free\Bind;
use Phunkie\Cats\Free\Pure;
use Phunkie\Cats\Free\Suspend;
use Phunkie\Types\Kind;

abstract class Free
{
    public static function pure($a)
    {
        return new Pure($a);
    }

    public static function liftM(Kind $fa)
    {
        return new Suspend($fa);
    }

    public function flatMap($f)
    {
        return new Bind($this, $f);
    }
}
