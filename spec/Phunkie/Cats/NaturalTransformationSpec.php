<?php

namespace spec\Phunkie\Cats;

use Md\Unit\TestCase;
use Phunkie\Cats\Show;
use Phunkie\Cats\NaturalTransformation;
use function Phunkie\Functions\show\usesTrait;
use const Phunkie\Functions\option\optionToList;

class NaturalTransformationSpec extends TestCase
{
    /**
     * @test
     */
    public function it_applies_a_natural_transformation()
    {
        $nt = new NaturalTransformation(optionToList);

        $this->assertIsLike($nt(Some(42)), ImmList(42));
    }

    /**
     * @test
     */
    public function it_is_showable()
    {
        $nt = new NaturalTransformation(optionToList);

        $this->assertTrue(usesTrait($nt, Show::class));
        $this->assertEquals("~>[Option, ImmList]", $nt->showType());
    }
}
