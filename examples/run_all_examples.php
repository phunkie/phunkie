<?php

require_once dirname(__DIR__) . "/vendor/autoload.php";
require_once __DIR__ . "/option.php";
require_once __DIR__ . "/list.php";
require_once __DIR__ . "/function1.php";
require_once __DIR__ . "/functor.php";
require_once __DIR__ . "/functor_composite.php";
require_once __DIR__ . "/applicative.php";
require_once __DIR__ . "/monad.php";

function printLn($value) { show($value); echo PHP_EOL; }

option_examples();
list_examples();
function1_examples();
functor_examples();
functor_composite_examples();
applicative_examples();
monad_examples();