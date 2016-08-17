<?php

namespace spec\Md\Phunkie\Types;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin \Md\Phunkie\Types\ImmMap
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

    function it_has_plus()
    {
        $this->beConstructedWith(["hello" => "there"]);
        $this->plus("hi", "here")->eqv(ImmMap(["hello" => "there", "hi" => "here"]))->shouldBe(true);
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