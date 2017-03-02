<?php

namespace spec\Phunkie\Validation;

use Phunkie\Cats\Applicative;
use Phunkie\Cats\Functor;
use Phunkie\Cats\Monad;
use function Phunkie\Functions\applicative\ap;
use function Phunkie\Functions\applicative\map2;
use function Phunkie\Functions\applicative\pure;
use function Phunkie\Functions\function1\compose;
use const Phunkie\Functions\function1\identity;
use function Phunkie\Functions\functor\fmap;
use function Phunkie\Functions\monad\bind;
use function Phunkie\Functions\monad\flatten;
use function Phunkie\Functions\validation\toOption;
use Phunkie\Validation\Failure;
use Phunkie\Validation\Success;
use PhpSpec\ObjectBehavior;

class ValidationSpec extends ObjectBehavior
{
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

    function it_offers_a_curried_Either()
    {
        $success = compose(Either("nay"));
        $failure = compose(Either("nay"));

        expect($success("yay"))->toBeLike(Success("yay"));
        expect($failure(None()))->toBeLike(Failure("nay"));
        expect($failure(null))->toBeLike(Failure("nay"));
    }

    function it_can_be_constructed_with_Attempt()
    {
        expect(Attempt(function(){ return 42; }))->toBeLike(Success(42));

        $failure = Attempt(function(){ throw new \Exception("nay"); });
        expect(($failure->fold(function(\Exception $e) {
            return 'Failure(' . get_class($e) . '("' . $e->getMessage() . '")' . ')';
        }))(identity))->toBe('Failure(Exception("nay"))');
    }

    function it_can_be_converted_to_an_option()
    {
        expect(toOption(Success(42)))->toBeLike(Some(42));
        expect(toOption(Failure("nay")))->toBe(None());

        expect(Success(42)->toOption())->toBeLike(Some(42));
        expect(Failure("nay")->toOption())->toBe(None());
    }

    function it_is_a_functor()
    {
        $this->beAnInstanceOf(Success::class);
        $this->beConstructedWith("yay");
        $this->shouldHaveType(Functor::class);

        $this->map("strlen")->shouldBeLike(Success(3));

        expect((fmap ("strlen")) (Failure("nay")))->toBeLike(Failure("nay"));
    }

    function it_is_a_monad()
    {
        $this->beAnInstanceOf(Success::class);
        $this->beConstructedWith(1);
        $this->shouldHaveType(Monad::class);

        $this->flatMap(function($x) { return Success($x + 2); })->shouldBeLike(Success(3));
        expect((bind (function($x) { return Success($x + 2); } )) (Failure("nay")))->toBeLike(
            Failure("nay")
        );

        expect(flatten(Success(Success(200))))->toBeLike(Success(200));
        expect(flatten(Failure(Failure(404))))->toBeLike(Failure(404));
    }

    function it_is_an_applicative()
    {
        $this->beAnInstanceOf(Success::class);
        $this->beConstructedWith(1);
        $this->shouldHaveType(Applicative::class);

        $xs = (ap (Success(function($a) { return $a +1; }))) (Success(1));
        expect($xs)->toBeLike(Success(2));

        $xs = (ap (Success(function($a) { return $a +1; }))) (Failure("nay"));
        expect($xs)->toBeLike(Failure("nay"));

        $xs = (ap (Failure(function($a) { return $a . "!"; }))) (Failure("nay"));
        expect($xs)->toBeLike(Failure("nay!"));

        $xs = (pure ("Success")) (42);
        expect($xs)->toBeLike(Success(42));
        expect((pure ("Failure")) ("nay"))->toBeLike(Failure("nay"));
        expect(Failure("")->pure("nay"))->toBeLike(Failure("nay"));

        $xs = ((map2 (function($x, $y) { return $x + $y; })) (Success(1))) (Success(2));
        expect($xs)->toBeLike(Success(3));

        $xs = ((map2 (function($x, $y) { return $x + $y; })) (Success(1))) (Failure("nay"));
        expect($xs)->toBeLike(Failure("nay"));

        $xs = ((map2 (function($x, $y) { return $x + $y; })) (Failure("nay"))) (Success(1));
        expect($xs)->toBeLike(Failure("nay"));
    }

    function its_success_return_itself_on_orElse()
    {
        $this->beAnInstanceOf(Success::class);
        $this->beConstructedWith("ok");

        $this->orElse(Failure("nay"))->shouldBeLike(Success("ok"));
    }

    function its_failure_return_argument_on_orElse()
    {
        $this->beAnInstanceOf(Failure::class);
        $this->beConstructedWith("nay");

        $this->orElse(Success("ok"))->shouldBeLike(Success("ok"));
    }
}