<?php

namespace spec\Phunkie;

use Phunkie\Types\Function1;
use PHPUnit\Framework\TestCase;
use function Phunkie\PatternMatching\Referenced\ListWithTail as ListWithTail;
use function Phunkie\PatternMatching\Referenced\Some as Just;
use function Phunkie\PatternMatching\Referenced\Success as Valid;
use function Phunkie\PatternMatching\Referenced\Failure as Invalid;
use function Phunkie\PatternMatching\Referenced\Right as RightOf;
use function Phunkie\PatternMatching\Referenced\Left as LeftOf;
use function Phunkie\PatternMatching\Referenced\Pair as PairOf;
use function Phunkie\PatternMatching\Referenced\Tuple as TupleOf;
use function Phunkie\PatternMatching\Referenced\Function1 as Function1Of;
use function Phunkie\PatternMatching\Referenced\Nel as NelOf;
use Phunkie\PatternMatching\Referenced\GenericReferenced;
use Phunkie\Types\Cons as ConsType;
use Phunkie\Cats\StateT;
use Phunkie\Cats\Id;
use function Phunkie\PatternMatching\Referenced\StateT as StateTOf;
use function Phunkie\PatternMatching\Referenced\Cons as ConsOf;
use function Phunkie\PatternMatching\Referenced\Id as IdOf;
use function Phunkie\PatternMatching\Referenced\ImmSet as ImmSetOf;
use Phunkie\Cats\IO;
use Phunkie\Cats\State;
use Phunkie\Cats\Reader;
use Phunkie\Cats\Kleisli;
use Phunkie\Cats\OptionT;
use Phunkie\Cats\EitherT;
use Phunkie\Types\ImmString;
use Phunkie\Types\ImmInteger;
use function Phunkie\PatternMatching\Referenced\ImmString as ImmStringOf;
use function Phunkie\PatternMatching\Referenced\ImmInteger as ImmIntegerOf;
use function Phunkie\PatternMatching\Referenced\IO as IOOf;
use function Phunkie\PatternMatching\Referenced\State as StateOf;
use function Phunkie\PatternMatching\Referenced\Reader as ReaderOf;
use function Phunkie\PatternMatching\Referenced\Kleisli as KleisliOf;
use function Phunkie\PatternMatching\Referenced\OptionT as OptionTOf;
use function Phunkie\PatternMatching\Referenced\EitherT as EitherTOf;
use function Phunkie\PatternMatching\Wildcarded\ImmList as WildcardedImmList;

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

    /**
     * @test
     */
    public function it_accepts_reference_when_comparing_rights()
    {
        $on = pmatch(Right(42));
        $result = match (true) {
            $on(RightOf($x)) => $x
        };

        $this->assertEquals(42, $result);
    }

    /**
     * @test
     */
    public function it_accepts_reference_when_comparing_lefts()
    {
        $on = pmatch(Left("boom!"));
        $result = match (true) {
            $on(RightOf($x)) => "right: " . $x,
            $on(LeftOf($x)) => "left: " . $x
        };

        $this->assertEquals("left: boom!", $result);
    }

    /**
     * @test
     */
    public function it_accepts_reference_when_comparing_pairs()
    {
        $on = pmatch(Pair(1, 2));
        $result = match (true) {
            $on(PairOf($x, $y)) => $x + $y
        };

        $this->assertEquals(3, $result);
    }

    /**
     * @test
     */
    public function it_accepts_reference_when_comparing_tuples()
    {
        $on = pmatch(Tuple(1, 2, 3));
        $result = match (true) {
            $on(TupleOf($x, $y, $z)) => $x + $y + $z
        };

        $this->assertEquals(6, $result);
    }

    /**
     * @test
     */
    public function it_does_not_compare_a_tuple_to_one_of_a_different_size()
    {
        $on = pmatch(Tuple(1, 2, 3, 4));
        $result = match (true) {
            $on(TupleOf($x, $y, $z)) => "three",
            $on(_) => "not three"
        };

        $this->assertEquals("not three", $result);
    }

    /**
     * @test
     */
    public function it_accepts_reference_when_comparing_function1s()
    {
        $on = pmatch(Function1::identity());
        $result = match (true) {
            $on(Function1Of($f)) => $f(42)
        };

        $this->assertEquals(42, $result);
    }

    /**
     * @test
     */
    public function it_accepts_reference_when_comparing_nels()
    {
        $on = pmatch(Nel(1, 2, 3));
        $result = match (true) {
            $on(NelOf($x, $xs)) => $x + $xs->head
        };

        $this->assertEquals(3, $result);
    }

    /**
     * @test
     */
    public function it_does_not_compare_a_nel_to_an_ordinary_list()
    {
        $on = pmatch(ImmList(1, 2));
        $result = match (true) {
            $on(NelOf($x, $xs)) => "nel",
            $on(_) => "list"
        };

        $this->assertEquals("list", $result);
    }

    /**
     * @test
     */
    public function it_does_not_match_a_class_that_cannot_supply_the_parts_asked_for()
    {
        // Cons inherits a constructor that declares no parameters, so there is
        // nothing to take apart, and the pattern must not pretend otherwise.
        $on = pmatch(Cons(1, Nil()));
        $result = match (true) {
            $on(new GenericReferenced(ConsType::class, $x, $xs)) => "bound",
            $on(_) => "nothing to bind"
        };

        $this->assertEquals("nothing to bind", $result);
    }

    /**
     * @test
     */
    public function it_accepts_reference_when_comparing_state_transformers()
    {
        $on = pmatch(new StateT(fn ($s) => new Id(Pair($s, $s + 1))));
        $result = match (true) {
            $on(StateTOf($run)) => ($run(1))()->_2
        };

        $this->assertEquals(2, $result);
    }

    /**
     * @test
     */
    public function it_accepts_reference_when_comparing_cons()
    {
        $on = pmatch(Cons(1, ImmList(2, 3)));
        $result = match (true) {
            $on(ConsOf($x, $xs)) => $x + $xs->head
        };

        $this->assertEquals(3, $result);
    }

    /**
     * @test
     */
    public function it_accepts_reference_when_comparing_ids()
    {
        $on = pmatch(new Id(42));
        $result = match (true) {
            $on(IdOf($x)) => $x
        };

        $this->assertEquals(42, $result);
    }

    /**
     * @test
     */
    public function it_accepts_reference_when_comparing_immutable_strings()
    {
        $on = pmatch(new ImmString("hi"));
        $result = match (true) {
            $on(ImmStringOf($s)) => $s
        };

        $this->assertEquals("hi", $result);
    }

    /**
     * @test
     */
    public function it_accepts_reference_when_comparing_immutable_integers()
    {
        $on = pmatch(new ImmInteger(7));
        $result = match (true) {
            $on(ImmIntegerOf($i)) => $i
        };

        $this->assertEquals(7, $result);
    }

    /**
     * @test
     */
    public function it_accepts_reference_when_comparing_ios()
    {
        $on = pmatch(new IO(fn () => 42));
        $result = match (true) {
            $on(IOOf($thunk)) => $thunk()
        };

        $this->assertEquals(42, $result);
    }

    /**
     * @test
     */
    public function it_accepts_reference_when_comparing_states()
    {
        $on = pmatch(new State(fn ($s) => Pair($s, $s + 1)));
        $result = match (true) {
            $on(StateOf($run)) => $run(1)->_2
        };

        $this->assertEquals(2, $result);
    }

    /**
     * @test
     */
    public function it_accepts_reference_when_comparing_readers()
    {
        $on = pmatch(new Reader(fn ($r) => $r * 2));
        $result = match (true) {
            $on(ReaderOf($run)) => $run(21)
        };

        $this->assertEquals(42, $result);
    }

    /**
     * @test
     */
    public function it_accepts_reference_when_comparing_kleislis()
    {
        $on = pmatch(new Kleisli(fn ($x) => new Id($x + 1)));
        $result = match (true) {
            $on(KleisliOf($run)) => ($run(1))()
        };

        $this->assertEquals(2, $result);
    }

    /**
     * @test
     */
    public function it_accepts_reference_when_comparing_option_transformers()
    {
        $on = pmatch(new OptionT(new Id(Some(42))));
        $result = match (true) {
            $on(OptionTOf($monad)) => $monad()->get()
        };

        $this->assertEquals(42, $result);
    }

    /**
     * @test
     */
    public function it_accepts_reference_when_comparing_either_transformers()
    {
        $on = pmatch(new EitherT(new Id(Right(42))));
        $result = match (true) {
            $on(EitherTOf($monad)) => $monad()->getOrElse(0)
        };

        $this->assertEquals(42, $result);
    }

    /**
     * @test
     */
    public function it_accepts_reference_when_comparing_sets()
    {
        $on = pmatch(ImmSet(1, 2));
        $result = match (true) {
            $on(ImmSetOf($a, $b)) => $a + $b
        };

        $this->assertEquals(3, $result);
    }

    /**
     * @test
     */
    public function it_does_not_compare_a_set_to_one_of_a_different_size()
    {
        $on = pmatch(ImmSet(1, 2, 3));
        $result = match (true) {
            $on(ImmSetOf($a, $b)) => "two",
            $on(_) => "not two"
        };

        $this->assertEquals("not two", $result);
    }

    /**
     * @test
     */
    public function it_compares_maps_through_their_list_of_pairs()
    {
        // A map has no pattern of its own: it is taken apart by matching on the
        // list of pairs it is made of, one entry and the rest of them at a time.
        $sum = function ($pairs) use (&$sum) {
            $on = pmatch($pairs);

            return match (true) {
                $on(Nil) => 0,
                $on(ListWithTail($pair, $rest)) => $pair->_2 + $sum($rest)
            };
        };

        $this->assertEquals(6, $sum(ImmMap(["a" => 1, "b" => 2, "c" => 3])->toList()));
    }
}
