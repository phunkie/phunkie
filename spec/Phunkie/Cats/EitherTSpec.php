<?php

namespace spec\Phunkie\Cats;

use Md\Unit\TestCase;
use Phunkie\Cats\EitherT;
use PHPUnit\Framework\Attributes\Test;

class EitherTSpec extends TestCase
{
    #[Test]
    public function it_implements_map()
    {
        $m = new EitherT(ImmList(Right(1), Left("error"), Right(2)));
        $result = $m->map(fn($x) => $x + 1);

        $this->assertIsLike(
            $result->getValue(),
            ImmList(Right(2), Left("error"), Right(3))
        );
    }

    #[Test]
    public function it_implements_flatMap()
    {
        $m = new EitherT(ImmList(Right(1), Left("error"), Right(2)));

        // Transform even numbers, fail on odd
        $result = $m->flatMap(
            fn($x) =>
            $x % 2 === 0
                ? new EitherT(ImmList(Right($x * 2)))
                : new EitherT(ImmList(Left("odd number")))
        );

        $this->assertIsLike(
            $result->getValue(),
            ImmList(Left("odd number"), Left("error"), Right(4))
        );
    }

    #[Test]
    public function it_implements_isRight()
    {
        $m = new EitherT(ImmList(Right(1), Left("error"), Right(2)));
        $result = $m->isRight();

        $this->assertIsLike(
            $result,
            ImmList(true, false, true)
        );
    }

    #[Test]
    public function it_implements_isLeft()
    {
        $m = new EitherT(ImmList(Right(1), Left("error"), Right(2)));
        $result = $m->isLeft();

        $this->assertIsLike(
            $result,
            ImmList(false, true, false)
        );
    }

    #[Test]
    public function it_implements_getOrElse()
    {
        $m = new EitherT(ImmList(Right(1), Left("error"), Right(2)));
        $result = $m->getOrElse(42);

        $this->assertIsLike(
            $result,
            ImmList(1, 42, 2)
        );
    }

    #[Test]
    public function it_implements_fold()
    {
        $m = new EitherT(ImmList(Right(5), Left("error"), Right(10)));
        $result = $m->fold(
            fn($e) => "Error: $e",
            fn($v) => "Value: $v"
        );

        $this->assertIsLike(
            $result,
            ImmList("Value: 5", "Error: error", "Value: 10")
        );
    }

    #[Test]
    public function it_implements_swap()
    {
        $m = new EitherT(ImmList(Right(1), Left("error")));
        $result = $m->swap();

        $this->assertIsLike(
            $result->getValue(),
            ImmList(Left(1), Right("error"))
        );
    }

    #[Test]
    public function it_implements_bimap()
    {
        $m = new EitherT(ImmList(Right(5), Left("error")));
        $result = $m->bimap(
            fn($e) => "Error: $e",
            fn($v) => $v * 2
        );

        $this->assertIsLike(
            $result->getValue(),
            ImmList(Right(10), Left("Error: error"))
        );
    }

    #[Test]
    public function it_implements_leftMap()
    {
        $m = new EitherT(ImmList(Right(5), Left("error")));
        $result = $m->leftMap(fn($e) => "Error: $e");

        $this->assertIsLike(
            $result->getValue(),
            ImmList(Right(5), Left("Error: error"))
        );
    }

    #[Test]
    public function it_converts_to_optionT()
    {
        $m = new EitherT(ImmList(Right(1), Left("error"), Right(2)));
        $result = $m->toOptionT();

        $this->assertIsLike(
            $result->getValue(),
            ImmList(Some(1), None(), Some(2))
        );
    }

    #[Test]
    public function it_creates_from_optionT()
    {
        $optionList = ImmList(Some(1), None(), Some(2));
        $result = EitherT::fromOptionT($optionList, "not found");

        $this->assertIsLike(
            $result->getValue(),
            ImmList(Right(1), Left("not found"), Right(2))
        );
    }

    #[Test]
    public function it_creates_pure_right_value()
    {
        $result = EitherT::pure(ImmList(), 42);

        $this->assertIsLike(
            $result->getValue(),
            ImmList(Right(42))
        );
    }

    #[Test]
    public function it_creates_left_value()
    {
        $result = EitherT::left(ImmList(), "error");

        $this->assertIsLike(
            $result->getValue(),
            ImmList(Left("error"))
        );
    }

    #[Test]
    public function it_composes_multiple_transformations()
    {
        $m = new EitherT(ImmList(Right(1), Left("error"), Right(2)));

        $result = $m
            ->map(fn($x) => $x * 2)                    // [Right(2), Left("error"), Right(4)]
            ->flatMap(fn($x) => new EitherT(           // [Right(4), Left("error"), Right(8)]
                ImmList(Right($x * 2))
            ));

        $this->assertIsLike(
            $result->getValue(),
            ImmList(Right(4), Left("error"), Right(8))
        );
    }

    #[Test]
    public function it_short_circuits_on_left()
    {
        $m = new EitherT(ImmList(Right(1), Left("error"), Right(2)));

        $result = $m
            ->flatMap(fn($x) => new EitherT(ImmList(Left("failed"))))
            ->map(fn($x) => $x * 100);  // This should not execute for Right values

        $this->assertIsLike(
            $result->getValue(),
            ImmList(Left("failed"), Left("error"), Left("failed"))
        );
    }

    #[Test]
    public function it_implements_kind()
    {
        $m = new EitherT(ImmList(Right(42)));

        $this->assertEquals("EitherT", $m::kind);
        $this->assertEquals(3, $m->getTypeArity());
        $this->assertEquals(['F', 'L', 'R'], $m->getTypeVariables());
    }

    #[Test]
    public function it_preserves_the_outer_monad_structure()
    {
        // Using List as outer monad
        $list = new EitherT(ImmList(Right(1), Right(2)));
        $this->assertIsLike(
            $list->map(fn($x) => $x + 1)->getValue(),
            ImmList(Right(2), Right(3))
        );

        // Using Option as outer monad
        $option = new EitherT(Some(Right(42)));
        $this->assertIsLike(
            $option->map(fn($x) => $x + 1)->getValue(),
            Some(Right(43))
        );
    }

    #[Test]
    public function it_chains_error_handling_computations()
    {
        $safeDivide = fn($a, $b) => $b === 0
            ? Left("Division by zero")
            : Right($a / $b);

        $safeSqrt = fn($x) => $x < 0
            ? Left("Negative sqrt")
            : Right(sqrt($x));

        // Success case
        $computation = (new EitherT(ImmList($safeDivide(16, 4))))
            ->flatMap(fn($x) => new EitherT(ImmList($safeSqrt($x))));

        $this->assertIsLike(
            $computation->getValue(),
            ImmList(Right(2.0))
        );

        // Error case - division by zero
        $computation = (new EitherT(ImmList($safeDivide(16, 0))))
            ->flatMap(fn($x) => new EitherT(ImmList($safeSqrt($x))));

        $this->assertIsLike(
            $computation->getValue(),
            ImmList(Left("Division by zero"))
        );
    }
}
