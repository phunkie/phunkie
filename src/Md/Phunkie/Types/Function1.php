<?php

namespace Md\Phunkie\Types;

use Md\Phunkie\Cats\Show;
use Md\Phunkie\Ops\Function1\Function1ApplicativeOps;
use Md\Phunkie\Ops\Function1\Function1EqOps;

final class Function1 implements Kind
{
    use Function1ApplicativeOps, Function1EqOps, Show;
    const kind = "Function1";
    private $reflection;
    private $f;

    public function __construct(callable $f) {
        $this->reflection = new \ReflectionFunction($f);
        $this->f = $f;
    }

    public function get() { return $this->f; }

    public function __invoke($a) { return $this->invokeFunctionOnArg($a); }

    public function andThen(callable $g): Function1 {
        $f = $this;
        return Function1(function($x) use ($f, $g) { return $g($f->invokeFunctionOnArg($x)); });
    }

    public function compose(callable $g): Function1 {
        $f = $this;
        return Function1(function($x) use ($f, $g) {
            return $f->invokeFunctionOnArg(
                $g instanceof Function1 ? $g->invokeFunctionOnArg($x) : call_user_func($g, $x)
            );
        });
    }

    public static function identity(): Function1 { return Function1(function($x) { return $x; }); }

    private function invokeFunctionOnArg($arg) { return $this->reflection->invoke($arg); }

    function toString(): string
    {
        return sprintf("Function1(%s=>%s)",
            $this->reflection->getParameters()[0]->getType() ?: "?",
            $this->reflection->getReturnType() ?: "?");
    }
}