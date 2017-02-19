<?php

namespace Phunkie\Utils\Trampoline;

abstract class Trampoline
{
    public function run()
    {
        $result = $this->get();

        while ($result instanceof More) {
            $result = $result->get();
        }

        return $result->get();
    }

    abstract public function get();
}