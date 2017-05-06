<?php

namespace Phunkie\Cats\Free;

use Phunkie\Cats\Free;

class Bind extends Free
{
    private $target;
    private $f;

    public function __construct($target, $f)
    {
        $this->target = $target;
        $this->f = $f;
    }
}
