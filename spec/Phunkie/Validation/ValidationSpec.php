<?php

namespace spec\Phunkie\Validation;

use Phunkie\Cats\Applicative;
use Phunkie\Cats\Functor;
use Phunkie\Cats\Monad;
use Phunkie\Validation\Failure;
use Phunkie\Validation\Success;
use Md\Unit\TestCase;
use function Phunkie\Functions\applicative\ap;
use function Phunkie\Functions\applicative\map2;
use function Phunkie\Functions\applicative\pure;
use function Phunkie\Functions\function1\compose;
use function Phunkie\Functions\functor\fmap;
use function Phunkie\Functions\monad\bind;
use function Phunkie\Functions\monad\flatten;
use function Phunkie\Functions\validation\toOption;
use const Phunkie\Functions\function1\identity;

class ValidationSpec extends TestCase
{
    /**
     * @test
     */
    public function it_is_right_if_success()
    {
        $v = new Success("yay");

        $this->assertTrue($v->isRight());
    }

    /**
     * @test
     */
    public function it_is_left_if_failure()
    {
        $v = new Failure("nay");

        $this->assertTrue($v->isLeft());
    }

    /**
     * @test
     */
    public function it_offers_a_curried_Either()
    {
        $success = compose(Either("nay"));
        $failure = compose(Either("nay"));

        $this->assertIsLike($success("yay"), Success("yay"));
        $this->assertIsLike($failure(None()), Failure("nay"));
        $this->assertIsLike($failure(null), Failure("nay"));
    }

    /**
     * @test
     */
    public function it_can_be_constructed_with_Attempt()
    {
        $this->assertIsLike(
            Attempt(fn () => 42),
            Success(42)
        );

        $failure = Attempt(function () {
            throw new \Exception("nay");
        });
        $this->assertEquals(
            'Failure(Exception("nay"))',
            ($failure->fold(fn (\Exception $e) => 'Failure(' . get_class($e) . '("' . $e->getMessage() . '")' . ')'))(identity)
        );
    }

    /**
     * @test
     */
    public function it_can_be_converted_to_an_option()
    {
        $this->assertIsLike(toOption(Success(42)), Some(42));
        $this->assertIsLike(toOption(Failure("nay")), None());

        $this->assertIsLike(Success(42)->toOption(), Some(42));
        $this->assertIsLike(Failure("nay")->toOption(), None());
    }

    /**
     * @test
     */
    public function it_is_a_functor()
    {
        $f = new Success("yay");
        $this->assertInstanceOf(Functor::class, $f);
        $this->assertIsLike($f->map("strlen"), Success(3));

        $this->assertIsLike((fmap("strlen"))(Failure("nay")), Failure("nay"));
    }

    /**
     * @test
     */
    public function it_is_a_monad()
    {
        $m = new Success(1);
        $this->assertInstanceOf(Monad::class, $m);

        $this->assertIsLike(
            $m->flatMap(fn ($x) => Success($x + 2)),
            Success(3)
        );
        $this->assertIsLike(
            (bind(fn ($x) => Success($x + 2)))(Failure("nay")),
            Failure("nay")
        );

        $this->assertIsLike(flatten(Success(Success(200))), Success(200));
        $this->assertIsLike(flatten(Failure(Failure(404))), Failure(404));
    }

    /**
     * @test
     */
    public function it_is_an_applicative()
    {
        $ap = new Success(1);
        $this->assertInstanceOf(Applicative::class, $ap);

        $xs = (ap(Success(fn ($a) => $a +1)))(Success(1));
        $this->assertIsLike($xs, Success(2));

        $xs = (ap(Success(fn ($a) => $a +1)))(Failure("nay"));
        $this->assertIsLike($xs, Failure("nay"));

        $xs = (ap(Failure(fn ($a) => $a . "!")))(Failure("nay"));
        $this->assertIsLike($xs, Failure("nay!"));

        $xs = (pure("Success"))(42);
        $this->assertIsLike($xs, Success(42));
        $this->assertIsLike((pure("Failure"))("nay"), Failure("nay"));
        $this->assertIsLike(Failure("")->pure("nay"), Failure("nay"));

        $xs = ((map2(fn ($x, $y) => $x + $y))(Success(1)))(Success(2));
        $this->assertIsLike($xs, Success(3));

        $xs = ((map2(fn ($x, $y) => $x + $y))(Success(1)))(Failure("nay"));
        $this->assertIsLike($xs, Failure("nay"));

        $xs = ((map2(fn ($x, $y) => $x + $y))(Failure("nay")))(Success(1));
        $this->assertIsLike($xs, Failure("nay"));
    }

    /**
     * @test
     */
    public function its_success_return_itself_on_orElse()
    {
        $v = new Success("ok");

        $this->assertIsLike($v->orElse(Failure("nay")), Success("ok"));
    }

    /**
     * @test
     */
    public function its_failure_return_argument_on_orElse()
    {
        $v = new Failure("nay");

        $this->assertIsLike($v->orElse(Success("ok")), Success("ok"));
    }
}
