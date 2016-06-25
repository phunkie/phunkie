<?php

namespace Md\Phunkie\Cats;

use Md\Phunkie\Types\Kind;

interface Applicative extends Apply
{
    public function pure($a): Kind;
}