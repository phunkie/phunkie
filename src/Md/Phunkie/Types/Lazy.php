<?php

namespace Md\Phunkie\Types;

class Lazy
{
    private $run;

    public function __construct(callable $run)
    {
        $this->run = $run;
    }

    public function run()
    {
        return call_user_func($this->run);
    }
}