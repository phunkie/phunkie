<?php

namespace spec\Phunkie\Cats;

use Eris\TestTrait;
use Phunkie\Laws\MonadLaws;
use Phunkie\Types\Kind;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

class MonadSpec extends TestCase
{
    use TestTrait;
    use MonadLaws;

    #[Test]
    public function it_obeys_the_law_of_flatmap_associativity()
    {
        // Option
        $fa = Some(42);
        $f = fn (int $x): Kind => Some(gettype($x));
        $g = fn (string $x): Kind => Some(strlen($x) % 2 == 0);
        $this->assertTrue($this->flatMapAssociativity($fa, $f, $g));

        // List
        $fa = ImmList(1, 2, 3);
        $f = fn (int $x): Kind => ImmList(gettype($x));
        $g = fn (string $x): Kind => ImmList(strlen($x) % 2 == 0);
        $this->assertTrue($this->flatMapAssociativity($fa, $f, $g));

        // Set
        $fa = ImmSet(1, 2, 3);
        $f = fn (int $x): Kind => ImmSet(gettype($x));
        $g = fn (string $x): Kind => ImmSet(strlen($x) % 2 == 0);
        $this->assertTrue($this->flatMapAssociativity($fa, $f, $g));

        // Either
        $fa = Right(42);
        $f = fn (int $x): Kind => Right(gettype($x));
        $g = fn (string $x): Kind => Right(strlen($x) % 2 == 0);
        $this->assertTrue($this->flatMapAssociativity($fa, $f, $g));
    }

    #[Test]
    public function it_obeys_the_law_of_left_identity()
    {
        // Option
        $fa = Some(42);
        $a = 1;
        $f = fn (int $x): Kind => Some(($x + 2) % 2 == 0);
        $this->assertTrue($this->leftIdentity($fa, $a, $f));

        // List
        $fa = ImmList(1, 2, 3);
        $a = 1;
        $f = fn (int $x): Kind => ImmList(($x + 2) % 2 == 0);
        $this->assertTrue($this->leftIdentity($fa, $a, $f));

        // Set
        $fa = ImmSet(1, 2, 3);
        $a = 1;
        $f = fn (int $x): Kind => ImmSet(($x + 2) % 2 == 0);
        $this->assertTrue($this->leftIdentity($fa, $a, $f));

        // Either
        $fa = Right(42);
        $a = 1;
        $f = fn (int $x): Kind => Right(($x + 2) % 2 == 0);
        $this->assertTrue($this->leftIdentity($fa, $a, $f));
    }

    #[Test]
    public function it_obeys_the_law_of_right_identity()
    {
        // Option
        $fa = Some(42);
        $this->assertTrue($this->rightIdentity($fa));

        // List
        $fa = ImmList(1, 2, 3);
        $this->assertTrue($this->rightIdentity($fa));

        // Set
        $fa = ImmSet(1, 2, 3);
        $this->assertTrue($this->rightIdentity($fa));

        // Either
        $fa = Right(42);
        $this->assertTrue($this->rightIdentity($fa));
    }
}
