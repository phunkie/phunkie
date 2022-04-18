<?php

namespace spec\Phunkie\Cats;

use Md\Unit\TestCase;

use Phunkie\Cats\Free;
use Phunkie\Cats\Free\Pure;
use Phunkie\Cats\Free\Suspend;
use Phunkie\Cats\Free\Bind;
use Phunkie\Cats\NaturalTransformation;
use const Phunkie\Functions\option\optionToList;

class FreeSpec extends TestCase
{
    /**
     * @test
     */
    public function it_implements_pure()
    {
        $this->assertIsLike(new Pure(42), Free::pure(42));
    }

    /**
     * @test
     */
    public function it_implements_liftM()
    {
        $this->assertIsLike(new Suspend(Some(42)), Free::liftM(Some(42)));
    }

    /**
     * @test
     */
    public function it_implements_flatMap()
    {
        $this->assertInstanceOf(
            Bind::class,
            (new Suspend(Some(42)))->flatMap(function ($x) {
                return Free::liftM(Some($x + 1));
            })
        );
    }

    /**
     * @test
     */
    public function it_implements_foldMap_for_pure()
    {
        $this->assertIsLike(
            ImmList(42),
            (new Pure(42))->foldMap(new NaturalTransformation(optionToList))
        );
    }

    /**
     * @test
     */
    public function it_implements_foldMap_for_suspend()
    {
        $this->assertIsLike(
            ImmList(42),
            (new Suspend(Some(42)))
                ->foldMap(new NaturalTransformation(optionToList))
        );
    }

    /**
     * @test
     */
    public function it_implements_foldMap_for_bind()
    {
        $this->assertIsLike(
            ImmList(42),
            (new Bind(
                Free::pure(Some(42)),
                function ($e) {
                    return Free::pure(42);
                }
            ))->foldMap(new NaturalTransformation(optionToList))
        );
    }
}
