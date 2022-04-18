<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

array_map(function ($file) {
    require_once $file;
}, glob(__DIR__ .'/*'));

const Option = 'Option';
const ImmList = 'ImmList';
const ImmSet = 'ImmSet';
const Function1 = 'Function1';
const _ = "Phunkie@Reserverd@Constant@_";
const None = 'Phunkie@Reserverd@Constant@None';
const Nil = 'Phunkie@Reserverd@Constant@Nil';
