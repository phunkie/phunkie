<?php

namespace spec\Phunkie\Cats;

use Phunkie\Cats\Kleisli;
use Md\Unit\TestCase;
use PHPUnit\Framework\Attributes\Test;

class KleisliSpec extends TestCase
{
    #[Test]
    public function it_wraps_a_function_returning_a_monad()
    {
        $k = new Kleisli(fn($x) => Some($x * 2));
        $this->assertIsLike(Some(84), $k->run(42));
    }

    #[Test]
    public function it_composes_with_andThen()
    {
        $parseString = new Kleisli(
            fn($s) => 
            is_numeric($s) ? Some(intval($s)) : None()
        );
        $double = new Kleisli(fn($i) => Some($i * 2));

        $parseAndDouble = $parseString->andThen($double);

        $this->assertIsLike(Some(84), $parseAndDouble->run("42"));
        $this->assertIsLike(None(), $parseAndDouble->run("not a number"));
    }

    #[Test]
    public function it_composes_with_compose()
    {
        $double = new Kleisli(fn($i) => Some($i * 2));
        $toString = new Kleisli(fn($i) => Some(strval($i)));

        $doubleAndString = $toString->compose($double);

        $this->assertIsLike(Some("84"), $doubleAndString->run(42));
    }

    #[Test]
    public function it_obeys_the_left_identity_law()
    {
        $f = new Kleisli(fn($x) => Some($x * 2));
        $pure = new Kleisli(fn($x) => Some($x));
        
        $value = 42;
        
        // pure(a).andThen(f) == f(a)
        $this->assertIsLike(
            $f->run($value),
            $pure->andThen($f)->run($value)
        );
    }

    #[Test]
    public function it_obeys_the_right_identity_law()
    {
        $f = new Kleisli(fn($x) => Some($x * 2));
        $pure = new Kleisli(fn($x) => Some($x));
        
        $value = 42;
        
        // f.andThen(pure) == f
        $this->assertIsLike(
            $f->run($value),
            $f->andThen($pure)->run($value)
        );
    }

    #[Test]
    public function it_obeys_the_associativity_law()
    {
        $f = new Kleisli(fn($x) => Some($x * 2));
        $g = new Kleisli(fn($x) => Some($x + 1));
        $h = new Kleisli(fn($x) => Some(strval($x)));
        
        $value = 42;
        
        // (f.andThen(g)).andThen(h) == f.andThen(g.andThen(h))
        $this->assertIsLike(
            $f->andThen($g)->andThen($h)->run($value),
            $f->andThen($g->andThen($h))->run($value)
        );
    }

    #[Test]
    public function it_handles_failure_cases()
    {
        $validatePositive = new Kleisli(
            fn($x) => 
            $x > 0 ? Some($x) : None()
        );
        $validateEven = new Kleisli(
            fn($x) => 
            $x % 2 == 0 ? Some($x) : None()
        );

        $validateBoth = $validatePositive->andThen($validateEven);

        $this->assertIsLike(Some(42), $validateBoth->run(42));  // Positive and even
        $this->assertIsLike(None(), $validateBoth->run(43));    // Positive but odd
        $this->assertIsLike(None(), $validateBoth->run(-42));   // Even but negative
        $this->assertIsLike(None(), $validateBoth->run(-43));   // Neither
    }
} 