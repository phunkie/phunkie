<?php

namespace spec\Phunkie\Functions;

use PhpSpec\ObjectBehavior;

class ComprehensionSpec extends ObjectBehavior
{
    function it_is_equivalent_to_map_with_one_bind()
    {
        expect(
            for_(
                __($x)->_(Some('x'))
            )->yields($x)
        )->toBeLike(
            Some('x')->map(function ($x) {
                return $x;
            })
        );
    }

    function it_passes_the_value_to_the_next_bind()
    {
        expect(
            for_(
                __ ($x) ->_ (Some('x')),
                __ ($X) ->_ (Some(strtoupper($x)))
            ) -> yields ($X)
        )->toBe(
            Some('x')->flatMap(function($x) {
                return Some(strtoupper($x))->map(function($X) {
                    return $X;
                });
            })
        );
    }

    function it_works_for_three_calls()
    {
        expect(
            for_ (
                __ ( $x )      ->_ ( Some('x')            ),
                __ ( $X )      ->_ ( Some(strtoupper($x)) ),
                __ ( $quoted ) ->_ ( Some("'$X'")         )
            ) -> yields ( $quoted )
        )->toBeLike(
            Some('x')->flatMap(function($x) {
                return Some(strtoupper($x))->flatMap(function($X) {
                    return Some("'$X'")->map(function($quoted) {
                        return $quoted;
                    });
                });
            })
        );
    }

    function it_yields_pairs()
    {
        expect(
            for_ (
                __ ( $x )      ->_ ( Some('x') ),
                __ ( $y )      ->_ ( Some('y') )
            ) -> yields ( $x, $y )
        )->toBeLike(
            Some('x')->flatMap(function($x) {
                return Some('y')->map(function($y) use ($x) {
                    return Pair($x, $y);
                });
            })
        );
    }

    function it_binds_pairs_to_separate_vars()
    {
        $y = null;
        expect(
            for_ (
                __ ( $x, $y ) ->_ ( Some(Pair('x', 'y')) )
            ) -> yields ( $y )
        )->toBeLike(
            Some(Pair('x', 'y'))->map(function($pair) use ($y) {
                $y = $pair->_2;
                return $y;
            })
        );
    }
}