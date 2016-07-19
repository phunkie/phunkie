Phunkie
=======

Phunkie is a library with functional structures for PHP.

Options
-------
```bash
$ php -a
php> require_once "vendor/autoload.php";
php> show(Some(1));
Some(1)
php> show(None());
None
php> show(Option(42));
Some(42)
```

Lists
-----
```bash
php> show(ImmList(2,3,4));
List(1,2,3)
php> show(ImmList(Some(1), None(), Some(3)));
List(Some(1),None,Some(3))

php> show(head(ImmList(1,2,3)));
1
php> show(tail(ImmList(1,2,3)));
List(2,3)
php> show(init(ImmList(1,2,3)));
List(1,2)
php> show(last(ImmList(1,2,3)));
3
php> show(reverse(ImmList(1,2,3)));
List(3,2,3)
php> show(length(ImmList(1,2,3)))
3

php> $isNotGreen = function($x) { return $x != "Green"; };

php> $colours = ImmList("Black", "Red", "Green");
php> $notGreen = $colours->filter($isNotGreen);
php> show($notGreen);
List("Black","Red")

php> $greenAndNot = $colours->partition($isNotGreen);
php> show($greenAndNot);
List(List("Black","Red"),List("Green"))

php> $zipped = ImmList("A","B","C")->zip(ImmList(1,2,3));
php> show($zipped);
List(Pair("A",1),Pair("B",1),Pair("C",1))
```

Function1
---------
```bash
php> $f = Function1('strlen');
php> show($f("hello"));
5
php> $g = Function1(function($x) { return $x % 2 === 0; });
php> show($g($f("hello")));
false
php> $h = $g->compose($f);
php> show($h("hello"));
false
php> $h = $f->andThen($g);
php> show($h("hello"));
false
```

Functor
-------
```bash
php> show(Some(1)->map(function($x) { return $x + 1;}));
Some(2)
php> show(None()->map(function($x) { return $x + 1;}));
None
php> show(ImmList(1,2,3)->map(function($x) { return $x + 1;}));
List(2,3,4)
php> show(ImmList(1,2,3)->zipWith(function($x) { return $x + 1;}));
List(Pair(1,2),Pair(2,3),Pair(3,4))

php> $x = map(function($x) { return $x + 1;}, Some(42));
php> show($x);
Some(43)
```

Foldable
--------
```bash
php> $sum = ImmList(1,2,3)->foldLeft(0)(function($x, $y) { return $x + $y; });
php> show($sum);
6
php> $letters = ImmList("a", "b", "c")->foldLeft("letters:")(function($x, $y) { return $x . $y; });
php> show($letters);
"letters:abc"
php> $letters = ImmList("a", "b", "c")->foldRight("<--")(function($x, $y) { return $x . $y; });
php> show($letters);
"abc<--"
```

Currying
--------
```bash
php>  $curried = ImmList(1,2,3)->foldLeft(0)(_);
php> show($curried(function($x, $y) { return $x + $y; }));
6
```

Functor Composite
-----------------
```bash
php> $fa = Functor(Option);
php> show($fa->map(Option(1), function($x) { return $x + 1; }));
Some(2)

php> show($fa->map(Option(ImmList(1,2,3)), function($x) { return $x + 1; }));
PHP Notice:  Object of class Md\Phunkie\Types\ImmList could not be converted to int

php> $fa = Functor(Option)->compose(ImmList);
php> show($fa->map(Option(ImmList(1,2,3)), function($x) { return $x + 1; }));
Some(List(2,3,4))
```

Applicative
-----------
```bash
php> show(None()->pure(42));
Some(42)
php> show(ImmList()->pure(42));
List(42)
php> show(Some(1)->apply(Some(function($x) { return $x + 1;})));
Some(2)
php> show(None()->apply(Some(function($x) { return $x + 1;})));
None
php> show(ImmList(1,2,3)->apply(ImmList(function($x) { return $x + 1;})));
List(2,3,4)
php> show(ImmList()->apply(ImmList(function($x) { return $x + 1;})));show
List()
php> show(Some(1)->map2(Some(2), function($x, $y) { return $x + $y; }));
Some(3)
php> show(ImmList(1,2,3)->map2(ImmList(4,5,6), function($x, $y) { return $x + $y; }));
List(5,6,7,6,7,8,7,8,9)
```

Monad
-----
```bash
php> show(ImmList(1,2,3)->flatMap(function($x) { return Some($x + 1); }));
List(2,3,4)
php> show(ImmList(1,2,3)->flatMap(function($x) { return $x % 2 === 0 ? None() : Some($x + 1); }));
List(2,4)
php> show(ImmList(1,2,3)->flatMap(function($x) { return None(); }));
List()
php> show(ImmList(1,2,3)->flatMap(function($x) { return ImmList($x + 1, $x + 2); }));
List(2,3,3,4,4,5)
php> show(Some(1)->flatMap(function($x) { return Some($x + 1); }));
Some(2)
php> show(Some(1)->flatMap(function($x) { return None(); }));
None
php> show(None()->flatMap(function($x) { return Some(42); }));
None
php> show(ImmList(1,2,3)->flatMap(function($x) { return ImmList(Some($x + 1)); }));>php show
List(Some(2),Some(3),Some(4))

php> show(Some(Some(42))->flatten());
Some(42)
php> show(ImmList(ImmList(1,2,3))->flatten());
List(1,2,3)
```

Kleisli
-------
```bash
php> $f = kleisli(function($x) { return Some($x + 1); });
php> $g = kleisli(function($x) { return Some($x + 4); });
php> $x = $k->andThen($g);
php> show(($x->run)(3));
Some(8)
```

Monoid
------
```bash
php> show(combine(1,1));
2
php> show(combine("a","b"));
"ab"
php> show(combine([1,2,3], [4,5,6]));
[1,2,3,4,5,6]
php> show(combine(true, false));
false
php> show(combine(ImmList(1,2,3), ImmList(4,5,6)));
List(1,2,3,4,5,6)
php> show(combine(Some(4), Some(2)));
6
php> show(combine(Some("4"), Some("2")));
"42"
php> ImmList(1,2,3)->combine(ImmList(4,5,6));
List(1,2,3,4,5,6)

php> $option = Option(42);
php> zero($option);
None
php> show($option->zero());
None

php> $list = ImmList(1,2,3)
php> show($list->zero());
List()

php> show(zero(rand(1,45)));
0
php> show(zero([1,2,3]));
[]
```