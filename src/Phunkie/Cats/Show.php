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

use Phunkie\Types\Kind;

trait Show
{
    abstract public function toString(): string;

    public function show(): string
    {
        return $this->toString();
    }

    public function showType(): string
    {
        if ($this instanceof Kind) {
            return sprintf($this::kind . "<%s>", implode(", ", $this->getTypeVariables()));
        }
        return get_class($this);
    }
}
