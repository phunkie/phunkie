<?php


namespace Phunkie\Ops\ImmList;

use function Phunkie\Functions\show\showArrayType;
use Phunkie\Types\ImmList\NoSuchElementException;
use Phunkie\Types\Kind;

trait ImmListTraverseOps
{
    public function traverse(callable $f): Kind
    {
        return $this->map($f)->sequence();
    }

    public function sequence(): Kind
    {
        $listType = showArrayType($this->toArray());
        $typeConstructor = substr($listType, 0, strpos($listType, "<"));
        if ($typeConstructor == "") {
            throw new \TypeError("Cannot find a type constructor in elements of list type $listType");
        }

        try {
            $sequence = $typeConstructor($this->map(function($e) {
            if ($e == None()) {
                throw new NoSuchElementException;
            }
            return $e->get();
        })); } catch (NoSuchElementException $e) {
            return None();
        }

        if (is_callable($typeConstructor) && $sequence instanceof Kind) {
            return $sequence;
        }
        throw new \TypeError("$typeConstructor is not a type constructor");
    }
}