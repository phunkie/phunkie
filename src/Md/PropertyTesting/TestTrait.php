<?php

namespace Md\PropertyTesting;

use Eris\TestTrait as Test;

trait TestTrait
{
    public function forAll(...$args)
    {
        $test = new class() {
            use Test;
            public function run(...$args) {
                return $this->withRand('rand')->forAll(...$args);
            }
        };
        return $test->run(...$args);
    }
}