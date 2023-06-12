<?php

namespace spec\Phunkie\Types;

use Phunkie\Cats\Functor;
use Md\Unit\TestCase;
use function Phunkie\Functions\function1\compose;
use function Phunkie\Functions\functor\allAs;
use function Phunkie\Functions\functor\asVoid;
use function Phunkie\Functions\functor\fmap;
use function Phunkie\Functions\functor\zipWith;
use function Phunkie\Functions\tuple\assign;
use const Phunkie\Functions\numbers\increment;

class TupleSpec extends TestCase
{
    /**
     * @test
     */
    public function it_lets_you_assign_the_return_values()
    {
        $name = $gender = $age = null;
        (compose(assign($name, $gender, $age)))(Tuple("Luigi", "male", 23));
        $this->assertEquals($name, "Luigi");
        $this->assertEquals($gender, "male");
        $this->assertEquals($age, 23);

        $name = $age = null;
        (compose(assign($name, $age)))(Pair("Luigi", 23));
        $this->assertEquals($name, "Luigi");
        $this->assertEquals($age, 23);
    }

    /**
     * @test
     */
    public function it_is_a_functor()
    {
        $tuple = Tuple(1, 2, 3);
        $this->assertInstanceOf(Functor::class, $tuple);

        $this->assertIsLike($tuple->map(increment), Tuple(2, 3, 4));
        $this->assertIsLike(fmap(increment, Tuple(1, 2, 3)), Tuple(2, 3, 4));

        $this->assertIsLike($tuple->as(0), Tuple(0, 0, 0));
        $this->assertIsLike(allAs(0, Tuple(1, 2, 3)), Tuple(0, 0, 0));

        $this->assertIsLike($tuple->void(), Tuple(Unit(), Unit(), Unit()));
        $this->assertIsLike(asVoid(Tuple(1, 2, 3)), Tuple(Unit(), Unit(), Unit()));

        $this->assertIsLike(
            $tuple->zipWith(fn ($x) => 2 * $x),
            Tuple(Pair(1, 2), Pair(2, 4), Pair(3, 6))
        );
        $this->assertIsLike(
            zipWith(fn ($x) => 2 * $x, Tuple(1, 2, 3)),
            Tuple(Pair(1, 2), Pair(2, 4), Pair(3, 6))
        );
    }
}
