<?php

namespace spec\Md\Phunkie\Cats\Functor;

use Eris\TestTrait;
use Md\Phunkie\Laws\InvariantLaws;
use Md\Phunkie\Types\Function1;
use Md\PropertyTesting\Generator\RandomKindGenerator;
use PhpSpec\ObjectBehavior;

class InvariantSpec extends ObjectBehavior
{
    use InvariantLaws,RandomKindGenerator,TestTrait;

    function it_obeys_the_identity_law_of_invariance()
    {
        $this->forAll(
            $this->genRandomFA()
        )->then(function($fa) {
            $arg = $fa instanceof Function1 ? Some(42) : None();
            expect($this->invariantIdentity($fa, $arg))->toBe(true);
        });
    }

    function it_obeys_the_composition_law_of_invariance()
    {
        $this->forAll(
            $this->genRandomFA(),
            $this->genFunctionIntToString(),
            $this->genFunctionStringToInt(),
            $this->genFunctionStringToBool(),
            $this->genFunctionBoolToString()
        )->then(function($fa, $f1, $f2, $g1, $g2) {
            expect($this->invariantComposition($fa, $f1, $f2, $g1, $g2))->toBe(true);
        });
    }
}