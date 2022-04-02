<?php

namespace Phunkie\Cats\Free;

use Phunkie\Cats\Free;

class Suspend extends Free
{
    private $fa;

    public function __construct($fa)
    {
        $this->fa = $fa;
    }
}
