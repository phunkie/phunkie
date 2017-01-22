<?php

namespace spec\Md\Phunkie\Validation;

use Md\Phunkie\Cats\Functor;
use function Md\Phunkie\Functions\function1\compose;
use const Md\Phunkie\Functions\functor\fmap;
use Md\Phunkie\Validation\Failure;
use Md\Phunkie\Validation\Success;
use PhpSpec\ObjectBehavior;

class ValidationSpec extends ObjectBehavior
{
    function it_is_a_functor()
    {
        $this->beAnInstanceOf(Success::class);
        $this->beConstructedWith("yay");
        $this->shouldHaveType(Functor::class);
    }

    function it_is_right_if_success()
    {
        $this->beAnInstanceOf(Success::class);
        $this->beConstructedWith("yay");
        $this->shouldBeRight();
    }

    function it_is_left_if_failure()
    {
        $this->beAnInstanceOf(Failure::class);
        $this->beConstructedWith("nay");
        $this->shouldBeLeft();
    }

    function it_has_map_for_success()
    {
        $this->beAnInstanceOf(Success::class);
        $this->beConstructedWith("yay");
        $this->map("strlen")->shouldBeLike(Success(3));
    }

    function it_has_map_for_failure()
    {
        $this->beAnInstanceOf(Failure::class);
        $this->beConstructedWith("nay");
        $this->map("strlen")->shouldBeLike(Failure("nay"));
    }

    function it_offers_a_curried_Either()
    {
        $success = compose(Either("nay"));
        $failure = compose(Either("nay"));

        expect($success("yay"))->toBeLike(Success("yay"));
        expect($failure(None()))->toBeLike(Failure("nay"));
        expect($failure(null))->toBeLike(Failure("nay"));
    }
}