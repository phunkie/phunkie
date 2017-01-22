<?php

namespace spec\Phunkie\Cats;

use Phunkie\Laws\ApplicativeLaws;
use Md\PropertyTesting\Generator\RandomKindGenerator;
use PhpSpec\ObjectBehavior;

use Eris\TestTrait;

class ApplicativeSpec extends ObjectBehavior
{
    use ApplicativeLaws,TestTrait,RandomKindGenerator;

    function it_obeys_the_identity_law()
    {
        $this->forAll(
            $this->genRandomFA()
        )->then(function($fa) {
            expect($this->applicativeIdentity($fa))->toBe(true);
        });
    }

    function it_obeys_the_homomorphism_law()
    {
        // Option
        $fa = Some(42);
        $a = 42;
        $f = function($x){ return $x + 1; };
        expect($this->applicativeHomomorphism($fa, $a, $f))->toBe(true);

        // List
        $fa = ImmList(42);
        $a = 42;
        $f = function($x){ return $x + 1; };
        expect($this->applicativeHomomorphism($fa, $a, $f))->toBe(true);

        // Function1
        $fa = Function1(function($x){ return $x; });
        $a = function($x) { return $x + 42; };
        $f = function($x){ return $x; };
        expect($this->applicativeHomomorphism($fa, $a, $f))->toBe(true);
    }

    function it_obeys_the_interchange_law()
    {
        // Option
        $fa = None();
        $a = 42;
        $f = function($x):string { return gettype($x); };
        expect($this->applicativeInterchange($fa, $a, Some($f)))->toBe(true);

        // List
        $fa = ImmList();
        expect($this->applicativeInterchange($fa, $a, ImmList($f)))->toBe(true);

        // Function1
        $fa = Function1($f);
        $fab = Function1(function(string $s) { return function($x) { return $x;}; });
        // @TODO Function1 applicative does not yet obey the interchange law
        // expect($this->applicativeInterchange($fa, $f, $fab))->toBe(true);
    }

    function it_obeys_the_map_law()
    {
        // Option
        $fa = Some(42);
        $f = function($x){ return $x + 1; };
        expect($this->applicativeMap($fa, $f))->toBe(true);

        // List
        $fa = ImmList(42);
        $f = function($x){ return $x + 1; };
        expect($this->applicativeMap($fa, $f))->toBe(true);

        // Function1
        $fa = Function1(function($x){ return $x; });
        $f = function($x){ return $x + 1; };
        expect($this->applicativeMap($fa, $f))->toBe(true);
    }
}