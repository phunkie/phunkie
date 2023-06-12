<?php

namespace Md\PropertyTesting\Generator;

use Eris\Generator;

use Phunkie\Types\Kind;

use Eris\Generator\IntegerGenerator as IntGen;
use Eris\Generator\ElementsGenerator as ElementsGen;
use Eris\Generator\SequenceGenerator as SequenceGen;
use Eris\Generator\OneOfGenerator as OneOfGen;
use Eris\Generator\MapGenerator as MapGen;

trait RandomKindGenerator
{
    private function genOption($gen): Generator
    {
        return new MapGen(fn ($x) => Option($x), $gen);
    }

    private function genImmList($gen): Generator
    {
        return new MapGen(
            fn ($sequence) => ImmList(...$sequence),
            new SequenceGen(new OneOfGen([$gen]))
        );
    }

    private function genImmSet($gen): Generator
    {
        return new MapGen(
            fn ($sequence) => ImmSet(...$sequence),
            new SequenceGen(new OneOfGen([$gen]))
        );
    }

    private function genFunction1()
    {
        return ElementsGen::fromArray([Function1(fn ($x): string => gettype($x))]);
    }

    private function genRandomFA(): Generator
    {
        return new OneOfGen([
            $this->genImmList(new OneOfGen([new IntGen()])),
            $this->genOption(new IntGen()),
            ElementsGen::fromArray([Function1(fn ($x): string => gettype($x))]),
            $this->genImmSet(new OneOfGen([new IntGen()]))
        ]);
    }

    private function genFunctionIntToString(): ElementsGen
    {
        return ElementsGen::fromArray([Function1(fn (int $x): string => (string)$x)]);
    }

    private function genFunctionStringToInt(): ElementsGen
    {
        return ElementsGen::fromArray([Function1(fn (string $x): int => strlen($x))]);
    }

    private function genFunctionStringToBool(): ElementsGen
    {
        return ElementsGen::fromArray([Function1(fn (string $x): bool => strlen($x) % 2 === 0)]);
    }

    private function genFunctionBoolToString(): ElementsGen
    {
        return ElementsGen::fromArray([Function1(fn (bool $x): string => $x ? 'true' : 'false')]);
    }

    private function genFunctionIntToFString(mixed $f): mixed
    {
        if (is_callable($f)) {
            return ElementsGen::fromArray([Function1(fn (int $x): Kind => \call_user_func_array($f, [(string)$x]))]);
        }
    }

    private function genFunctionStringToFInt(mixed $f): mixed
    {
        if (is_callable($f)) {
            return ElementsGen::fromArray([Function1(fn (string $x): Kind => \call_user_func_array($f, [strlen($x)]))]);
        }
    }
}
