<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Phunkie\Types;

use Phunkie\Cats\Show;
use Phunkie\Types\None;
use Phunkie\Types\Some;
use Md\Unit\TestCase;
use Phunkie\Ops\Option\OptionApplicativeOps;
use Md\PropertyTesting\TestTrait;
use Eris\Generator\IntegerGenerator as IntGen;
use Phunkie\Utils\WithFilter;
use function Phunkie\Functions\show\showValue;
use function Phunkie\Functions\show\usesTrait;
use function Phunkie\Functions\applicative\ap;
use function Phunkie\Functions\applicative\pure;

use function Phunkie\Functions\applicative\map2;

use function Phunkie\Functions\monad\bind;
use function Phunkie\Functions\monad\flatten;
use function Phunkie\Functions\monad\mcompose;

class OptionSpec extends TestCase
{
    use TestTrait;

    private $option;

    public function setUp(): void
    {
        $this->option = Option(1);
    }

    /**
     * @test
     */
    public function let()
    {
        $this->assertInstanceOf(Some::class, Option(1));
    }

    /**
     * @test
     */
    public function it_is_showable()
    {
        $this->assertTrue(usesTrait(Option(2), Show::class));
        $this->assertEquals(showValue(Option(2)), "Some(2)");
        $this->assertEquals(showValue(None()), "None");
    }

    /**
     * @test
     */
    public function it_is_a_functor()
    {
        $spec = $this;
        $this->forAll(
            new IntGen()
        )->then(function ($a) use ($spec) {
            $this->assertIsLike(
                Option($a)->map(fn ($x) => $x + 1),
                Some($a + 1)
            );
        });
    }

    /**
     * @test
     */
    public function it_has_filter()
    {
        $this->assertIsLike(
            $this->option->filter(fn ($x) => $x == 1),
            Some(1)
        );
    }

    /**
     * @test
     */
    public function it_has_withFilter()
    {
        $this->assertInstanceOf(
            WithFilter::class,
            $this->option->withFilter(fn ($x) => $x == 1)
        );
    }

    /**
     * @test
     */
    public function its_withFilter_plus_map_to_identity_is_equivalent_to_filter()
    {
        $this->assertIsLike(
            $this
                ->option
                ->withFilter(fn ($x) => $x == 1)
                ->map(fn ($x) => $x),
            $this->option->filter(fn ($x) => $x == 1)
        );
    }

    /**
     * @test
     */
    public function it_is_an_applicative()
    {
        $x = (ap(Option(fn ($a) => $a +1)))(Option(1));
        $this->assertIsLike($x, Option(2));

        $x = (pure(Option))(42);
        $this->assertIsLike($x, Option(42));

        $x = ((map2(fn ($x, $y) => $x + $y))(Option(1)))(Option(2));
        $this->assertIsLike($x, Option(3));
    }

    /**
     * @test
     */
    public function it_is_a_monad()
    {
        $xs = (bind(fn ($a) => Option($a +1)))(Option(1));
        $this->assertIsLike($xs, Option(2));

        $xs = flatten(Option(Option(1)));
        $this->assertIsLike($xs, Option(1));

        $xs = Option("h");
        $f = fn (string $s) => Option($s . "e");
        $g = fn (string $s) => Option($s . "l");
        $h = fn (string $s) => Option($s . "o");
        $hello = mcompose($f, $g, $g, $h);
        $this->assertIsLike($hello($xs), Option("hello"));
    }

    /**
     * @test
     */
    public function it_returns_none_when_none_is_mapped()
    {
        $option = Option(null);
        $this->assertInstanceOf(None::class, $option);

        $this->assertIsLike(
            $option->map(fn ($x) => $x + 1),
            None()
        );
    }

    /**
     * @test
     */
    public function it_has_applicative_ops()
    {
        $ref = new \ReflectionClass($this->option);
        $this->assertTrue($ref->hasMethod('apply'));
        $this->assertTrue($ref->hasMethod('pure'));
        $this->assertTrue($ref->hasMethod('map2'));
        $this->assertTrue(usesTrait($this->option, OptionApplicativeOps::class));
    }

    /**
     * @test
     */
    public function it_returns_none_when_none_is_applied()
    {
        $option = Option(null);
        $this->assertInstanceOf(None::class, $option);
        $this->assertIsLike(
            $option->apply(Option(fn ($x) => $x + 1)),
            None()
        );
    }

    /**
     * @test
     */
    public function it_applies_the_result_of_the_function_to_a_List()
    {
        $spec = $this;
        $this->forAll(
            new IntGen()
        )->then(function ($a) use ($spec) {
            $this->assertIsLike(
                Option($a)->apply(Option(fn ($x) => $x + 1)),
                Some($a + 1)
            );
        });
    }
}
