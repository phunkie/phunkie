<?php

namespace spec\Phunkie\Cats;

use Md\PropertyTesting\TestTrait;
use Phunkie\Laws\FunctorLaws;
use Phunkie\Types\Function1;
use Md\PropertyTesting\Generator\RandomKindGenerator;
use PHPUnit\Framework\TestCase;

class FunctorSpec extends TestCase
{
    use FunctorLaws;
    use TestTrait;
    use RandomKindGenerator;

    /**
     * @test
     */
    public function it_obeys_the_identity_law_of_covariance()
    {
        $this->forAll(
            $this->genRandomFA()
        )->then(function ($fa) {
            $arg = $fa instanceof Function1 ? Some(42) : None();
            $this->assertTrue($this->covariantIdentity($fa, $arg));
        });
    }

    /**
     * @test
     */
    public function it_obeys_the_composition_law_of_covariance()
    {
        $this->forAll(
            $this->genRandomFA(),
            $this->genFunctionStringToInt(),
            $this->genFunctionStringToInt()
        )->then(function ($fa, $f, $g) {
            $this->assertTrue($this->covariantComposition($fa, $f, $g));
        });
    }
}
