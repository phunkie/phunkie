<?php

use Md\Phunkie\Types\Function1;

function Function1(callable $f) { return new Function1($f); }