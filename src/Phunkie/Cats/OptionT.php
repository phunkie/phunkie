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

use Phunkie\Types\Option;

/**
 * OptionT<F, A>
 */
class OptionT
{
    /**
     * @var Monad<Option<A>>
     */
    private $value;

    public function __construct(Monad $value)
    {
        $this->value = $value;
    }

    /**
     * @param callable<A, B> $f
     * @return OptionT<F, B>
     */
    public function map($f): OptionT
    {
        return OptionT($this->value->map(fn (Option $o) => $o->map($f)));
    }

    /**
     * @param callable<A, OptionT<F, B>> $f
     * @return OptionT<F, B>
     */
    public function flatMap($f): OptionT
    {
        return OptionT($this->value->flatMap(function (Option $o) use ($f) {
            return $o->map(
                fn ($a) => $f($a)->value
            )->getOrElse($this->value->pure(None()));
        }));
    }

    /**
     * @return F<Boolean>
     */
    public function isDefined()
    {
        return $this->value->map(fn (Option $o) => $o->isDefined());
    }

    /**
     * @return F<Boolean>
     */
    public function isEmpty()
    {
        return $this->value->map(fn (Option $o) => $o->isEmpty());
    }

    /**
     * @param A $default
     * @return F<A>
     */
    public function getOrElse($default)
    {
        return $this->value->map(fn (Option $o) => $o->getOrElse($default));
    }
}
