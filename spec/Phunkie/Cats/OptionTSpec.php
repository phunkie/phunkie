<?php

namespace spec\Phunkie\Cats;

use Md\Unit\TestCase;
use Phunkie\Cats\OptionT;
use Phunkie\Types\ImmList;
use Phunkie\Types\Option;

class OptionTSpec extends TestCase
{
    /**
     * @test
     */
    public function it_implements_map()
    {
        $m = new OptionT(ImmList(Some(1), None(), Some(2)));
        $result = $m->map(fn($x) => $x + 1);

        $this->assertIsLike(
            $result->getValue(),
            ImmList(Some(2), None(), Some(3))
        );
    }

    /**
     * @test
     */
    public function it_implements_flatMap()
    {
        $m = new OptionT(ImmList(Some(1), None(), Some(2)));
        
        // More complex transformation showing proper monadic binding
        $result = $m->flatMap(fn($x) => 
            $x % 2 === 0 
                ? new OptionT(ImmList(Some($x * 2))) 
                : new OptionT(ImmList(None()))
        );

        $this->assertIsLike(
            $result->getValue(),
            ImmList(None(), None(), Some(4))
        );
    }

    /**
     * @test
     */
    public function it_implements_isDefined()
    {
        $m = new OptionT(ImmList(Some(1), None(), Some(2)));
        $result = $m->isDefined();

        $this->assertIsLike(
            $result,
            ImmList(true, false, true)
        );
    }

    /**
     * @test
     */
    public function it_implements_isEmpty()
    {
        $m = new OptionT(ImmList(Some(1), None(), Some(2)));
        $result = $m->isEmpty();

        $this->assertIsLike(
            $result,
            ImmList(false, true, false)
        );
    }

    /**
     * @test
     */
    public function it_implements_getOrElse()
    {
        $m = new OptionT(ImmList(Some(1), None(), Some(2)));
        $result = $m->getOrElse(42);

        $this->assertIsLike(
            $result,
            ImmList(1, 42, 2)
        );
    }

    /**
     * @test
     */
    public function it_composes_multiple_transformations()
    {
        $m = new OptionT(ImmList(Some(1), None(), Some(2)));
        
        $result = $m
            ->map(fn($x) => $x * 2)                    // [Some(2), None, Some(4)]
            ->flatMap(fn($x) => new OptionT(           // [Some(4), None, Some(8)]
                ImmList(Some($x * 2))
            ));

        $this->assertIsLike(
            $result->getValue(),
            ImmList(Some(4), None(), Some(8))
        );
    }

    /**
     * @test
     */
    public function it_implements_kind()
    {
        $m = new OptionT(ImmList(Some(42)));
        
        $this->assertEquals("OptionT", $m::kind);
        $this->assertEquals(2, $m->getTypeArity());
        $this->assertEquals(['F', 'A'], $m->getTypeVariables());
    }

    /**
     * @test
     */
    public function it_preserves_the_outer_monad_structure()
    {
        // Using different monad structures
        $list = new OptionT(ImmList(Some(1), Some(2)));
        $this->assertIsLike(
            $list->map(fn($x) => $x + 1)->getValue(),
            ImmList(Some(2), Some(3))
        );

        $option = new OptionT(Some(Some(42)));
        $this->assertIsLike(
            $option->map(fn($x) => $x + 1)->getValue(),
            Some(Some(43))
        );
    }
}
