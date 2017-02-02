<?php

namespace spec\Phunkie\Types;

use Phunkie\Cats\Functor;
use function Phunkie\Functions\function1\compose;
use function Phunkie\Functions\functor\allAs;
use function Phunkie\Functions\functor\asVoid;
use function Phunkie\Functions\functor\fmap;
use function Phunkie\Functions\functor\zipWith;
use const Phunkie\Functions\numbers\increment;
use function Phunkie\Functions\tuple\assign;
use PhpSpec\ObjectBehavior;

class TupleSpec extends ObjectBehavior
{
    function it_lets_you_assign_the_return_values()
    {
        $name = $gender = $age = null;
        (compose(assign($name, $gender, $age)))(Tuple("Luigi", "male", 23));
        expect($name)->toBe("Luigi");
        expect($gender)->toBe("male");
        expect($age)->toBe(23);

        $name = $age = null;
        (compose(assign($name, $age)))(Pair("Luigi", 23));
        expect($name)->toBe("Luigi");
        expect($age)->toBe(23);
    }

    function it_is_a_functor()
    {
        $this->beConstructedWith(1,2,3);
        $this->shouldHaveType(Functor::class);

        $this->map(increment)->shouldBeLike(Tuple(2,3,4));
        expect(fmap(increment, Tuple(1,2,3)))->toBeLike(Tuple(2,3,4));

        $this->as(0)->shouldBeLike(Tuple(0,0,0));
        expect(allAs(0, Tuple(1,2,3)))->toBeLike(Tuple(0,0,0));

        $this->void()->shouldBeLike(Tuple(Unit(), Unit(), Unit()));
        expect(asVoid(Tuple(1,2,3)))->toBeLike(Tuple(Unit(), Unit(), Unit()));

        $this->zipWith(function($x) { return 2 * $x; })->shouldBeLike(Tuple(Pair(1,2), Pair(2,4), Pair(3,6)));
        expect(zipWith(function($x) { return 2 * $x; }, Tuple(1,2,3)))->toBeLike(
            Tuple(Pair(1,2), Pair(2,4), Pair(3,6))
        );
    }
}