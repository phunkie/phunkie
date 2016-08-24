<?php

namespace Md\Phunkie\Cats;

use function Md\Phunkie\Functions\semigroup\combine;

/*
 * Reader<A, B>
 */
class Reader
{
    private $run;

    /**
     * @param callable<A, B> $run
     */
    public function __construct(callable $run)
    {
        $this->run = $run;
    }

    public function run($a)
    {
        return call_user_func($this->run, $a);
    }

    /**
     * @param callable<B, C> $f
     * @return Reader<A, C>
     */
    public function map(callable $f)
    {
        return Reader(combine($f, $this->run));
    }

    /**
     * @param callable<B, Reader<A, C>> $f
     * @return Reader<A, C>
     */
    public function flatMap($f)
    {
        return Reader(function($a) use ($f) { return $f($this->run($a))->run($a); });
    }

    /**
     * @param Reader $that
     * @return Reader
     * @throws \TypeError
     */
    public function andThen(Reader $that)
    {
        return Reader(combine($that->run, $this->run));
    }
}