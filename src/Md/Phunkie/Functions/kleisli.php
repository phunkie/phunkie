<?php

namespace Md\Phunkie\Functions\kleisli;

use Md\Phunkie\Cats\Kleisli;

const kleisli = "\\Md\\Phunkie\\Functions\\kleisli\\kleisli";
function kleisli(callable $run)
{
    return new Kleisli($run);
}