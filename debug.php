<?php

require_once "vendor/autoload.php";

$argv = ["bin/phpspec", "run", "spec/Phunkie/PatternMatchingSpec.php:109"];

$_SERVER['argv'] = $argv;

include_once "bin/phpspec";
