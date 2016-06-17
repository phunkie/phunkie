<?php

namespace Md\Phunkie\Types;

use Md\Phunkie\Std\Function1\Function1EqOps;
use Md\Phunkie\Std\Function1\Function1FunctorOps;

class Function1 implements Kind
{
    use Function1FunctorOps, Function1EqOps;
    private $reflection;

    public function __construct(callable $f) {
        $this->reflection = new \ReflectionFunction($f);
    }

    public function __invoke($a) {
        return $this->invokeFunctionOnArg($a);
    }

    public function andThen(callable $g): Function1
    {
        $f = $this;
        return Function1(function($x) use ($f, $g) { return $g($f->invokeFunctionOnArg($x)); });
    }

    public function compose(callable $g): Function1
    {
        $f = $this;
        return Function1(function($x) use ($f, $g) { return $f->invokeFunctionOnArg($g->invokeFunctionOnArg($x)); });
    }

    public static function identity(): Function1
    {
        return Function1(function($x) { return $x; });
    }

    private function invokeFunctionOnArg($arg)
    {
        return $this->reflection->invoke($arg);
    }
}