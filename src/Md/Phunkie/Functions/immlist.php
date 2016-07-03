<?php

use Md\Phunkie\Types\ImmList;

function ImmList(...$values): ImmList { return new ImmList(...$values); }

function head(ImmList $list) { return $list->head(); }
function init(ImmList $list) { return $list->init(); }
function tail(ImmList $list) { return $list->tail(); }
function last(ImmList $list) { return $list->last(); }
function reverse(ImmList $list) { return $list->reverse(); }
function length(ImmList $list) { return $list->length; }