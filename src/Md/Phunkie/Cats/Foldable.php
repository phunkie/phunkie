<?php

namespace Md\Phunkie\Cats;

interface Foldable
{
    public function foldLeft($initial, callable $f);
    public function foldRight($initial, callable $f);
    public function foldMap(callable $f);
    public function fold();
}