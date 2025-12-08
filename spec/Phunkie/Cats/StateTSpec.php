<?php

namespace spec\Phunkie\Cats;

use Md\Unit\TestCase;
use Phunkie\Cats\StateT;
use Phunkie\Cats\State;
use Phunkie\Types\ImmList;
use Phunkie\Types\Pair;
use Phunkie\Types\Unit;

class StateTSpec extends TestCase
{
    /**
     * @test
     */
    public function it_runs_function_under_a_context()
    {
        $s = new StateT(Some(new State(fn ($n) => Pair($n + 1, $n))));
        $this->assertIsLike($s->run(1), (Some(Pair(2, 1))));
    }

    /**
     * @test
     */
    public function it_implements_map()
    {
        $state = ImmList(
            new State(fn($s) => Pair($s + 1, $s))
        );
        
        $st = new StateT($state);
        $result = $st
            ->map(fn($x) => $x * 2)
            ->run(1);

        $this->assertIsLike(
            $result,
            ImmList(Pair(2, 2))  // state: 1->2, value: 1*2
        );
    }

    /**
     * @test
     */
    public function it_implements_flatMap()
    {
        $increment = new StateT(ImmList(
            new State(fn($s) => Pair($s + 1, $s))
        ));

        $double = fn($x) => new StateT(ImmList(
            new State(fn($s) => Pair($s, $x * 2))
        ));

        $result = $increment
            ->flatMap($double)
            ->run(1);

        $this->assertIsLike(
            $result,
            ImmList(Pair(2, 2))  // state: 1->2, value: 1*2
        );
    }

    /**
     * @test
     */
    public function it_implements_modify()
    {
        // Test with ImmList monad
        $listState = new StateT(ImmList(
            new State(fn($s) => Pair($s, "value"))
        ));
        $result = $listState
            ->modify(fn($s) => $s + 1)
            ->run(1);
        $this->assertIsLike(
            $result,
            ImmList(Pair(2, Unit()))  // state modified, value replaced with Unit
        );

        // Test with Option monad
        $optionState = new StateT(Some(
            new State(fn($s) => Pair($s, "value"))
        ));
        $result = $optionState
            ->modify(fn($s) => $s * 2)
            ->run(3);
        $this->assertIsLike(
            $result,
            Some(Pair(6, Unit()))  // state modified, value replaced with Unit
        );
    }
}
