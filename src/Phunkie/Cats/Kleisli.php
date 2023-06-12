<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\Cats;

use function Phunkie\Functions\kleisli\kleisli as k;

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
        return k(fn ($a) => $this->run($a)->flatMap($f->run));
    }

    public function run($a)
    {
        return call_user_func($this->run, $a);
    }
}
