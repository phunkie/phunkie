<?php

use Md\Phunkie\Validation\Failure;
use Md\Phunkie\Validation\Success;

function Failure($e) {
    return new Failure($e);
}

function Success($a) {
    return new Success($a);
}