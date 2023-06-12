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

use function Phunkie\Functions\io\io;

abstract class IO
{
    abstract public function run();

    public function andThen(IO $g)
    {
        return io(function () use ($g) {
            $this->run();
            $g->run();
        });
    }
    public function map($f)
    {
        return io(fn () => $f($this->run()));
    }

    public function flatMap($f)
    {
        return $f($this->run());
    }
}
