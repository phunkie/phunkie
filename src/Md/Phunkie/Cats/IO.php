<?php

namespace Md\Phunkie\Cats;

use function Md\Phunkie\Functions\io\io;

abstract class IO
{
    abstract public function run();

    public function andThen(IO $g) {
        return io(function() use ($g) {$this->run();$g->run();});
    }
    public function map($f){
        return io(function() use ($f) { return $f($this->run());});
    }

    public function flatMap($f) { return $f($this->run()); }
}