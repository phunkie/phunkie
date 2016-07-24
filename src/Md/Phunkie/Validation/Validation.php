<?php

namespace Md\Phunkie\Validation;

use TypeError;

abstract class Validation
{
    public function isRight(): bool
    {
        switch (true) {
            case $this instanceof Failure: return false;
            case $this instanceof Success: return true;
            default: throw new TypeError("Validation cannot be extended outside namespace");
        }
    }

    public function isLeft(): bool
    {
        switch (true) {
            case $this instanceof Success: return false;
            case $this instanceof Failure: return true;
            default: throw new TypeError("Validation cannot be extended outside namespace");
        }
    }
}