<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\Types;

use Phunkie\Cats\Applicative;
use Phunkie\Cats\Show;
use Phunkie\Ops\Function1\Function1ApplicativeOps;
use Phunkie\Ops\Function1\Function1EqOps;

use function Phunkie\Functions\type\normaliseType;

final class Function1 implements Kind, Applicative
{
    use Function1ApplicativeOps;
    use Function1EqOps;
    use Show;
    public const kind = "Function1";
    private $reflection;
    private $f;

    public function __construct(callable $f)
    {
        $this->f = $f;
        $this->reflection = method_exists($f, '__invoke') ?
                            new \ReflectionMethod($f, '__invoke') :
                            new \ReflectionFunction($f);
        $this->guardCallableNumberOfParameters();
    }

    public function get()
    {
        return $this->f;
    }

    public function __invoke($a)
    {
        return $this->invokeFunctionOnArg($a);
    }

    public function run($a)
    {
        return $this->invokeFunctionOnArg($a);
    }

    public function andThen(callable $g): Function1
    {
        $f = $this;
        return Function1(function ($x) use ($f, $g) {
            return $g($f->invokeFunctionOnArg($x));
        });
    }

    public function compose(callable $g): Function1
    {
        $f = $this;
        return Function1(function ($x) use ($f, $g) {
            return $f->invokeFunctionOnArg(
                $g instanceof Function1 ? $g->invokeFunctionOnArg($x) : call_user_func($g, $x)
            );
        });
    }

    public static function identity(): Function1
    {
        return Function1(function ($x) {
            return $x;
        });
    }

    public function zero()
    {
        return Function1::identity();
    }

    public function combine(callable $g)
    {
        return $this->compose($g);
    }

    public function toString(): string
    {
        return sprintf(
            "Function1(%s=>%s)",
            normaliseType($this->reflection->getParameters()[0]->getType()->getName()) ?: "?",
            normaliseType($this->reflection->getReturnType()->getName()) ?: "?"
        );
    }

    public function getTypeArity(): int
    {
        return 2;
    }

    public function getTypeVariables(): array
    {
        return [
            normaliseType($this->reflection->getParameters()[0]->getType()) ?: "?",
            normaliseType($this->reflection->getReturnType()) ?: "?"
        ];
    }

    public function showType()
    {
        return "Function1";
    }

    private function invokeFunctionOnArg($arg)
    {
        return call_user_func($this->f, $arg);
    }

    private function guardCallableNumberOfParameters()
    {
        $numberOfParameters = $this->reflection->getNumberOfParameters();
        if ($numberOfParameters != 1) {
            throw new \TypeError("Function1 takes a callable with 1 parameter. $numberOfParameters given.");
        }
    }
}
