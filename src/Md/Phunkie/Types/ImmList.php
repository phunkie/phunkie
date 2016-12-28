<?php

namespace Md\Phunkie\Types;

use Md\Phunkie\Cats\Applicative;
use Md\Phunkie\Cats\Monad;
use Md\Phunkie\Cats\Show;
use function Md\Phunkie\Functions\show\get_value_to_show;
use function Md\Phunkie\Functions\type\promote;
use Md\Phunkie\Ops\ImmList\ImmListApplicativeOps;
use Md\Phunkie\Ops\ImmList\ImmListEqOps;
use Md\Phunkie\Ops\ImmList\ImmListFoldableOps;
use Md\Phunkie\Ops\ImmList\ImmListMonadOps;
use Md\Phunkie\Ops\ImmList\ImmListMonoidOps;
use Md\Phunkie\Ops\ImmList\ImmListOps;
use Md\Phunkie\Utils\Iterator;

abstract class ImmList implements Kind, Applicative, Monad
{
    use Show;
    use ImmListOps,
        ImmListApplicativeOps,
        ImmListEqOps,
        ImmListMonadOps,
        ImmListFoldableOps,
        ImmListMonoidOps;

    const kind = ImmList;
    private $values;
    final public function __construct() { switch (get_class($this)) {
        case NonEmptyList::class: $this->constructNonEmptyList(func_num_args(), func_get_args()); break;
        case Cons::class: $this->constructCons(func_num_args(), func_get_args()); break;
        case Nil::class: $this->constructNil(func_num_args()); break;
        default: throw $this->listIsSealed(); }
    }

    public function toString(): string
    {
        return "List(". implode(", ", $this->map(function($e) { return get_value_to_show($e); })->values) . ")";
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