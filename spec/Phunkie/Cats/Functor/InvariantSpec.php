<?php

namespace spec\Phunkie\Cats\Functor;

use Md\PropertyTesting\TestTrait;
use Phunkie\Laws\InvariantLaws;
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
            expect($this->invariantIdentity($fa))->toBe(true);
        });
    }

    function it_obeys_the_composition_law_of_invariance()
    {
        $this->forAll(
            $this->genRandomFA(),
            $this->genFunctionStringToInt(),
            $this->genFunctionStringToInt(),
            $this->genFunctionStringToBool(),
            $this->genFunctionBoolToString()
        )->then(function($fa, $f1, $f2, $g1, $g2) {
            expect($this->invariantComposition($fa, $f1, $f2, $g1, $g2))->toBe(true);
        });
    }
}