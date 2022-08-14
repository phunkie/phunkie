<?php

namespace Phunkie\Ops\ImmSet;

use Phunkie\Types\ImmSet;
use Phunkie\Types\Kind;
use Phunkie\Types\None;
use Phunkie\Types\Option;

trait ImmSetMonadOps
{
    public function flatMap(callable $f): Kind
    {
        $b = [];
        foreach ($this->toArray() as $a) {
            $tmp = $f($a);
            $b = $this->createNewImmSetArray($f, $tmp, $b);
        }
        return \ImmSet(...$b);
    }

    public function flatten(): Kind
    {
        return $this->flatMap(fn ($x) => $x);
    }

    /**
     * @param mixed $f
     * @param $tmp
     * @param array $b
     * @return array
     */
    private function createNewImmSetArray(mixed $f, $tmp, array $b): array
    {
        switch (true) {
            case $f instanceof None:
            case $tmp instanceof None:
                break;
            case $tmp instanceof ImmSet:
                foreach ($tmp->toArray() as $value) {
                    $b[] = $value;
                }
                break;
            case $tmp instanceof Option:
                $b[] = $tmp->get();
                break;
            default:
                throw new \BadMethodCallException('Type mismatch');
        }
        return $b;
    }
}
