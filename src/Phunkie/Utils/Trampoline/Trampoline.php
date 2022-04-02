<?php

namespace Phunkie\Utils\Trampoline;

abstract class Trampoline
{
    public function run()
    {
        $result = $this->get();

        while ($result instanceof Trampoline) {
            $result = $result->get();
        }

        return $result;
    }

    abstract public function get();
}
