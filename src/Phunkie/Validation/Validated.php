<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\Validation;

/**
 * Trait for types that can be wrapped in a Success validation.
 * 
 * This trait provides a convenient way to convert any value into
 * a successful validation result. It's typically used by types
 * that represent valid data that might need validation context.
 *
 * Example:
 * ```php
 * class ValidData {
 *     use Validated;
 *     private $value;
 *     
 *     public function validate() {
 *         return $this->success(); // Returns Success(this)
 *     }
 * }
 * ```
 *
 * @template A
 */
trait Validated
{
    /**
     * Wraps this value in a Success validation.
     *
     * @return Success<never,A> A Success containing this value
     */
    public function success()
    {
        return new Success($this);
    }
}
