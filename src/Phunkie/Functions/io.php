<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\Functions\io;

use Phunkie\Cats\IO;

/**
 * Functions for working with IO operations.
 * 
 * This module provides functions for creating safe IO operations:
 * - CLI interaction (readline, args)
 * - Output (print, echo)
 * - File operations (read, write)
 * 
 * All functions return an IO that must be explicitly run.
 *
 * Example:
 * ```php
 * // Chain multiple IO operations
 * $program = readLine("Enter name: ")
 *     ->flatMap(fn($name) => 
 *         writeLine("Hello, $name!")
 *     );
 * 
 * // Run the program
 * unsafelyRun($program);
 * ```
 */

/**
 * Creates an IO from a function.
 * 
 * @template A
 * @param callable():A $f Function to wrap
 * @return IO<A> The IO operation
 */
const io = "\\Phunkie\\Functions\\io\\io";
function io(callable $f): IO
{
    return new class ($f) extends IO {
        private $f;
        public function __construct($f) { $this->f = $f; }
        public function run() { return call_user_func($this->f); }
    };
}

/**
 * Reads a line from stdin with prompt.
 * 
 * Example:
 * ```php
 * $name = readLine("Name: ");  // Shows prompt and returns IO<string>
 * ```
 *
 * @param string $prompt Text to display before reading
 * @return IO<string> The read operation
 */
function readLine(string $prompt = ""): IO
{
    return io(function() use ($prompt) {
        echo $prompt;
        return trim(fgets(STDIN));
    });
}

/**
 * Gets command line arguments.
 * 
 * Example:
 * ```php
 * $args = getArgs();  // Returns IO<array<string>>
 * ```
 *
 * @return IO<array<string>> The args operation
 */
function getArgs(): IO
{
    return io(fn() => array_slice($_SERVER['argv'], 1));
}

/**
 * Writes text to stdout.
 * 
 * Example:
 * ```php
 * $write = write("Hello ");  // Returns IO<void>
 * ```
 *
 * @param string $text Text to write
 * @return IO<void> The write operation
 */
function write(string $text): IO
{
    return io(fn() => print($text));
}

/**
 * Writes text with newline to stdout.
 * 
 * Example:
 * ```php
 * $writeln = writeLine("Hello!");  // Returns IO<void>
 * ```
 *
 * @param string $text Text to write
 * @return IO<void> The write operation
 */
function writeLine(string $text): IO
{
    return io(fn() => print($text . PHP_EOL));
}

/**
 * Reads entire file contents.
 * 
 * Example:
 * ```php
 * $content = readFile("file.txt");  // Returns IO<string>
 * ```
 *
 * @param string $path File path
 * @return IO<string> The read operation
 * @throws \RuntimeException If file cannot be read
 */
function readFile(string $path): IO
{
    return io(function() use ($path) {
        if (!is_readable($path)) {
            throw new \RuntimeException("Cannot read file: $path");
        }
        return file_get_contents($path);
    });
}

/**
 * Writes content to a file.
 * 
 * Example:
 * ```php
 * $write = writeFile("file.txt", "content");  // Returns IO<void>
 * ```
 *
 * @param string $path File path
 * @param string $content Content to write
 * @return IO<void> The write operation
 * @throws \RuntimeException If file cannot be written
 */
function writeFile(string $path, string $content): IO
{
    return io(function() use ($path, $content) {
        if (file_put_contents($path, $content) === false) {
            throw new \RuntimeException("Cannot write file: $path");
        }
    });
}

/**
 * Appends content to a file.
 * 
 * Example:
 * ```php
 * $append = appendFile("log.txt", "new line\n");  // Returns IO<void>
 * ```
 *
 * @param string $path File path
 * @param string $content Content to append
 * @return IO<void> The append operation
 * @throws \RuntimeException If file cannot be written
 */
function appendFile(string $path, string $content): IO
{
    return io(function() use ($path, $content) {
        if (file_put_contents($path, $content, FILE_APPEND) === false) {
            throw new \RuntimeException("Cannot append to file: $path");
        }
    });
}

/**
 * Executes an IO operation.
 * 
 * This is unsafe as it performs side effects. Use with caution.
 *
 * Example:
 * ```php
 * $io = readLine("Name: ")->flatMap(fn($name) => 
 *     writeLine("Hello, $name!")
 * );
 * 
 * unsafelyRun($io);  // Actually performs the IO
 * ```
 *
 * @template A
 * @param IO<A> $io The IO to run
 * @return A The result
 */
function unsafelyRun(IO $io)
{
    return $io->run();
}
