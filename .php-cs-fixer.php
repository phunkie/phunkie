<?php

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = Finder::create()
  ->exclude(['vendor', 'cache', 'bin'])
  ->in(__DIR__);

$rules = [
    '@PHP81Migration' => true,
    'trailing_comma_in_multiline' => false,
    'use_arrow_functions' => true
];

$config = new Config();

return $config
  ->setRules($rules)
  ->setFinder($finder);
