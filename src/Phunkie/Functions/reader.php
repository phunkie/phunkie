<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace {
    use Phunkie\Cats\Reader;

    function Reader(callable $run)
    {
        return new Reader($run);
    }
}

namespace Phunkie\Functions\reader {

    use const Phunkie\Functions\function1\identity;
    use Phunkie\Cats\Reader;

    const ask = "Md\\Phunkie\\Functions\\reader\\ask";

    /**
     * Gets the environment from a Reader.
     * 
     * Creates a Reader that returns its environment unchanged.
     * Useful for accessing the environment in computations.
     *
     * Example:
     * ```php
     * // Get entire environment
     * $getEnv = ask();
     * $env = $getEnv->run(['key' => 'value']);  // ['key' => 'value']
     * 
     * // Use with map to access parts
     * $getKey = ask()->map(fn($env) => $env['key']);
     * $value = $getKey->run(['key' => 'value']); // "value"
     * ```
     *
     * @template R
     * @return Reader<R,R> Reader that returns environment
     */
    function ask(): Reader
    {
        return Reader(identity);
    }

    const mapReader = "\\Phunkie\\Functions\\reader\\mapReader";
    /**
     * Maps a function over a Reader's result.
     * 
     * Creates a new Reader that transforms the result of the original
     * using the provided function.
     *
     * Example:
     * ```php
     * $getName = Reader(fn($config) => $config['name']);
     * $getLength = mapReader(fn($name) => strlen($name))($getName);
     * 
     * $length = $getLength->run(['name' => 'test']); // 4
     * ```
     *
     * @template R,A,B
     * @param callable(A):B $f Function to apply to result
     * @return callable(Reader<R,A>):Reader<R,B> Function expecting Reader
     */
    function mapReader(callable $f): callable
    {
        return fn(Reader $reader) => Reader(fn($r) => $f($reader->run($r)));
    }

    const apReader = "\\Phunkie\\Functions\\reader\\apReader";
    /**
     * Applies a Reader of a function to a Reader of a value.
     * 
     * Combines a Reader containing a function with a Reader containing
     * a value to produce a new Reader with the function applied.
     *
     * Example:
     * ```php
     * $getPrefix = Reader(fn($env) => $env['prefix']);
     * $getName = Reader(fn($env) => $env['name']);
     * 
     * $prefixFn = $getPrefix->map(fn($p) => fn($n) => $p . $n);
     * $fullName = apReader($prefixFn)($getName);
     * 
     * $result = $fullName->run([
     *     'prefix' => 'Mr. ',
     *     'name' => 'Smith'
     * ]); // "Mr. Smith"
     * ```
     *
     * @template R,A,B
     * @param Reader<R,callable(A):B> $rf Reader of function
     * @return callable(Reader<R,A>):Reader<R,B> Function expecting Reader of value
     */
    function apReader(Reader $rf): callable
    {
        return fn(Reader $ra) => Reader(fn($r) => ($rf->run($r))($ra->run($r)));
    }
}
