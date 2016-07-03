<?php

namespace Md\Phunkie\Types;

use Md\Phunkie\Cats\Applicative;
use Md\Phunkie\Cats\Functor;
use Md\Phunkie\Cats\Show;
use Md\Phunkie\Ops\Function1\Function1ApplicativeOps;
use Md\Phunkie\Ops\Function1\Function1EqOps;

final class Function1 implements Kind, Applicative
{
    use Function1ApplicativeOps, Function1EqOps, Show;
    const kind = "Function1";
    private $reflection;
    private $f;

    public function __construct(callable $f) {
        if (method_exists($f, '__invoke')) {
            $this->reflection = new \ReflectionMethod($f, '__invoke');
            $this->f = $f;
        } else {
            $this->reflection = new \ReflectionFunction($f);
            $this->f = $f;
        }
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

    private function invokeFunctionOnArg($arg) {
        try {
            return $this->reflection->invoke($arg);
        } catch (\ReflectionException $e) {
            return call_user_func($this->f, $arg);
        }
    }

    function toString(): string
    {
        return sprintf("Function1(%s=>%s)",
            $this->reflection->getParameters()[0]->getType() ?: "?",
            $this->reflection->getReturnType() ?: "?");
    }
}