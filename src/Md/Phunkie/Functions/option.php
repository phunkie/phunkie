<?php

use Md\Phunkie\Types\None;
use Md\Phunkie\Types\Option;
use Md\Phunkie\Types\Some;

function Option(...$t):Option { return Some(...$t); }

function Some(...$t):Option { return Some::instance(...$t); }

function None():Option { return None::instance(); }
