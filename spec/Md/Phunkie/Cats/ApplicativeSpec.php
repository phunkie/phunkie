<?php

namespace spec\Md\Phunkie\Cats;


use Md\Phunkie\Laws\ApplicativeLaws;
use Md\Phunkie\Types\Function1;
use Md\PropertyTesting\Generator\RandomKindGenerator;
use PhpSpec\ObjectBehavior;

use Eris\TestTrait;
use Eris\Generator\IntegerGenerator as IntGen;

class ApplicativeSpec extends ObjectBehavior
{
    use ApplicativeLaws,TestTrait,RandomKindGenerator;

     //applicativeIdentity(Kind $fa, Option $arg)
    function it_obeys_the_identity_law()
    {
        $this->forAll(
            $this->genRandomFA()
        )->then(function($fa) {
            expect($this->applicativeIdentity($fa))->toBe(true);
        });
    }

    // applicativeHomomorphism(Kind $fa, $a, callable $f)
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
        $a = function($x){ return $x; };
        $f = function($x){ return $x; };
        expect($this->applicativeHomomorphism($fa, $a, $f))->toBe(true);
    }

    // applicativeInterchange(Kind $fa, $a, Kind $ff)
    function it_obeys_the_interchange_law()
    {
        $this->forAll(
            $this->genRandomFA(),
            new IntGen(),
            $this->genFunctionIntToString(),
            $this->genFunctionStringToInt()
        )->then(function($fa, $a, $f, $g) {
            if ($fa instanceof Function1) {
                $a = $g->get();
            }
            expect($this->applicativeInterchange($fa, $a, $fa->pure($f->get())))->toBe(true);
        });
    }

    // applicativeMap(Kind $fa, callable $f)
    function it_obeys_the_map_law()
    {

    }
}