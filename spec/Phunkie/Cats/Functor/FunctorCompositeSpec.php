<?php

namespace spec\Phunkie\Cats\Functor;

use Phunkie\Cats\Functor\FunctorComposite;
use Phunkie\Types\Function1;
use Phunkie\Types\ImmList;
use Phunkie\Types\Option;
use Md\Unit\TestCase;
use function Phunkie\Functions\show\is_showable;

/**
 * @mixin FunctorComposite
 */
class FunctorCompositeSpec extends TestCase
{
    private $functor;

    public function setUp(): void
    {
        $this->functor = new FunctorComposite(Option::kind);
    }

    /**
     * @test
     */
    public function it_works_like_a_functor()
    {
        $this->assertIsLike(
            Some(2),
            $this->functor->map(Option(1), function ($x) {
                return $x + 1;
            })
        );
    }

    /**
     * @test
     */
    public function it_is_showable()
    {
        $this->assertTrue(is_showable($this->functor));
        $this->assertEquals(
            'Functor(Option)',
            $this->functor->toString(),
        );
    }

    /**
     * @test
     */
    public function it_is_composable()
    {
        $this->assertEquals(
            'Functor(Option(List))',
            $this->functor->compose(ImmList::kind)->toString()
        );
        $this->assertEquals(
            'Functor(Option(List(Function1)))',
            $this->functor->compose(ImmList::kind)->compose(Function1::kind)->toString()
        );
    }

    /**
     * @test
     */
    public function it_composes_functor_functionality()
    {
        $functor = (new FunctorComposite(ImmList::kind));
        $fa = $functor->compose(Option::kind);
        $xs = ImmList(Some(1), None(), Some(2));
        $this->assertIsLike(
            $fa->map($xs, function ($x) {
                return $x + 1;
            }),
            ImmList(Some(2), None(), Some(3))
        );
    }
}
