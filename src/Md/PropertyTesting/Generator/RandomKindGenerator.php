<?php

namespace Md\PropertyTesting\Generator;

use Eris\Generator;
use Md\PropertyTesting\Generator\ImmListGenerator as ListGen;

use Md\Phunkie\Types\Kind;

use Eris\Generator\IntegerGenerator as IntGen;
use Eris\Generator\StringGenerator as StringGen;
use Eris\Generator\ElementsGenerator as ElementsGen;
use Eris\Generator\OneOfGenerator as OneOfGen;
use Eris\Generator\MapGenerator as MapGen;

trait RandomKindGenerator
{
    private function genRandomFA(): Generator
    {
        return new OneOfGen([
            new ListGen(new OneOfGen([new IntGen()])),
            new MapGen(function($x) { return Option($x); }, new IntGen()),
            ElementsGen::fromArray([Function1('gettype')])
        ]);
    }

    private function genFunctionIntToString(): ElementsGen
    {
        return ElementsGen::fromArray([Function1(function (int $x):string { return (string)$x; } )]);
    }

    private function genFunctionStringToInt(): ElementsGen
    {
        return ElementsGen::fromArray([Function1(function (string $x) { return strlen($x); })]);
    }

    private function genFunctionStringToBool(): ElementsGen
    {
        return ElementsGen::fromArray([Function1(function (string $x) { return strlen($x) % 2 === 0; })]);
    }

    private function genFunctionBoolToString(): ElementsGen
    {
        return ElementsGen::fromArray([Function1(function(bool $x) { return strlen($x) % 2 === 0; })]);
    }

    private function genFunctionIntToFString(mixed $f): mixed
    {
        if (is_callable($f)) {
            return ElementsGen::fromArray([Function1(function(int $x):Kind { return \call_user_func_array($f, [(string)$x]); } )]);
        }
    }

    private function genFunctionStringToFInt(mixed $f): mixed
    {
        if (is_callable($f)) {
            return ElementsGen::fromArray([Function1(function(string $x):Kind { return \call_user_func_array($f, [strlen($x)]); })]);
        }
    }
}
