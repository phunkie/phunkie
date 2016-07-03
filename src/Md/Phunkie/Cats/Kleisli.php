<?php

namespace Md\Phunkie\Cats;

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
        return kleisli(function($a) use ($f) {
            $g = call_user_func($this->run, $a);
            return $g->flatMap($f->run);
        });
    }

    public function __get($arg)
    {
        switch ($arg) {
            case 'run': return $this->run;
        }
        throw new \InvalidArgumentException("Invalid property $arg for Kleisli");
    }

    public function __set($name, $value)
    {
        throw new \TypeError("Kleisli are immutable");
    }
}