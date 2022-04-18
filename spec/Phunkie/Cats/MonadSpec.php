<?php

namespace spec\Phunkie\Cats;

use Eris\TestTrait;
use Phunkie\Laws\MonadLaws;
use Phunkie\Types\Kind;
use PHPUnit\Framework\TestCase;

class MonadSpec extends TestCase
{
    use TestTrait;
    use MonadLaws;

    /**
     * @test
     */
    public function it_obeys_the_law_of_flatmap_associativity()
    {
        // Option
        $fa = Some(42);
        $f = function (int $x): Kind {
            return Some(gettype($x));
        };
        $g = function (string $x): Kind {
            return Some(strlen($x) % 2 == 0);
        };
        $this->assertTrue($this->flapMapAssociativity($fa, $f, $g));

        // List
        $fa = ImmList(1, 2, 3);
        $f = function (int $x): Kind {
            return ImmList(gettype($x));
        };
        $g = function (string $x): Kind {
            return ImmList(strlen($x) % 2 == 0);
        };
        $this->assertTrue($this->flapMapAssociativity($fa, $f, $g));

        // Set
        $fa = ImmSet(1, 2, 3);
        $f = function (int $x): Kind {
            return ImmSet(gettype($x));
        };
        $g = function (string $x): Kind {
            return ImmSet(strlen($x) % 2 == 0);
        };
        $this->assertTrue($this->flapMapAssociativity($fa, $f, $g));
    }

    /**
     * @test
     */
    public function it_obeys_the_law_of_left_identity()
    {
        // Option
        $fa = Some(42);
        $a = 1;
        $f = function (int $x): Kind {
            return Some(($x + 2) % 2 == 0);
        };
        $this->assertTrue($this->leftIdentity($fa, $a, $f));

        // List
        $fa = ImmList(1, 2, 3);
        $a = 1;
        $f = function (int $x): Kind {
            return ImmList(($x + 2) % 2 == 0);
        };
        $this->assertTrue($this->leftIdentity($fa, $a, $f));

        // Set
        $fa = ImmSet(1, 2, 3);
        $a = 1;
        $f = function (int $x): Kind {
            return ImmSet(($x + 2) % 2 == 0);
        };
        $this->assertTrue($this->leftIdentity($fa, $a, $f));
    }

    /**
     * @test
     */
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
    }
}
