<?php

namespace spec\Phunkie;

use Phunkie\Types\Function1;
use PHPUnit\Framework\TestCase;
use function Phunkie\PatternMatching\Referenced\ListWithTail as ListWithTail;
use function Phunkie\PatternMatching\Referenced\Some as Just;
use function Phunkie\PatternMatching\Referenced\Success as Valid;
use function Phunkie\PatternMatching\Referenced\Failure as Invalid;
use function Phunkie\PatternMatching\Wildcarded\ImmList as WildcardedImmList;

error_reporting(E_ALL & ~E_DEPRECATED);

class PatternMatchingSpec extends TestCase
{
    /**
     * @test
     */
    public function it_behaves_like_a_match()
    {
        $on = pmatch(1 + 1);
        $result = match (true) {
            $on(3) => 2,
            $on(2) => 3
        };

        // expect($result)->toBe(3);
        $this->assertEquals(3, $result);
    }

    /**
     * @test
     */
    public function it_supports_a_default_clause_with_underscore()
    {
        $on = pmatch(1 + 1);
        $result = match (true) {
            $on(3) => 2,
            $on(4) => 4,
            $on(_) => 6
        };
        // expect($result)->toBe(6);
        $this->assertEquals(6, $result);
    }

    /**
     * @test
     */
    public function it_does_not_break_when_comparing_objects_to_scalars()
    {
        $on = pmatch(1 + 1);
        $result = match (true) {
            $on(Some(3)) => 2,
            $on(2) => 8
        };

        $this->assertEquals(8, $result);
    }

    /**
     * @test
     */
    public function it_supports_wildcard_for_options()
    {
        $on = pmatch(Some(1 + 1));
        $result = match (true) {
            $on(Some(3)) => 2,
            $on(Some(_)) => 10
        };

        $this->assertEquals(10, $result);
    }

    /**
     * @test
     */
    public function it_supports_wildcard_for_none()
    {
        $on = pmatch(None());
        $result = match (true) {
            $on(None) => 10,
            $on(Some(_)) => 2
        };

        $this->assertEquals(10, $result);
    }

    /**
     * @test
     */
    public function it_supports_wildcard_for_function1()
    {
        $on = pmatch(Function1::identity());
        $result = match (true) {
            $on(Some(3)) => 2,
            $on(Function1(_)) => 10
        };

        $this->assertEquals(10, $result);
    }

    /**
     * @test
     */
    public function it_supports_wildcard_for_failure()
    {
        $boom = fn () => Failure(Nel(new \Exception("Boom!")));
        $on = pmatch($boom());
        $result = match (true) {
            $on(Success(_)) => 2,
            $on(Failure(_)) => 10
        };

        $this->assertEquals(10, $result);
    }

    /**
     * @test
     */
    public function it_supports_wildcard_for_success()
    {
        $yay = fn () => Success("yay!");
        $on = pmatch($yay());
        $result = match (true) {
            $on(Failure(_)) => 2,
            $on(Success(_)) => 10
        };

        $this->assertEquals(10, $result);
    }

    /**
     * @test
     */
    public function it_supports_nil_constant_for_comparing_lists()
    {
        $on = pmatch(Nil());
        $result = match (true) {
            $on(Nil) => 10,
            $on(Nel(_)) => 2
        };

        $this->assertEquals(10, $result);
    }

    /**
     * @test
     */
    public function it_accepts_wildcard_for_head_when_comparing_lists()
    {
        $on = pmatch(ImmList(1, 2));
        $result = match (true) {
            $on(Nil) => 10,
            $on(WildcardedImmList(_, Cons(2, Nil))) => 2
        };

        $this->assertEquals(2, $result);

        $on = pmatch(ImmList(1));
        $result = match (true) {
            $on(Nil) => 10,
            $on(WildcardedImmList(_, Nil)) => 2
        };

        $this->assertEquals(2, $result);

        $on = pmatch(ImmList(1, 2));
        $result = match (true) {
            $on(Nil) => 10,
            $on(WildcardedImmList(_, Nil)) => 2,
            $on(WildcardedImmList(_, WildcardedImmList(_, Nil))) => 3
        };

        $this->assertEquals(3, $result);
    }

    /**
     * @test
     */
    public function it_accepts_wildcard_for_tail_when_comparing_lists()
    {
        $on = pmatch(ImmList(1, 2));
        $result = match (true) {
            $on(Nil) => 10,
            $on(WildcardedImmList(1, _)) => 2
        };

        $this->assertEquals(2, $result);
    }

    /**
     * @test
     */
    public function it_accepts_wildcard_for_both_head_and_tail_when_comparing_lists()
    {
        $on = pmatch(ImmList(1, 2));
        $result = match (true) {
            $on(Nil) => 10,
            $on(WildcardedImmList(_, _)) => 2
        };

        $this->assertEquals(2, $result);
    }

    /**
     * @test
     */
    public function it_accepts_wildcard_for_nel_when_comparing_lists()
    {
        $on = pmatch(Nel(1, 2));
        $result = match (true) {
            $on(Nil) => 10,
            $on(Nel(_)) => 2
        };

        $this->assertEquals(2, $result);
    }

    /**
     * @test
     */
    public function it_accepts_reference_when_comparing_lists()
    {
        $on = pmatch(ImmList(1, 2));
        $result = match (true) {
            $on(ListWithTail($x, $xs)) => $x + $xs->head
        };

        $this->assertEquals(3, $result);
    }

    /**
     * @test
     */
    public function it_accepts_reference_when_comparing_options()
    {
        $on = pmatch(Some(42));
        $result = match (true) {
            $on(Just($x)) => $x
        };

        $this->assertEquals(42, $result);
    }

    /**
     * @test
     */
    public function it_accepts_reference_when_comparing_successes()
    {
        $yay = fn () => Success("yay!");
        $on = pmatch($yay());
        $result = match (true) {
            $on(Valid($x)) => $x
        };

        $this->assertEquals($x, $result);
    }

    /**
     * @test
     */
    public function it_accepts_reference_when_comparing_failures()
    {
        $boom = fn () => Failure("boom!");
        $on = pmatch($boom());
        $result = match (true) {
            $on(Invalid($x)) => $x
        };

        $this->assertEquals($x, $result);
    }
}
