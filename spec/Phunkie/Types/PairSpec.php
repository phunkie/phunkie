<?php

namespace spec\Phunkie\Types;

use Phunkie\Utils\Copiable;
use Md\Unit\TestCase;

class PairSpec extends TestCase
{
    private $pair;

    public function setUp(): void
    {
        $this->pair = Pair("a", 1);
    }

    /**
     * @test
     */
    public function it_is_copiable()
    {
        $this->assertInstanceOf(Copiable::class, $this->pair);
    }
}
