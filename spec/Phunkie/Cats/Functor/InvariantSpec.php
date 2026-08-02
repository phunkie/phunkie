<?php

namespace spec\Phunkie\Cats\Functor;

use Md\PropertyTesting\TestTrait;
use Phunkie\Laws\InvariantLaws;
use Md\PropertyTesting\Generator\RandomKindGenerator;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

class InvariantSpec extends TestCase
{
    use InvariantLaws;
    use RandomKindGenerator;
    use TestTrait;

    #[Test]
    public function it_obeys_the_identity_law_of_invariance()
    {
        $this->forAll(
            $this->genRandomFA()
        )->then(function ($fa) {
            $this->assertTrue($this->invariantIdentity($fa));
        });
    }

    #[Test]
    public function it_obeys_the_composition_law_of_invariance()
    {
        $this->forAll(
            $this->genRandomFA(),
            $this->genFunctionStringToInt(),
            $this->genFunctionStringToInt(),
            $this->genFunctionStringToBool(),
            $this->genFunctionBoolToString()
        )->then(function ($fa, $f1, $f2, $g1, $g2) {
            $this->assertTrue($this->invariantComposition($fa, $f1, $f2, $g1, $g2));
        });
    }
}
