<?php

namespace Phunkie\Ops\ImmList;

use Phunkie\Types\Kind;
use function Phunkie\Functions\show\showArrayType;
use const Phunkie\Functions\option\fromSome;
use const Phunkie\Functions\option\isDefined;

/**
 * @mixin \Phunkie\Types\ImmList
 */
trait ImmListTraverseOps
{
    public function traverse(callable $f): Kind
    {
        return $this->map($f)->sequence();
    }

    public function sequence(): Kind
    {
        $typeConstructor = $this->guardIsListOfTypeConstructor();

        $sequence = $this->takeWhile(isDefined)->length == $this->length ?
            $typeConstructor($this->map(fromSome)) :
            None();

        if ($sequence instanceof Kind) {
            return $sequence;
        }

        throw new \TypeError("$typeConstructor is not a type constructor");
    }

    private function guardIsListOfTypeConstructor(): string
    {
        $listType = showArrayType($this->toArray());
        $typeConstructor = substr($listType, 0, strpos($listType, "<"));
        if ($typeConstructor == "") {
            throw new \TypeError("Cannot find a type constructor in elements of list type $listType");
        }
        if (!is_callable($typeConstructor)) {
            throw new \TypeError("$typeConstructor is not a callable type constructor");
        }
        return $typeConstructor;
    }
}
