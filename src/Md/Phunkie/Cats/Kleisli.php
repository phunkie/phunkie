<?php

namespace Md\Phunkie\Cats;

use function Md\Phunkie\Functions\kleisli\kleisli as k;

/**
 * Kleisli<F, A, B> (equivalent to ReaderT)
 * represents a function A => F<B>
 */
class Kleisli
{
    private $run;

    /**
     * @param callable<A, F<B>> $run
     */
    public function __construct(callable $run)
    {
        $this->run = $run;
    }

    /**
     * @param Kleisli<F, B, C> $f
     * @return Kleisli<F, A, C>
     */
    public function andThen(Kleisli $f): Kleisli
    {
        return k(function($a) use ($f) {
            return $this->run($a)->flatMap($f->run);
        });
    }

    public function run($a)
    {
        return call_user_func($this->run, $a);
    }
}