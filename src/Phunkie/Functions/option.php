<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Phunkie\Types\None;
use Phunkie\Types\Option;
use Phunkie\Types\Some;

function Option($t) { return $t === null ? None() : Some($t); }

function Some(...$t):Option { return Some::instance(...$t); }

function None():Option { return None::instance(); }
