<?php

array_map (function($file) { require_once $file; }, glob(__DIR__ .'/*'));

const Option = 'Option';
const ImmList = 'ImmList';
const Function1 = 'Function1';