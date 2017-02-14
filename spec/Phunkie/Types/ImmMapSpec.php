<?php

namespace spec\Phunkie\Types;

use PhpSpec\ObjectBehavior;
use Phunkie\Cats\Functor;
use function Phunkie\Functions\functor\allAs;
use function Phunkie\Functions\functor\asVoid;
use function Phunkie\Functions\functor\fmap;
use function Phunkie\Functions\functor\zipWith;
use function Phunkie\Functions\show\show;
use Phunkie\Types\Pair;

/**
 * @mixin \Phunkie\Types\ImmMap
 */
class ImmMapSpec extends ObjectBehavior
{
    function it_can_be_created_with_associative_arrays()
    {
        $this->beConstructedWith(["hello" => "there"]);
        $this->get("hello")->shouldBeLike(Some("there"));
    }

    function it_returns_None_when_key_does_not_exist()
    {
        $this->beConstructedWith(["hello" => "there"]);
        $this->get("hi")->shouldBeLike(None());
    }

    function it_can_be_constructed_with_an_even_number_of_objects()
    {
        $this->beConstructedWith(
            new AccountNumber(1), new Account("John smith"),
            new AccountNumber(2), new Account("Chuck Norris")
        );

        $this->get(new AccountNumber(2))->shouldBeLike(Some(new Account("Chuck Norris")));
    }

    function it_cannot_be_constructed_with_an_odd_number()
    {
        $this->beConstructedWith(
            new AccountNumber(1), new Account("John smith"),
            new AccountNumber(2), new Account("Chuck Norris"),
            new AccountNumber(3)
        );

        $this->shouldThrow()->duringInstantiation();
    }

    function it_also_complains_on_one_argument_if_it_is_not_an_array()
    {
        $this->beConstructedWith(new AccountNumber(1));
        $this->shouldThrow()->duringInstantiation();
    }

    function it_lets_you_check_if_a_key_exists()
    {
        $this->beConstructedWith(["hello" => "there"]);
        $this->contains("hello")->shouldReturn(true);
        $this->contains("hi")->shouldReturn(false);
    }

    function it_lets_you_set_a_default_value()
    {
        $this->beConstructedWith(["hello" => "there"]);
        $this->getOrElse("hi", "here")->shouldReturn("here");
        $this->getOrElse("hello", "here")->shouldReturn("there");
    }

    function it_is_showable()
    {
        $this->beConstructedWith(["hi" => "here", "hello" => "there"]);
        $this->toString()->shouldBe('Map("hi" -> "here", "hello" -> "there")');
    }

    function it_has_minus()
    {
        $this->beConstructedWith(["hello" => "there", "hi" => "here"]);
        $this->minus("hi")->eqv(ImmMap(["hello" => "there"]))->shouldBe(true);
    }

    function it_has_plus()
    {
        $this->beConstructedWith(["hello" => "there"]);
        $this->plus("hi", "here")->eqv(ImmMap(["hello" => "there", "hi" => "here"]))->shouldBe(true);
    }

    function it_can_be_copied()
    {
        $this->beConstructedWith(["hello" => "there", "hi" => "here"]);
        $this->copy()->show()->shouldBe($this->getWrappedObject()->show());
    }

    function it_is_a_functor()
    {
        $this->beConstructedWith(["a" => 1, "b" => 2, "c" => 3]);
        $this->shouldHaveType(Functor::class);
        $increment = function(Pair $keyValue) {
            return Pair($keyValue->_1,  $keyValue->_2 + 1);
        };

        $this->map($increment)->eqv(ImmMap(["a" => 2, "b" => 3, "c" => 4]))->shouldBe(true);
        expect(fmap($increment, ImmMap(["a" => 1, "b" => 2, "c" => 3]))->eqv(ImmMap(["a" => 2, "b" => 3, "c" => 4])))->toBe(true);

        $this->as(Pair("a", 0))->eqv(ImmMap(["a" => 0]))->shouldBe(true);
        expect(allAs(Pair("a", 0), ImmMap(["a" => 1, "b" => 2, "c" => 3]))->eqv(ImmMap(["a" => 0])))->toBe(true);

        $this->as(Pair(_, 0))->eqv(ImmMap(["a" => 0, "b" => 0, "c" => 0]))->shouldBe(true);
        expect(allAs(Pair(_, 0), ImmMap(["a" => 1, "b" => 2, "c" => 3]))->eqv(ImmMap(["a" => 0, "b" => 0, "c" => 0])))->toBe(true);

        $this->void()->eqv(ImmMap(["a" => Unit(), "b" => Unit(), "c" => Unit()]))->shouldBe(true);
        expect(asVoid(ImmMap(["a" => 1, "b" => 2, "c" => 3]))->eqv(ImmMap(["a" => Unit(), "b" => Unit(), "c" => Unit()])))->toBe(true);

        $this->zipWith(function($x) { return 2 * $x; })->eqv(ImmMap(["a" => Pair(1,2), "b" => Pair(2,4), "c" => Pair(3,6)]))->shouldBe(true);
        expect(zipWith(function($x) { return 2 * $x; }, ImmMap(["a" => 1, "b" => 2, "c" => 3]))->eqv(
            ImmMap(["a" => Pair(1,2), "b" => Pair(2,4), "c" => Pair(3,6)])
        ))->toBe(true);
    }
}









class AccountNumber {
    private $number;
    public function __construct(int $number) { $this->number = $number; }
}

class Account {
    private $name;
    public function __construct(string $name) { $this->name = $name; }
}