<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\Ops\Option;

use BadMethodCallException;
use Phunkie\Cats\Applicative;
use Phunkie\Types\Kind;
use Phunkie\Types\Option;
use Phunkie\Types\None;
use Phunkie\Types\Some;

/**
 * @mixin Option
 */
trait OptionApplicativeOps
{
    use OptionFunctorOps;
    public function pure($a): Applicative
    {
        return Option($a);
    }
    /**
     * @param Option<Closure<A, B>> $ff
     * @return Option<B>
     * @throws BadMethodCallException
     */
    public function apply(Kind $ff): Kind { return match (true) {
        $this->isEmpty() => None(),
        $ff instanceof None => None(),
        $ff instanceof Some && is_callable($ff->get()) => $this->map($ff->get()),
        default => throw new BadMethodCallException()};
    }

    public function map2(Kind $fb, callable $f): Kind
    {
        return $this->apply($fb->map(fn ($b) => fn ($a) => $f($a, $b)));
    }
}
