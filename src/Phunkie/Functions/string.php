<?php

namespace Phunkie\Functions\string {

    use Phunkie\Types\ImmList;

    /**
     * Functions for working with strings.
     * 
     * This module provides utility functions for splitting and joining
     * strings in a functional style using immutable lists.
     */

    const lines = "\\Phunkie\\Functions\\string\\lines";
    /**
     * Splits a string into lines.
     * 
     * Breaks a string at line boundaries into an immutable list.
     * Uses system-specific line endings (PHP_EOL).
     *
     * Example:
     * ```php
     * $text = "hello\nworld";
     * lines($text);  // ImmList("hello", "world")
     * 
     * $empty = "";
     * lines($empty); // ImmList()
     * ```
     *
     * @param string $s String to split
     * @return ImmList<string> List of lines
     */
    function lines(string $s): ImmList
    {
        return ImmList(...explode(PHP_EOL, $s));
    }

    const words = "\\Phunkie\\Functions\\string\\words";
    /**
     * Splits a string into words.
     * 
     * Breaks a string at whitespace into an immutable list.
     * Handles multiple whitespace characters.
     *
     * Example:
     * ```php
     * $text = "hello   world";
     * words($text);  // ImmList("hello", "world")
     * 
     * $spaces = "  ";
     * words($spaces); // ImmList()
     * ```
     *
     * @param string $s String to split
     * @return ImmList<string> List of words
     */
    function words(string $s): ImmList
    {
        return ImmList(...preg_split('/\s+/', $s));
    }

    const unlines = "\\Phunkie\\Functions\\string\\unlines";
    /**
     * Joins lines into a string.
     * 
     * Combines a list of strings into a single string with newlines.
     * Inverse operation of lines().
     *
     * Example:
     * ```php
     * $lines = ImmList("hello", "world");
     * unlines($lines);  // "hello\nworld"
     * 
     * $empty = ImmList();
     * unlines($empty);  // ""
     * ```
     *
     * @param ImmList<string> $lines Lines to join
     * @return string Combined string
     */
    function unlines(ImmList $lines): string
    {
        return $lines->mkString("\n");
    }

    const unwords = "\\Phunkie\\Functions\\string\\unwords";
    /**
     * Joins words into a string.
     * 
     * Combines a list of strings into a single space-separated string.
     * Inverse operation of words().
     *
     * Example:
     * ```php
     * $words = ImmList("hello", "world");
     * unwords($words);  // "hello world"
     * 
     * $empty = ImmList();
     * unwords($empty);  // ""
     * ```
     *
     * @param ImmList<string> $words Words to join
     * @return string Combined string
     */
    function unwords(ImmList $words): string
    {
        return $words->mkString(" ");
    }
}
