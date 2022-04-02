<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Phunkie\Types;

use Phunkie\Cats\Show;
use Phunkie\Cats\Traverse;
use Phunkie\Ops\ImmList\ImmListApplicativeOps;
use Phunkie\Types\Nil;
use Md\Unit\TestCase;
use Md\PropertyTesting\TestTrait;
use Eris\Generator\SequenceGenerator as SeqGen;
use Eris\Generator\IntegerGenerator as IntGen;
use Phunkie\Utils\WithFilter;
use function Phunkie\Functions\applicative\ap;
use function Phunkie\Functions\applicative\pure;
use function Phunkie\Functions\applicative\map2;
use function Phunkie\Functions\monad\bind;
use function Phunkie\Functions\monad\flatten;

use function Phunkie\Functions\monad\mcompose;
use function Phunkie\Functions\show\showValue;
use function Phunkie\Functions\show\usesTrait;
use function Phunkie\Functions\immlist\transpose;

/**
 * @mixin ImmListApplicativeOps
 */
class ImmListSpec extends TestCase
{
    use TestTrait;

    /**
     * @test
     */
    public function it_is_showable()
    {
        $this->assertTrue(usesTrait(ImmList(2, 3, 4), Show::class));
        $this->assertEquals(showValue(ImmList(1, 2, 3)), "List(1, 2, 3)");
    }

    /**
     * @test
     */
    public function it_is_a_functor()
    {
        $spec = $this;
        $this->forAll(
            new SeqGen(new IntGen())
        )->then(function ($list) use ($spec) {
            $this->assertIsLike(
                ImmList(...$list)->map(function ($x) {
                    return $x + 1;
                }),
                ImmList(...array_map(function ($x) {
                    return $x + 1;
                }, $list))
            );
        });
    }

    /**
     * @test
     */
    public function it_returns_an_empty_list_when_an_empty_list_is_mapped()
    {
        $list = ImmList();
        $this->assertInstanceOf(Nil::class, $list);
        $this->assertPropertyCount(0, $list->map(function ($x) {
            return $x + 1;
        }));
    }

    /**
     * @test
     */
    public function it_is_has_applicative_ops()
    {
        $this->assertTrue(usesTrait(ImmList(1, 2, 3), ImmListApplicativeOps::class));
    }

    /**
     * @test
     */
    public function it_returns_an_empty_list_when_an_empty_list_is_applied()
    {
        $list = ImmList();
        $this->assertInstanceOf(Nil::class, $list);
        $this->assertPropertyCount(0, $list->apply(
            ImmList(function ($x) {
                return $x + 1;
            })
        ));
    }

    /**
     * @test
     */
    public function it_applies_the_result_of_the_function_to_a_List()
    {
        $spec = $this;
        $this->forAll(
            new SeqGen(new IntGen())
        )->then(function ($list) use ($spec) {
            $this->assertIsLike(
                ImmList(...$list)->apply(ImmList(function ($x) {
                    return $x + 1;
                })),
                ImmList(...array_map(function ($x) {
                    return $x + 1;
                }, $list))
            );
        });
    }

    /**
     * @test
     */
    public function it_returns_its_length()
    {
        $this->assertPropertyCount(3, ImmList(1, 2, 3));
    }

    /**
     * @test
     */
    public function it_has_filter()
    {
        $this->assertIsLike(
            ImmList(2),
            ImmList(1, 2, 3)->filter(function ($x) {
                return $x === 2;
            })
        );
    }

    /**
     * @test
     */
    public function it_has_withFilter()
    {
        $this->assertInstanceOf(
            WithFilter::class,
            ImmList(1, 2, 3)->withFilter(function ($x) {
                return $x === 2;
            })
        );
    }

    /**
     * @test
     */
    public function its_withFilter_plus_map_to_identity_is_equivalent_to_filter()
    {
        $list = ImmList(1, 2, 3);
        $this->assertIsLike(
            $list
                ->withFilter(function ($x) {
                    return $x === 2;
                })
                ->map(function ($x) {
                    return $x;
                }),
            $list->filter(function ($x) {
                return $x === 2;
            })
        );
    }

    /**
     * @test
     */
    public function it_has_reject()
    {
        $this->assertIsLike(
            ImmList(1, 3),
            ImmList(1, 2, 3)->reject(function ($x) {
                return $x === 2;
            })
        );
    }

    /**
     * @test
     */
    public function it_implements_reduce()
    {
        $this->assertEquals(6, ImmList(1, 2, 3)->reduce(function ($x, $y) {
            return $x + $y;
        }));
    }

    /**
     * @test
     */
    public function it_implements_reduce_string_example()
    {
        $this->assertEquals("abc", ImmList("a", "b", "c")->reduce(function ($x, $y) {
            return $x . $y;
        }));
    }

    /**
     * @test
     */
    public function it_will_complain_if_reduce_returns_a_type_different_to_the_list_type()
    {
        $this->expectError();

        ImmList(1, 2, 3)->reduce(function ($x, $y) {
            return "Oh no! a string!";
        });
    }

    /**
     * @test
     */
    public function it_can_be_casted_to_array()
    {
        $this->assertEquals([1, 2, 3], ImmList(1, 2, 3)->toArray());
    }

    /**
     * @test
     */
    public function it_can_be_transposed()
    {
        $list = ImmList(ImmList(1, 2, 3), ImmList(4, 5, 6));
        $transposed = ImmList(
            ImmList(1, 4),
            ImmList(2, 5),
            ImmList(3, 6)
        );
        $this->assertIsLike($list->transpose(), $transposed);
        $this->assertIsLike(transpose($list), $transposed);
    }

    /**
     * @test
     */
    public function it_zips()
    {
        $list = ImmList(1, 2, 3);
        $this->assertIsLike(
            $list->zip(ImmList("A", "B", "C")),
            ImmList(Pair(1, "A"), Pair(2, "B"), Pair(3, "C"))
        );
    }

    /**
     * @test
     */
    public function it_takes_n_elements_from_list()
    {
        $this->assertIsLike(ImmList(1, 2, 3)->take(2), ImmList(1, 2));
    }

    /**
     * @test
     */
    public function it_takes_while_something_is_true()
    {
        $list = ImmList(1, 2, 3, 4, 5, 6);
        $this->assertIsLike(
            $list->takeWhile(function ($el) {
                return $el < 4;
            }),
            ImmList(1, 2, 3)
        );

        $this->assertIsLike(
            $list->takeWhile(function ($el) {
                return $el < 9;
            }),
            ImmList(1, 2, 3, 4, 5, 6)
        );

        $this->assertIsLike(
            $list->takeWhile(function ($el) {
                return $el < 0;
            }),
            ImmList()
        );
    }

    /**
     * @test
     */
    public function it_drops_while_something_is_true()
    {
        $list = ImmList(1, 2, 3, 4, 5, 6);
        $this->assertIsLike(
            $list->dropWhile(function ($el) {
                return $el < 4;
            }),
            ImmList(4, 5, 6)
        );

        $this->assertIsLike(
            $list->dropWhile(function ($el) {
                return $el < 9;
            }),
            ImmList()
        );

        $this->assertIsLike(
            $list->dropWhile(function ($el) {
                return $el < 0;
            }),
            ImmList(1, 2, 3, 4, 5, 6)
        );
    }

    /**
     * @test
     */
    public function it_drops_n_elements_from_list()
    {
        $this->assertIsLike(ImmList(1, 2, 3)->drop(2), ImmList(3));
    }

    /**
     * @test
     */
    public function it_implements_head()
    {
        $this->assertEquals(1, ImmList(1, 2, 3)->head);
    }

    /**
     * @test
     */
    public function it_implements_tail()
    {
        $this->assertIsLike(ImmList(1, 2, 3)->tail, ImmList(2, 3));
    }

    /**
     * @test
     */
    public function it_implements_init()
    {
        $this->assertIsLike(ImmList(1, 2, 3)->init, ImmList(1, 2));
    }

    /**
     * @test
     */
    public function it_implements_last()
    {
        $this->assertEquals(3, ImmList(1, 2, 3)->last);
    }

    /**
     * @test
     */
    public function it_implements_shortcut_for_mapping_over_class_members()
    {
        $_ = underscore();
        $list = ImmList(new User("John"), new User("Alice"));
        $this->assertIsLike(
            $list->map($_->name)->map("strtoupper"),
            ImmList("JOHN", "ALICE")
        );
    }

    /**
     * @test
     */
    public function it_is_an_applicative()
    {
        $xs = (ap(ImmList(function ($a) {
            return $a +1;
        })))(ImmList(1));
        $this->assertIsLike($xs, ImmList(2));

        $xs = (pure(ImmList))(42);
        $this->assertIsLike($xs, ImmList(42));

        $xs = ((map2(function ($x, $y) {
            return $x + $y;
        }))(ImmList(1)))(ImmList(2));
        $this->assertIsLike($xs, ImmList(3));
    }

    /**
     * @test
     */
    public function it_is_a_monad()
    {
        $xs = (bind(function ($a) {
            return ImmList($a +1);
        }))(ImmList(1));
        $this->assertIsLike($xs, ImmList(2));

        $xs = flatten(ImmList(ImmList(1)));
        $this->assertIsLike($xs, ImmList(1));

        $xs = flatten(ImmList(ImmList(1), ImmList(2)));
        $this->assertIsLike($xs, ImmList(1, 2));

        $xs = ImmList("h");
        $f = function (string $s) {
            return ImmList($s . "e");
        };
        $g = function (string $s) {
            return ImmList($s . "l");
        };
        $h = function (string $s) {
            return ImmList($s . "o");
        };
        $hello = mcompose($f, $g, $g, $h);
        $this->assertIsLike($hello($xs), ImmList("hello"));
    }

    /**
     * @test
     */
    public function it_is_a_traverse()
    {
        $list = ImmList(1, 2, 3);
        $this->assertInstanceOf(Traverse::class, $list);

        $this->assertIsLike(
            $list->traverse(function ($x) {
                return Option($x);
            }),
            Some(ImmList(1, 2, 3))
        );

        $this->assertIsLike(
            $list->traverse(function ($x) {
                return $x > 2 ? None() : Some($x);
            }),
            None()
        );
    }

    /**
     * @test
     */
    public function it_implements_sequence()
    {
        $this->assertIsLike(
            ImmList(Some(1), Some(2), Some(3))->sequence(),
            Some(ImmList(1, 2, 3))
        );
    }

    /**
     * @test
     */
    public function it_returns_None_if_any_value_in_sequence_is_None()
    {
        $this->assertIsLike(
            ImmList(Some(1), None(), Some(3))->sequence(),
            None()
        );
    }
}

class User
{
    public $name;
    public function __construct($name)
    {
        $this->name = $name;
    }
}
