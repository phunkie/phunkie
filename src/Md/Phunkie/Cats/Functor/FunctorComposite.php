<?php

namespace Md\Phunkie\Cats\Functor;

use Md\Phunkie\Cats\Functor;
use Md\Phunkie\Cats\Show;
use Md\Phunkie\Ops\FunctorOps;
use Md\Phunkie\Types\Function1;
use Md\Phunkie\Types\ImmList;
use Md\Phunkie\Types\Kind;
use Md\Phunkie\Types\Option;

class FunctorComposite
{
    use Show,FunctorOps;
    protected $kinds = [];

    public function __construct(string $kind)
    {
        switch ($kind) {
            case ImmList::kind:
            case Option::kind:
            case Function1::kind:
                $this->kinds[] = $kind;
                break;
            default:
                throw new \RuntimeException("Composing functor of kind $kind is not supported");
        }
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
        $functor = new class($g, $this) extends FunctorComposite {
            use Show;
            private $fa;
            public function __construct(string $g, FunctorComposite $fa) {
                parent::__construct($g);
                $this->fa = $fa;
            }
            public function map(Kind $fga, callable $f) {
                return $this->fa->map($fga, function($ga) use ($f) { return $ga->map($f); });
            }
        };
        $functor->kinds = array_merge($this->kinds, $functor->kinds);
        return $functor;
    }

    function toString(): string
    {
        $covertImmListToList = function($kind) { return $kind == ImmList::kind ? 'List' : $kind; };
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