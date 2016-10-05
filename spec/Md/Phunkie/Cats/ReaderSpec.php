<?php

namespace spec\Md\Phunkie\Cats;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ReaderSpec extends ObjectBehavior
{
    function it_wraps_a_function()
    {
        $this->beConstructedWith(function(string $a) { return strrev($a); });
        $this->run("hello")
            ->shouldReturn("olleh");
    }

    function it_implements_map()
    {
        $this->beConstructedWith(function(string $a) { return strrev($a); });
        $this->map(function(string $a) { return strtoupper($a); })
            ->run("hello")
            ->shouldReturn("OLLEH");
    }
}