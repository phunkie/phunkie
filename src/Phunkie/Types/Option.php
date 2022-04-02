<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\Types;

use Phunkie\Cats\Applicative;
use Phunkie\Cats\Foldable;
use Phunkie\Cats\Monad;
use Phunkie\Cats\Show;
use Phunkie\Ops\Option\OptionApplicativeOps;
use Phunkie\Ops\Option\OptionEqOps;
use Phunkie\Ops\Option\OptionFoldableOps;
use Phunkie\Ops\Option\OptionMonadOps;
use Phunkie\Ops\Option\OptionMonoidOps;
use Phunkie\Ops\Option\OptionOps;
use Phunkie\Utils\Traversable;
use function Phunkie\Functions\show\showType;
use function Phunkie\Functions\show\showValue;

abstract class Option implements Kind, Applicative, Monad, Foldable, Traversable
{
    use Show;
    use OptionApplicativeOps;
    use OptionOps;
    use OptionEqOps;
    use OptionMonadOps;
    use OptionFoldableOps;
    use OptionMonoidOps;
    public const kind = "Option";
    protected $t;
    abstract public function getOrElse($t);
    abstract public function isDefined(): bool;
    abstract public function isEmpty(): bool;

    final protected function __construct($t = null)
    {
        if ($this instanceof Some) {
            $this->t = $t;
        } elseif (!$this instanceof None) {
            throw new \Error("Option can only be Some or None");
        }
    }

    public function toString(): string
    {
        return $this->isEmpty() ? "None" : "Some(". showValue($this->get()) . ")";
    }

    public function getTypeArity(): int
    {
        return $this->isEmpty() ? 0 : 1;
    }

    public function getTypeVariables(): array
    {
        return $this->isEmpty() ? [] : [showType($this->get())];
    }
}
