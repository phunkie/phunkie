<?php

namespace Md\Phunkie\PatternMatching;

class Underscore
{
    public function __get($member)
    {
        return new Wildcard($member);
    }
}