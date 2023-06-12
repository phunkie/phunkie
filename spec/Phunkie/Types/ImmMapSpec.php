<?php

namespace spec\Phunkie\Types;

use Md\Unit\TestCase;
use Phunkie\Cats\Functor;
use Phunkie\Types\Pair;
use function Phunkie\Functions\functor\allAs;
use function Phunkie\Functions\functor\asVoid;
use function Phunkie\Functions\functor\fmap;
use function Phunkie\Functions\functor\zipWith;
use function Phunkie\Functions\show\show;

/**
 * @mixin \Phunkie\Types\ImmMap
 */
class ImmMapSpec extends TestCase
{
    /**
     * @test
     */
    public function it_can_be_created_with_associative_arrays()
    {
        $map = ImmMap(["hello" => "there"]);
        $this->assertIsLike($map->get("hello"), Some("there"));
    }

    /**
     * @test
     */
    public function it_returns_None_when_key_does_not_exist()
    {
        $map = ImmMap(["hello" => "there"]);
        $this->assertIsLike($map->get("hi"), None());
    }

    /**
     * @test
     */
    public function it_can_be_constructed_with_an_even_number_of_objects()
    {
        $map = ImmMap(
            new AccountNumber(1),
            new Account("John smith"),
            new AccountNumber(2),
            new Account("Chuck Norris")
        );

        $this->assertIsLike(
            $map->get(new AccountNumber(2)),
            Some(new Account("Chuck Norris"))
        );
    }

    /**
     * @test
     */
    public function it_cannot_be_constructed_with_an_odd_number()
    {
        $this->expectError();
        $this->expectErrorMessage("not enough arguments for constructor ImmMap");

        $map = ImmMap(
            new AccountNumber(1),
            new Account("John smith"),
            new AccountNumber(2),
            new Account("Chuck Norris"),
            new AccountNumber(3)
        );
    }

    /**
     * @test
     */
    public function it_also_complains_on_one_argument_if_it_is_not_an_array()
    {
        $this->expectError();

        $map = ImmMap(new AccountNumber(1));
    }

    /**
     * @test
     */
    public function it_lets_you_check_if_a_key_exists()
    {
        $map = ImmMap(["hello" => "there"]);
        $this->assertTrue($map->contains("hello"));
        $this->assertFalse($map->contains("hi"));
    }

    /**
     * @test
     */
    public function it_lets_you_set_a_default_value()
    {
        $map = ImmMap(["hello" => "there"]);
        $this->assertEquals("here", $map->getOrElse("hi", "here"));
        $this->assertEquals("there", $map->getOrElse("hello", "here"));
    }

    /**
     * @test
     */
    public function it_is_showable()
    {
        $map = ImmMap(["hi" => "here", "hello" => "there"]);
        $this->assertEquals($map->toString(), 'Map("hi" -> "here", "hello" -> "there")');
    }

    /**
     * @test
     */
    public function it_has_minus()
    {
        $map = ImmMap(["hello" => "there", "hi" => "here"]);
        $this->assertTrue(
            $map->minus("hi")->eqv(ImmMap(["hello" => "there"]))
        );
    }

    /**
     * @test
     */
    public function it_has_plus()
    {
        $map = ImmMap(["hello" => "there"]);
        $this->assertTrue(
            $map
                ->plus("hi", "here")
                ->eqv(ImmMap(["hello" => "there", "hi" => "here"]))
        );
    }

    /**
     * @test
     */
    public function it_replaces_value_when_adding_with_same_key()
    {
        $map = ImmMap(["hello" => "there", "hi" => "here"]);
        $this->assertTrue(
            $map
                ->plus("hi", "nowhere")
                ->eqv(ImmMap(["hello" => "there", "hi" => "nowhere"]))
        );
    }

    /**
     * @test
     */
    public function it_can_be_copied()
    {
        $map = ImmMap(["hello" => "there", "hi" => "here"]);
        $this->assertEquals($map->copy()->show(), $map->show());
    }

    /**
     * @test
     */
    public function it_is_a_functor()
    {
        $f = ImmMap(["a" => 1, "b" => 2, "c" => 3]);
        $increment = fn (Pair $keyValue) => Pair($keyValue->_1, $keyValue->_2 + 1);

        $this->assertTrue(
            $f->map($increment)->eqv(ImmMap(["a" => 2, "b" => 3, "c" => 4]))
        );
        $this->assertTrue(
            fmap(
                $increment,
                ImmMap(["a" => 1, "b" => 2, "c" => 3])
            )
                    ->eqv(
                        ImmMap(["a" => 2, "b" => 3, "c" => 4])
                    )
        );

        $this->assertTrue($f->as(Pair("a", 0))->eqv(ImmMap(["a" => 0])));
        $this->assertTrue(
            allAs(Pair("a", 0), ImmMap(["a" => 1, "b" => 2, "c" => 3]))
                ->eqv(ImmMap(["a" => 0]))
        );

        $this->assertTrue(
            $f->as(Pair(_, 0))->eqv(ImmMap(["a" => 0, "b" => 0, "c" => 0]))
        );
        $this->assertTrue(
            allAs(Pair(_, 0), ImmMap(["a" => 1, "b" => 2, "c" => 3]))
                ->eqv(ImmMap(["a" => 0, "b" => 0, "c" => 0]))
        );

        $this->assertTrue(
            $f->void()->eqv(ImmMap(["a" => Unit(), "b" => Unit(), "c" => Unit()]))
        );
        $this->assertTrue(
            asVoid(
                ImmMap(["a" => 1, "b" => 2, "c" => 3])
            )
                    ->eqv(
                        ImmMap(["a" => Unit(), "b" => Unit(), "c" => Unit()])
                    )
        );

        $this->assertTrue(
            $f
                ->zipWith(fn ($x) => 2 * $x)
                ->eqv(
                    ImmMap(
                        [
                            "a" => Pair(1, 2),
                            "b" => Pair(2, 4),
                            "c" => Pair(3, 6)
                        ]
                    )
                )
        );
        $this->assertTrue(
            zipWith(
                fn ($x) => 2 * $x,
                ImmMap(["a" => 1, "b" => 2, "c" => 3])
            )->eqv(
                ImmMap(
                    [
                        "a" => Pair(1, 2),
                        "b" => Pair(2, 4),
                        "c" => Pair(3, 6)
                    ]
                )
            )
        );
    }
}

class AccountNumber
{
    private $number;
    public function __construct(int $number)
    {
        $this->number = $number;
    }
}

class Account
{
    private $name;
    public function __construct(string $name)
    {
        $this->name = $name;
    }
}
