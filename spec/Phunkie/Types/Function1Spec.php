<?php

namespace spec\Phunkie\Types;

use Phunkie\Cats\Show;
use Phunkie\Ops\Function1\Function1ApplicativeOps;
use Phunkie\Types\Function1;
use PHPUnit\Framework\TestCase;
use function Phunkie\Functions\show\showValue;
use function Phunkie\Functions\show\usesTrait;
use const Phunkie\Functions\function1\identity;

/**
 * @mixin Function1ApplicativeOps
 */
class Function1Spec extends TestCase
{
    private $f;

    public function setUp(): void
    {
        $this->f = new Function1(fn ($x): int => $x + 1);
    }

    /**
     * @test
     */
    public function it_is_showable()
    {
        $f = new Function1(fn (int $x): int => $x + 1);
        $this->assertTrue(usesTrait($f, Show::class));
        $this->assertEquals(
            showValue($f),
            "Function1(Int=>Int)"
        );
    }

    /**
     * @test
     */
    public function it_is_has_applicative_ops()
    {
        $this->assertTrue(
            usesTrait($this->f, Function1ApplicativeOps::class)
        );
    }

    /**
     * @test
     */
    public function it_returns_an_identity_when_identity_is_applied_to_itself()
    {
        $f = new Function1(identity);
        $this->assertTrue(
            $f
                ->apply(Function1(identity))
                ->eqv(Function1::identity(), Some(42))
        );
    }
}
