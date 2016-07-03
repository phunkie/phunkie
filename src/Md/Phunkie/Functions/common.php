<?php

use Md\Phunkie\Types\Kind;

array_map (function($file) { require_once $file; }, glob(__DIR__ .'/*'));

const Option = 'Option';
const ImmList = 'ImmList';
const Function1 = 'Function1';

function map(callable $f, Kind $kind) { return $kind->map($f); }