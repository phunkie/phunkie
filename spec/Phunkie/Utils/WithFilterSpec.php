<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Phunkie\Utils;

use Phunkie\Types\ImmList;
use Phunkie\Utils\WithFilter;
use PhpSpec\ObjectBehavior;

class WithFilterSpec extends ObjectBehavior
{
    private $filter;
    /** @var  ImmList */
    private $list;
    function let()
    {
        $this->filter = function ($x) {
            return $x % 2 == 0;
        };
        $this->list = ImmList(1, 2, 3);
        $this->beConstructedWith($this->list, $this->filter);
        $this->shouldHaveType(WithFilter::class);
    }

    function it_delegates_filter_to_monad()
    {
        $this->map(function($x) { return $x; })->shouldBeLike(
            $this->list->filter($this->filter)
        );
    }
}
