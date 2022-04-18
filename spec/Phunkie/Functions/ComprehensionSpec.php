<?php

namespace spec\Phunkie\Functions;

use Md\Unit\TestCase;
use const Phunkie\Functions\semigroup\combine;

class ComprehensionSpec extends TestCase
{
    /**
     * @test
     */
    public function it_is_equivalent_to_map_with_one_bind()
    {
        $this->assertIsLike(
            for_(
                __($x)->_(Some('x'))
            )->yields($x),
            Some('x')->map(function ($x) {
                return $x;
            })
        );
    }

    /**
     * @test
     */
    public function it_passes_the_value_to_the_next_bind()
    {
        $this->assertIsLike(
            for_(
                __($x) ->_(Some('x')),
                __($X) ->_(Some(strtoupper($x)))
            ) -> yields($X),
            Some('x')->flatMap(function ($x) {
                return Some(strtoupper($x))->map(function ($X) {
                    return $X;
                });
            })
        );
    }

    /**
     * @test
     */
    public function it_works_for_three_calls()
    {
        $this->assertIsLike(
            for_(
                __($x)      ->_(Some('x')),
                __($X)      ->_(Some(strtoupper($x))),
                __($quoted) ->_(Some("'$X'"))
            ) -> yields($quoted),
            Some('x')->flatMap(function ($x) {
                return Some(strtoupper($x))->flatMap(function ($X) {
                    return Some("'$X'")->map(function ($quoted) {
                        return $quoted;
                    });
                });
            })
        );
    }

    /**
     * @test
     */
    public function it_yields_pairs()
    {
        $this->assertIsLike(
            for_(
                __($x)      ->_(Some('x')),
                __($y)      ->_(Some('y'))
            ) -> yields($x, $y),
            Some('x')->flatMap(function ($x) {
                return Some('y')->map(function ($y) use ($x) {
                    return Pair($x, $y);
                });
            })
        );
    }

    /**
     * @test
     */
    public function it_binds_pairs_to_separate_vars()
    {
        $y = null;
        $this->assertIsLike(
            for_(
                __($x, $y) ->_(Some(Pair('x', 'y')))
            ) -> yields($y),
            Some(Pair('x', 'y'))->map(function ($pair) use ($y) {
                $y = $pair->_2;
                return $y;
            })
        );
    }

    /**
     * @test
     */
    public function it_lets_you_apply_a_function_to_binded_variables()
    {
        $this->assertIsLike(
            for_(
                __($x)      ->_(Some('x')),
                __($y)      ->_(Some('y'))
            ) -> call(combine, $x, $y),
            Some('x')->flatMap(function ($x) {
                return Some('y')->map(function ($y) use ($x) {
                    return call_user_func_array(combine, [$x, $y]);
                });
            })
        );
    }
}
