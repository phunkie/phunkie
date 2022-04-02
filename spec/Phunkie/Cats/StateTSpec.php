<?php

namespace spec\Phunkie\Cats;

use Phunkie\Cats\StateT;
use Md\Unit\TestCase;

class StateTSpec extends TestCase
{
    /**
     * @test
     */
    public function it_runs_function_under_a_context()
    {
        $s = new StateT(Some(function ($n) {
            return Some(Pair($n + 1, $n));
        }));
        $this->assertIsLike($s->run(1), (Some(Pair(2, 1))));
    }
}
