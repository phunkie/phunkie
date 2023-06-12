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
use RuntimeException;
use function Phunkie\Functions\semigroup\combine;

/**
 * @mixin \Phunkie\Types\Option
 */
trait OptionMonoidOps
{
    public function zero()
    {
        return None();
    }

    public function combine(Option $b): Option { return match (true) {
        $this->notAnOption() => throw new RuntimeException("Options ops imported to non-option"),
        $this->isEmpty() => $b,
        $b->isEmpty() => $this,
        default => Some(combine($this->get(), $b->get())) };
    }

    /**
     * @return bool
     */
    private function notAnOption(): bool
    {
        return !$this instanceof Option;
    }
}
