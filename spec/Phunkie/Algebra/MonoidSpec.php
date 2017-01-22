<?php

namespace spec\Phunkie\Algebra;

use Eris\Generator\BooleanGenerator;
use Eris\Generator\IntegerGenerator as IntGen;
use Eris\Generator\SequenceGenerator;
use Eris\Generator\StringGenerator;
use Eris\TestTrait;
use Phunkie\Laws\MonoidLaws;
use Md\PropertyTesting\Generator\RandomKindGenerator;
use PhpSpec\ObjectBehavior;
use Eris\Generator\ElementsGenerator as ElementsGen;

class MonoidSpec extends ObjectBehavior
{
    use TestTrait, MonoidLaws, RandomKindGenerator;

    function it_obeys_the_law_of_combine_left_identity_with_integers()
    {
        $this->forAll(
            new IntGen()
        )->then(function($x) {
            expect($this->combineLeftIdentity($x))->toBe(true);
        });
    }

    function it_obeys_the_law_of_combine_right_identity_with_integers()
    {
        $this->forAll(
            new IntGen()
        )->then(function($x) {
            expect($this->combineRightIdentity($x))->toBe(true);
        });
    }

    function it_obeys_the_law_of_combine_left_identity_with_strings()
    {
        $this->forAll(
            new StringGenerator()
        )->then(function($x) {
            expect($this->combineLeftIdentity($x))->toBe(true);
        });
    }

    function it_obeys_the_law_of_combine_right_identity_with_strings()
    {
        $this->forAll(
            new StringGenerator()
        )->then(function($x) {
            expect($this->combineRightIdentity($x))->toBe(true);
        });
    }

    function it_obeys_the_law_of_combine_left_identity_with_arrays()
    {
        $this->forAll(
            new SequenceGenerator(new IntGen())
        )->then(function($x) {
            expect($this->combineLeftIdentity($x))->toBe(true);
        });
    }

    function it_obeys_the_law_of_combine_right_identity_with_arrays()
    {
        $this->forAll(
            new SequenceGenerator(new IntGen())
        )->then(function($x) {
            expect($this->combineRightIdentity($x))->toBe(true);
        });
    }

    function it_obeys_the_law_of_combine_left_identity_with_callables()
    {
        $this->forAll(
            ElementsGen::fromArray([function (int $x):string { return (string)$x; }])
        )->then(function($x) {
            expect($this->combineLeftIdentity($x))->toBe(true);
        });
    }

    function it_obeys_the_law_of_combine_right_identity_with_callables()
    {
        $this->forAll(
            ElementsGen::fromArray([function (int $x):string { return (string)$x; }])
        )->then(function($x) {
            expect($this->combineRightIdentity($x))->toBe(true);
        });
    }

    function it_obeys_the_law_of_combine_left_identity_with_booleans()
    {
        $this->forAll(
            new BooleanGenerator()
        )->then(function($x) {
            expect($this->combineLeftIdentity($x))->toBe(true);
        });
    }

    function it_obeys_the_law_of_combine_right_identity_with_booleans()
    {
        $this->forAll(
            new BooleanGenerator()
        )->then(function($x) {
            expect($this->combineRightIdentity($x))->toBe(true);
        });
    }

    function it_obeys_the_law_of_combine_left_identity_with_options()
    {
        $this->forAll(
            $this->genOption(new IntGen())
        )->then(function($x) {
            expect($this->combineLeftIdentity($x))->toBe(true);
        });
    }

    function it_obeys_the_law_of_combine_right_identity_with_options()
    {
        $this->forAll(
            $this->genOption(new IntGen())
        )->then(function($x) {
            expect($this->combineRightIdentity($x))->toBe(true);
        });
    }

    function it_obeys_the_law_of_combine_left_identity_with_lists()
    {
        $this->forAll(
            $this->genImmList(new IntGen())
        )->then(function($x) {
            expect($this->combineLeftIdentity($x))->toBe(true);
        });
    }

    function it_obeys_the_law_of_combine_right_identity_with_lists()
    {
        $this->forAll(
            $this->genImmList(new IntGen())
        )->then(function($x) {
            expect($this->combineRightIdentity($x))->toBe(true);
        });
    }

    function it_obeys_the_law_of_combine_left_identity_with_function1()
    {
        $this->forAll(
            $this->genFunction1()
        )->then(function($x) {
            expect($this->combineLeftIdentity($x))->toBe(true);
        });
    }

    function it_obeys_the_law_of_combine_right_identity_with_function1()
    {
        $this->forAll(
            $this->genFunction1()
        )->then(function($x) {
            expect($this->combineRightIdentity($x))->toBe(true);
        });
    }
}