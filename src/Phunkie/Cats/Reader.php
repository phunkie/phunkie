<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\Cats;

use function Phunkie\Functions\semigroup\combine;

/**
 * Represents computations that read from a shared environment.
 * 
 * Reader<R,A> is a function from some configuration type R to a result type A.
 * It's useful for:
 * - Dependency injection
 * - Reading from configuration
 * - Composing computations that share environment
 *
 * Example:
 * ```php
 * class Config {
 *     public function __construct(
 *         public readonly string $host,
 *         public readonly int $port
 *     ) {}
 * }
 * 
 * // Create readers that depend on config
 * $getHost = Reader(fn(Config $c) => $c->host);
 * $getPort = Reader(fn(Config $c) => $c->port);
 * 
 * // Compose them
 * $getUrl = $getHost->map(
 *     fn($h) => "http://$h"
 * )->flatMap(fn($url) =>
 *     $getPort->map(fn($p) => "$url:$p")
 * );
 * 
 * // Run with config
 * $config = new Config("localhost", 8080);
 * $url = $getUrl->run($config); // "http://localhost:8080"
 * ```
 *
 * @template R The environment type
 * @template A The result type
 */
class Reader
{
    private $run;

    /**
     * Creates a new Reader from a function.
     *
     * @param callable(R):A $run The function that reads from the environment
     */
    public function __construct(callable $run)
    {
        $this->run = $run;
    }

    /**
     * Runs this reader with a given environment.
     *
     * @param R $r The environment to run with
     * @return A The result
     */
    public function run($r)
    {
        return call_user_func($this->run, $r);
    }

    /**
     * Maps a function over the result of this reader.
     *
     * @template B
     * @param callable(A):B $f The function to apply
     * @return Reader<R,B> A new reader with transformed result
     */
    public function map(callable $f): Reader
    {
        return Reader(combine($f, $this->run));
    }

    /**
     * Chains reader computations.
     *
     * @template B
     * @param callable(A):Reader<R,B> $f Function producing the next reader
     * @return Reader<R,B> The composed reader
     */
    public function flatMap(callable $f): Reader
    {
        return Reader(fn($r) => $f($this->run($r))->run($r));
    }

    /**
     * Composes this reader with another reader.
     * Runs this reader first, then the other.
     *
     * @param Reader<R,B> $that The reader to compose with
     * @return Reader<R,B> The composed reader
     */
    public function andThen(Reader $that): Reader
    {
        return Reader(combine($that->run, $this->run));
    }
}
