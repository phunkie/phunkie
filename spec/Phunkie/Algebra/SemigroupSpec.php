<?php

namespace spec\Phunkie\Algebra;

use Eris\Generator\BooleanGenerator;
use Eris\Generator\IntegerGenerator as IntGen;
use Eris\Generator\SequenceGenerator;
use Eris\Generator\StringGenerator;
use Md\PropertyTesting\TestTrait;
use Phunkie\Laws\SemigroupLaws;
use Md\PropertyTesting\Generator\RandomKindGenerator;
use PHPUnit\Framework\TestCase;
use function Phunkie\Functions\semigroup\combine;
use function Phunkie\Functions\show\show;

error_reporting(E_ALL & ~E_DEPRECATED);

class SemigroupSpec extends TestCase
{
    use TestTrait;
    use SemigroupLaws;
    use RandomKindGenerator;

    /**
     * @test
     */
    public function it_obeys_the_law_of_combined_associativity_for_integers()
    {
        $this->forAll(
            new IntGen(),
            new IntGen(),
            new IntGen()
        )->then(function ($x, $y, $z) {
            $this->assertTrue($this->combineAssociativity($x, $y, $z));
        });
    }

    /**
     * @test
     */
    public function it_obeys_the_law_of_combined_associativity_for_strings()
    {
        $this->forAll(
            new StringGenerator(),
            new StringGenerator(),
            new StringGenerator()
        )->then(function ($x, $y, $z) {
            $this->assertTrue($this->combineAssociativity($x, $y, $z));
        });
    }

    /**
     * @test
     */
    public function it_obeys_the_law_of_combined_associativity_for_booleans()
    {
        $this->forAll(
            new BooleanGenerator(),
            new BooleanGenerator(),
            new BooleanGenerator()
        )->then(function ($x, $y, $z) {
            $this->assertTrue($this->combineAssociativity($x, $y, $z));
        });
    }

    /**
     * @test
     */
    public function it_obeys_the_law_of_combined_associativity_for_arrays()
    {
        $this->forAll(
            new SequenceGenerator(new IntGen()),
            new SequenceGenerator(new IntGen()),
            new SequenceGenerator(new IntGen())
        )->then(function ($x, $y, $z) {
            $this->assertTrue($this->combineAssociativity($x, $y, $z));
        });
    }

    /**
     * @test
     */
    public function it_obeys_the_law_of_combined_associativity_for_callables()
    {
        $this->forAll(
            $this->genFunctionIntToString(),
            $this->genFunctionStringToBool(),
            $this->genFunctionBoolToString()
        )->then(function ($x, $y, $z) {
            $this->assertTrue($this->combineAssociativity($x, $y, $z));
        });
    }

    /**
     * @test
     */
    public function it_obeys_the_law_of_combined_associativity_for_options()
    {
        $this->forAll(
            $this->genOption(new IntGen()),
            $this->genOption(new IntGen()),
            $this->genOption(new IntGen())
        )->then(function ($x, $y, $z) {
            $this->assertTrue($this->combineAssociativity($x, $y, $z));
        });
    }

    /**
     * @test
     */
    public function it_obeys_the_law_of_combined_associativity_for_lists()
    {
        $this->forAll(
            $this->genImmList(new IntGen()),
            $this->genImmList(new IntGen()),
            $this->genImmList(new IntGen())
        )->then(function ($x, $y, $z) {
            $this->assertTrue($this->combineAssociativity($x, $y, $z));
        });
    }

    /**
     * @test
     */
    public function it_obeys_the_law_of_combined_associativity_for_function1()
    {
        $this->forAll(
            $this->genFunction1(),
            $this->genFunction1(),
            $this->genFunction1()
        )->then(function ($x, $y, $z) {
            $this->assertTrue($this->combineAssociativity($x, $y, $z));
        });
    }

    /**
     * @test
     */
    public function it_combines_nels()
    {
        $nel1 = Nel(1, 2, 3);
        $nel2 = Nel(4, 5, 6);
        $this->assertEquals(Nel(1, 2, 3, 4, 5, 6), combine($nel1, $nel2));
    }

    /**
     * @test
     */
    public function it_combines_failures_with_nels()
    {
        $nel1 = Nel(1, 2, 3);
        $nel2 = Nel(4, 5, 6);
        $this->assertEquals(
            Failure(Nel(1, 2, 3, 4, 5, 6)),
            combine(Failure($nel1), Failure($nel2))
        );
    }

    /**
     * @test
     */
    public function it_combines_compositely()
    {
        $this->assertEquals(2, combine(1, 1));
        $this->assertEquals(3, combine(1, 1, 1));
        $this->assertEquals(4, combine(1, 1, 1, 1));
        $this->assertEquals(5, combine(1, 1, 1, 1, 1));
    }
}
