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
use Md\Unit\TestCase;

class WithFilterSpec extends TestCase
{
    private $filter;

    public function setUp(): void
    {
        $this->filter = new WithFilter(
            ImmList(1, 2, 3),
            function ($x) {
                return $x % 2 == 0;
            }
        );
    }

    /**
     * @test
     */
    public function it_is_initializable()
    {
        $ref = new \ReflectionClass($this->filter);
        $this->assertTrue($ref->isInstantiable());
    }

    /**
     * @test
     */
    public function it_delegates_filter_to_monad()
    {
        $this->assertIsLike(
            $this->filter->map(function ($x) {
                return $x;
            }),
            ImmList(1, 2, 3)->filter(function ($x) {
                return $x % 2 == 0;
            })
        );
    }
}
