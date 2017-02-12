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
use Phunkie\Cats\Traverse;
use function Phunkie\Functions\show\showValue;
use function Phunkie\Functions\type\promote;
use Phunkie\Ops\ImmList\ImmListApplicativeOps;
use Phunkie\Ops\ImmList\ImmListEqOps;
use Phunkie\Ops\ImmList\ImmListFoldableOps;
use Phunkie\Ops\ImmList\ImmListMonadOps;
use Phunkie\Ops\ImmList\ImmListMonoidOps;
use Phunkie\Ops\ImmList\ImmListOps;
use Phunkie\Ops\ImmList\ImmListTraverseOps;
use Phunkie\Utils\Iterator;

class ImmList implements Kind, Applicative, Monad, Traverse, Foldable
{
    use Show;
    use ImmListOps,
        ImmListApplicativeOps,
        ImmListEqOps,
        ImmListMonadOps,
        ImmListFoldableOps,
        ImmListMonoidOps,
        ImmListTraverseOps;

    const kind = ImmList;
    private $values;
    final public function __construct() { switch (get_class($this)) {
        case NonEmptyList::class: $this->constructNonEmptyList(func_num_args(), func_get_args()); break;
        case Cons::class: $this->constructCons(func_num_args(), func_get_args()); break;
        case Nil::class: $this->constructNil(func_num_args()); break;
        case ImmList::class: $this->values = func_get_args(); break;
        default: throw $this->listIsSealed(); }
    }

    public function toString(): string
    {
        return "List(". implode(", ", $this->map(function($e) { return showValue($e); })->values) . ")";
    }

    public function toArray(): array { return $this->values; }

    public function iterator(): Iterator
    {
        $storage = new \SplObjectStorage();
        foreach ($this->toArray() as $k => $v) {
            $storage[promote($k)] = $v;
        }
        return new Iterator($storage);
    }

    private function constructNonEmptyList(int $argc, array $argv)
    {
        if ($argc == 0) {
            throw new \Error("not enough arguments for constructor Nel");
        }
        $this->values = $argv;
    }

    private function constructCons(int $argc, array $argv)
    {
        if ($argc != 2) {
            throw new \Error(($argc < 2 ? "not enough" : "too many") . " arguments for constructor List");
        }
        $head = $argv[0];
        $tail = $argv[1];
        if (!$tail instanceof ImmList) {
            throw new \TypeError("type mismatch 2nd argument List: expected List, found " .
                ((gettype($tail) == "object") ? get_class($tail) : gettype($tail)));
        }
        $this->values = array_merge([$head], $tail->toArray());
    }

    private function constructNil(int $argc)
    {
        if ($argc > 0) {
            throw new \Error("too many arguments for constructor Nil");
        }
        $this->values = [];
    }

    private function listIsSealed()
    {
        return new \TypeError("List cannot be extended outside namespace");
    }
}