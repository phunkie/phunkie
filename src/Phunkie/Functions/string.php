<?php

namespace Phunkie\Functions\string {

    use Phunkie\Types\ImmList;

    const lines = "\\Phunkie\\Functions\\string\\lines";
    function lines(string $s): ImmList
    {
        return ImmList(...explode(PHP_EOL, $s));
    }

    const words = "\\Phunkie\\Functions\\string\\words";
    function words(string $s): ImmList
    {
        return ImmList(...preg_split('/\s+/', $s));
    }

    const unlines = "\\Phunkie\\Functions\\string\\unlines";
    function unlines(ImmList $lines): string
    {
        return $lines->mkString("\n");
    }

    const unwords = "\\Phunkie\\Functions\\string\\unwords";
    function unwords(ImmList $words): string
    {
        return $words->mkString(" ");
    }
}