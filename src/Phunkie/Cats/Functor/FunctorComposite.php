<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\Cats\Functor;

use Phunkie\Cats\Show;
use Phunkie\Ops\FunctorOps;
use Phunkie\Types\Function1;
use Phunkie\Types\ImmList;
use Phunkie\Types\Kind;
use Phunkie\Types\Option;

class FunctorComposite
{
    use Show;
    use FunctorOps;
    protected $kinds = [];

    public function __construct(string $kind)
    {
        switch ($kind) {
        case ImmList::kind: case Option::kind: case Function1::kind: $this->kinds[] = $kind; break;
        default: throw new \RuntimeException("Composing functor of kind $kind is not supported"); }
    }

    public function map(Kind $fa, callable $f)
    {
        $this->guardKindType($fa, $this->kinds[0]);
        return $fa->map($f);
    }

    public function imap(Kind $fa, callable $f, callable $g)
    {
        return $this->map($fa, $f);
    }

    public function compose(string $g): FunctorComposite
    {
        $functor = new class ($g, $this) extends FunctorComposite {
            use Show;
            private $fa;
            public function __construct(string $g, FunctorComposite $fa)
            {
                parent::__construct($g);
                $this->fa = $fa;
            }
            public function map(Kind $fga, callable $f)
            {
                return $this->fa->map($fga, function ($ga) use ($f) {
                    return $ga->map($f);
                });
            }
        };
        $functor->kinds = array_merge($this->kinds, $functor->kinds);
        return $functor;
    }

    public function toString(): string
    {
        $covertImmListToList = function ($kind) {
            return $kind == ImmList::kind ? 'List' : $kind;
        };
        $kinds = array_map($covertImmListToList, $this->kinds);
        return "Functor(" . implode("(", $kinds) . str_repeat(")", count($kinds));
    }

    private function guardKindType(Kind $fa, string $expectedType)
    {
        if ($fa::kind !== $expectedType) {
            throw new \TypeError("Argument 1 passed to map() must be of the type " . $expectedType . ", " . $fa::kind . " given");
        }
    }
}
