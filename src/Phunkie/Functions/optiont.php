<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Phunkie\Cats\OptionT;

/**
 * Functions for working with OptionT monad transformer.
 * 
 * OptionT combines Option with another monad (like IO or Either),
 * allowing you to work with optional values in the context of that monad.
 */

/**
 * Creates an OptionT monad transformer.
 * 
 * Wraps a monad containing an Option into OptionT.
 * Useful for composing Option with other monads.
 *
 * Example:
 * ```php
 * // Option inside IO
 * $readOptional = IO(fn() => isset($_GET['id']) ? Some($_GET['id']) : None());
 * $computation = OptionT($readOptional)->map(fn($id) => $id * 2);
 * 
 * // Option inside Either
 * $validated = Right(Some(42));
 * $result = OptionT($validated)->getOrElse(0);  // 42
 * 
 * // Empty cases
 * OptionT(Right(None()));     // No value
 * OptionT(Left("error"));     // Error in outer monad
 * ```
 *
 * @template M,A
 * @param M<Option<A>> $monad Monad containing Option
 * @return OptionT<M,A> Option transformer
 */
function OptionT($value)
{
    return new OptionT($value);
}
