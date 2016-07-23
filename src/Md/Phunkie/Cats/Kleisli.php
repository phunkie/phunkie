<?php

namespace Md\Phunkie\Cats;

use function Md\Phunkie\Functions\kleisli\kleisli as k;
use function Md\Phunkie\Functions\pattern_matching\matching;
use function Md\Phunkie\Functions\pattern_matching\on;

/**
 * Represents a Function1 `A => F[B]`.
 */
class Kleisli
{
    private $run;

    public function __construct(callable $run)
    {
        $this->run = $run;
    }

    public function andThen(Kleisli $f): Kleisli
    {
        return k(function($a) use ($f) {
            $g = call_user_func($this->run, $a);
            return $g->flatMap($f->run);
        });
    }

    public function __get($arg)
    {
        return matching($arg,
            on("run")->returns($this->run),
            on(_)->throws(new \InvalidArgumentException("Invalid property $arg for Kleisli"))
        );
    }

    public function __set($name, $value)
    {
        throw new \TypeError("Kleisli are immutable");
    }
}