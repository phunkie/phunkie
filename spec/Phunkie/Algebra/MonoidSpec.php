<?php

namespace spec\Phunkie\Algebra;

use Eris\Generator\BooleanGenerator;
use Eris\Generator\IntegerGenerator as IntGen;
use Eris\Generator\SequenceGenerator;
use Eris\Generator\StringGenerator;
use Md\PropertyTesting\TestTrait;
use Phunkie\Laws\MonoidLaws;
use Md\PropertyTesting\Generator\RandomKindGenerator;
use PHPUnit\Framework\TestCase;
use Eris\Generator\ElementsGenerator as ElementsGen;

error_reporting(E_ALL & ~E_DEPRECATED);

class MonoidSpec extends TestCase
{
    use TestTrait;
    use MonoidLaws;
    use RandomKindGenerator;

    /*function let()
    {
        $this->beAnInstanceOf(AMonoid::class);
    }*/

    /**
     * @test
     */
    public function it_obeys_the_law_of_combine_left_identity_with_integers()
    {
        $this->forAll(
            new IntGen()
        )->then(function ($x) {
            // $this->assertTrue($this->combineLeftIdentity($x));
            $this->assertTrue($this->combineLeftIdentity($x));
        });
    }

    /**
     * @test
     */
    public function it_obeys_the_law_of_combine_right_identity_with_integers()
    {
        $this->forAll(
            new IntGen()
        )->then(function ($x) {
            $this->assertTrue($this->combineRightIdentity($x));
            // $this->assertTrue($this->combineRightIdentity($x));
        });
    }

    /**
     * @test
     */
    public function it_obeys_the_law_of_combine_left_identity_with_strings()
    {
        $this->forAll(
            new StringGenerator()
        )->then(function ($x) {
            $this->assertTrue($this->combineLeftIdentity($x));
            // $this->assertTrue($this->combineLeftIdentity($x));
        });
    }

    /**
     * @test
     */
    public function it_obeys_the_law_of_combine_right_identity_with_strings()
    {
        $this->forAll(
            new StringGenerator()
        )->then(function ($x) {
            $this->assertTrue($this->combineRightIdentity($x));
            // $this->assertTrue($this->combineRightIdentity($x));
        });
    }

    /**
     * @test
     */
    public function it_obeys_the_law_of_combine_left_identity_with_arrays()
    {
        $this->forAll(
            new SequenceGenerator(new IntGen())
        )->then(function ($x) {
            $this->assertTrue($this->combineLeftIdentity($x));
            // $this->assertTrue($this->combineLeftIdentity($x));
        });
    }

    /**
     * @test
     */
    public function it_obeys_the_law_of_combine_right_identity_with_arrays()
    {
        $this->forAll(
            new SequenceGenerator(new IntGen())
        )->then(function ($x) {
            $this->assertTrue($this->combineRightIdentity($x));
        });
    }

    /**
     * @test
     */
    public function it_obeys_the_law_of_combine_left_identity_with_callables()
    {
        $this->forAll(
            ElementsGen::fromArray([fn (int $x): string => (string)$x])
        )->then(function ($x) {
            $this->assertTrue($this->combineLeftIdentity($x));
        });
    }

    /**
     * @test
     */
    public function it_obeys_the_law_of_combine_right_identity_with_callables()
    {
        $this->forAll(
            ElementsGen::fromArray([fn (int $x): string => (string)$x])
        )->then(function ($x) {
            $this->assertTrue($this->combineRightIdentity($x));
        });
    }

    /**
     * @test
     */
    public function it_obeys_the_law_of_combine_left_identity_with_booleans()
    {
        $this->forAll(
            new BooleanGenerator()
        )->then(function ($x) {
            $this->assertTrue($this->combineLeftIdentity($x));
        });
    }

    /**
     * @test
     */
    public function it_obeys_the_law_of_combine_right_identity_with_booleans()
    {
        $this->forAll(
            new BooleanGenerator()
        )->then(function ($x) {
            $this->assertTrue($this->combineRightIdentity($x));
        });
    }

    /**
     * @test
     */
    public function it_obeys_the_law_of_combine_left_identity_with_options()
    {
        $this->forAll(
            $this->genOption(new IntGen())
        )->then(function ($x) {
            $this->assertTrue($this->combineLeftIdentity($x));
        });
    }

    /**
     * @test
     */
    public function it_obeys_the_law_of_combine_right_identity_with_options()
    {
        $this->forAll(
            $this->genOption(new IntGen())
        )->then(function ($x) {
            $this->assertTrue($this->combineRightIdentity($x));
        });
    }

    /**
     * @test
     */
    public function it_obeys_the_law_of_combine_left_identity_with_lists()
    {
        $this->forAll(
            $this->genImmList(new IntGen())
        )->then(function ($x) {
            $this->assertTrue($this->combineLeftIdentity($x));
        });
    }

    /**
     * @test
     */
    public function it_obeys_the_law_of_combine_right_identity_with_lists()
    {
        $this->forAll(
            $this->genImmList(new IntGen())
        )->then(function ($x) {
            $this->assertTrue($this->combineRightIdentity($x));
        });
    }

    /**
     * @test
     */
    public function it_obeys_the_law_of_combine_left_identity_with_function1()
    {
        $this->forAll(
            $this->genFunction1()
        )->then(function ($x) {
            $this->assertTrue($this->combineLeftIdentity($x));
        });
    }

    /**
     * @test
     */
    public function it_obeys_the_law_of_combine_right_identity_with_function1()
    {
        $this->forAll(
            $this->genFunction1()
        )->then(function ($x) {
            $this->assertTrue($this->combineRightIdentity($x));
        });
    }
}

class AMonoid implements \Phunkie\Algebra\Monoid
{
    public function zero()
    {
    }
    public function combine($one, $another)
    {
    }
}
