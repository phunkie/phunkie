<?php

namespace Md\Phunkie\Cats;

trait Show
{
    abstract function toString(): string;
    public function show()
    {
        return $this->toString();
    }
}