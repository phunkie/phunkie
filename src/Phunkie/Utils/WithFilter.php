<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\Utils;

class WithFilter
{
    private $filterable;
    private $filter;

    public function __construct(Traversable $filterable, callable $filter)
    {
        $this->filterable = $filterable;
        $this->filter = $filter;
    }

    public function map(callable $f)
    {
        return $this->filterable->filter($this->filter);
    }
}
