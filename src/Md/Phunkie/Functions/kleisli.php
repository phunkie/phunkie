<?php

use Md\Phunkie\Cats\Kleisli;

function kleisli(callable $run) {
    return new Kleisli($run);
}