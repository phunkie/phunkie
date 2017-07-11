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

use Phunkie\Types\Option;
use Phunkie\Utils\WithFilter;

/**
 * @mixin \Phunkie\Types\Option
 */
trait OptionOps
{
    public function filter(callable $condition): Option
    {
        return $this->isEmpty() ? $this : ($condition($this->get()) ? $this : None());
    }

    public function withFilter(callable $filter): WithFilter
    {
        return new WithFilter($this, $filter);
    }
}