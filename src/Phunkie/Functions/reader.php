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

    const ask = "Md\\Phunkie\\Functions\\reader\\ask";
    function ask()
    {
        return Reader(identity);
    }
}