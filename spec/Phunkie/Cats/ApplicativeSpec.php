<?php

namespace spec\Phunkie\Cats;

use Phunkie\Laws\ApplicativeLaws;
use Md\PropertyTesting\Generator\RandomKindGenerator;
use PHPUnit\Framework\TestCase;

use Eris\TestTrait;

class ApplicativeSpec extends TestCase
{
    use ApplicativeLaws;
    use TestTrait;
    use RandomKindGenerator;

    /**
     * @test
     */
    public function it_obeys_the_identity_law()
    {
        $this->forAll(
            $this->genRandomFA()
        )->then(function ($fa) {
            $this->assertTrue($this->applicativeIdentity($fa));
        });
    }

    /**
     * @test
     */
    public function it_obeys_the_homomorphism_law()
    {
        // Option
        $fa = Some(42);
        $a = 42;
        $f = fn ($x) => $x + 1;
        $this->assertTrue($this->applicativeHomomorphism($fa, $a, $f));

        // List
        $fa = ImmList(42);
        $a = 42;
        $f = fn ($x) => $x + 1;
        $this->assertTrue($this->applicativeHomomorphism($fa, $a, $f));

        // Function1
        $fa = Function1(fn ($x) => $x);
        $a = fn ($x) => $x + 42;
        $f = fn ($x) => $x;
        $this->assertTrue($this->applicativeHomomorphism($fa, $a, $f));

        // Set
        $fa = ImmSet(42);
        $a = 42;
        $f = fn ($x) => $x + 1;
        $this->assertTrue($this->applicativeHomomorphism($fa, $a, $f));
    }

    /**
     * @test
     */
    public function it_obeys_the_interchange_law()
    {
        // Option
        $fa = None();
        $a = 42;
        $f = fn ($x): string => gettype($x);
        $this->assertTrue($this->applicativeInterchange($fa, $a, Some($f)));

        // List
        $fa = ImmList();
        $this->assertTrue($this->applicativeInterchange($fa, $a, ImmList($f)));

        // Function1
        $fa = Function1($f);
        $fab = Function1(fn (string $s) => fn ($x) => $x);
        // @TODO Function1 applicative does not yet obey the interchange law
        // $this->assertTrue($this->applicativeInterchange($fa, $f, $fab));

        // Set
        $fa = ImmSet();
        $a = 42;
        $f = fn ($x): string => gettype($x);
        $this->assertTrue($this->applicativeInterchange($fa, $a, ImmSet($f)));
    }

    /**
     * @test
     */
    public function it_obeys_the_map_law()
    {
        // Option
        $fa = Some(42);
        $f = fn ($x) => $x + 1;
        $this->assertTrue($this->applicativeMap($fa, $f));

        // List
        $fa = ImmList(42);
        $f = fn ($x) => $x + 1;
        $this->assertTrue($this->applicativeMap($fa, $f));

        // Function1
        $fa = Function1(fn ($x) => $x);
        $f = fn ($x) => $x + 1;
        $this->assertTrue($this->applicativeMap($fa, $f));

        // Set
        $fa = ImmSet(42);
        $f = fn ($x) => $x + 1;
        $this->assertTrue($this->applicativeMap($fa, $f));
    }
}
