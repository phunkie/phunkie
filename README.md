Phunkie
=======

[![Build Status](https://travis-ci.org/phunkie/phunkie.svg?branch=master)](https://travis-ci.org/phunkie/phunkie)

Phunkie is a library with functional structures for PHP.

For better Phunkie development, consider installing [phunkie-console](https://github.com/MarcelloDuarte/phunkie-console).

```bash
$ bin/phunkie-console
Welcome to phunkie console.

Type in expressions to have them evaluated.

phunkie > 
```

Options
-------
```
phunkie > Some(1)
Option<Int> = Some(1)

phunkie > None()
None = None

phunkie > Option(42)
Option<Int> = Some(42)
```

Immutable Lists
---------------
You can import module functions with the `:import` command. Use `:help` for more information
```bash
phunkie > ImmList(2,3,4)
List<Int> = List(2, 3, 4)

phunkie > ImmList(Some(1), None(), Some(3))
List<Int> = List(Some(1), None, Some(3))

phunkie > :import immlist/*
Imported function \Phunkie\Functions\immlist\head()
Imported function \Phunkie\Functions\immlist\init()
Imported function \Phunkie\Functions\immlist\tail()
Imported function \Phunkie\Functions\immlist\last()
Imported function \Phunkie\Functions\immlist\reverse()
Imported function \Phunkie\Functions\immlist\length()
Imported function \Phunkie\Functions\immlist\concat()

phunkie > head (ImmList(1,2,3))
Int = 1

phunkie > tail (ImmList(1,2,3))
List<Int> = List(2, 3)

phunkie > init (ImmList(1,2,3))
List<Int> = List(1, 2)

phunkie > last (ImmList(1,2,3))
Int = 3

phunkie > reverse (ImmList(1,2,3))
List<Int> = List(3, 2, 1)

phunkie > length (ImmList(1,2,3))
Int = 3

phunkie > ImmList("Black", "Red", "Green")->filter(function($x) { return $x != "Green"; })
List<String> = List("Black", "Red")

phunkie > ImmList("Black", "Red", "Green")->partition(function($x) { return $x != "Green"; })
(List<String>, List<String>) = Pair(List("Black", "Red"), List("Green"))

phunkie > ImmList("A","B","C")->zip(ImmList(1,2,3))
List<(String, Int)> = List(Pair("A", 1), Pair("B", 2), Pair("C", 3))
```

Immutable Sets and Immutable Maps
---------------------------------
```bash
phunkie > ImmSet(1,2,3)
Set<Int> = Set(1, 2, 3)

phunkie > ImmSet(1,2,3,2) // No duplicates
Set<Int> = Set(1, 2, 3)

phunkie > ImmSet(1,2,3)->contains(3)
Boolean = true

phunkie > ImmSet(1,2,3)->minus(3) // creates a new set
Set<Int> = Set(1, 2)

phunkie > ImmSet(1,2,3)->plus(4) // again, new set
Set<Int> = Set(1, 2, 3, 4)

phunkie > ImmMap(["hello" => "there"])
Map<String, String> = Map("hello" -> "there")

phunkie > ImmMap(["hello" => "there"])->get("hello")
Option<String> = Some("there")

phunkie > ImmMap(["hello" => "there"])->get("zoom")
None = None

phunkie > ImmMap(["hello" => "there"])->plus("hi", "here") // creates a new Map
Map<String, String> = Map("hello" -> "there", "hi" -> "here")

// disclaimer: phunkie-console does not support multi-line instructions... yet!

phunkie > class Id {
phunkie {     private $number;
phunkie {     public function __construct($n)
phunkie {     {
phunkie {         $this->number = $n;
phunkie {     }
phunkie { }
defined class Id

phunkie > ImmMap(
phunkie (   new Id(1), "John Smith",
phunkie (   new Id(2), "Chuck Norris",
phunkie (   new Id(3), "Jack Bauer"
phunkie ( )
Map<Id, String> = Map(Id@2d05ca0 -> "John Smith", Id@2d05ca1 -> "Chuck Norris", Id@2d05ca2 -> "Jack Bauer")
```

Function1
---------
Disclaimer: phunkie-console does not support variable declaration just yet — but it is coming! The examples below are mainly to illustrate how `Function1` works.
```bash
phunkie > $f = Function1('strlen')
$f : Function1 = Function1(?=>?)

phunkie > $f("hello");
Int = 5

phunkie > $g = Function1(function($x) { return $x % 2 === 0; })
$g : Function1 = Function1(?=>?)

phunkie > $g($f("hello"))
Boolean = false

phunkie > $h = $g->compose($f)
$h : Function1 = Function1(?=>?)

phunkie > $h("hello")
Boolean = false

phunkie > $h = $f->andThen($g)
$h : Function1 = Function1(?=>?)

phunkie > $h("hello")
Boolean = false
```

Functor
-------
```bash
phunkie > Some(1)->map(function($x) { return $x + 1;})
Option<Int> = Some(2)

phunkie > None()->map(function($x) { return $x + 1;})
None = None

phunkie > ImmList(1,2,3)->map(function($x) { return $x + 1;})
List<Int> = List(2, 3, 4)

phunkie > ImmList(1,2,3)->zipWith(function($x) { return $x + 1;})
List<(Int, Int)> = List(Pair(1, 2), Pair(2, 3), Pair(3, 4))

phunkie> :import functor/*
Imported function \Phunkie\Functions\functor\fmap()

phunkie > fmap (function($x) { return $x + 1;}, Some(42))
Option<Int> = Some(43)
```

Foldable
--------
```bash
phunkie > ImmList(1,2,3)->foldLeft(0)(function($x, $y) { return $x + $y; })
Int = 6

phunkie > ImmList("a", "b", "c")->foldLeft("letters:")(function($x, $y) { return $x . $y; })
String = "letters:abc"

phunkie > ImmList("a", "b", "c")->foldRight("<--")(function($x, $y) { return $x . $y; })
String = "abc<--"
```

Currying
--------
```bash
phunkie > $curried = ImmList(1,2,3)->foldLeft(0)(_)
$curried : Callable = <function>

phunkie > $curried(function($x, $y) { return $x + $y; })
Int = 6

phunkie > $implode = curry('implode')
$implode: Callable = <function>

phunkie > $implodeColon = $implode(":")
$implodeColon: Callable = <function>

phunkie > $implodeColon(["a", "b", "c"])
$var0: String = "a:b:c"

phunkie> :import immlist/take
Imported function \Phunkie\Functions\immlist\take

phunkie > $take = uncurry(take)
$take: Callable = <function>

phunkie > $take(2, ImmList(1,2,3))
List<Int> = List(1, 2)
```

Functor Composite
-----------------
```bash
phunkie > $fa = Functor(Option)
$fa : Phunkie\Cats\Functor\FunctorComposite = Functor(Option)

phunkie > $fa->map(Option(1), function($x) { return $x + 1; })
Option<Int> = Some(2)

phunkie > fa->map(Option(ImmList(1,2,3)), function($x) { return $x + 1; })
Notice: Object of class Phunkie\Types\ImmList could not be converted to int

phunkie > $fa = Functor(Option)->compose(ImmList)
$fa : Phunkie\Cats\Functor\FunctorComposite = Functor(Option(List))

phunkie > $fa->map(Option(ImmList(1,2,3)), function($x) { return $x + 1; })
Option<List<Int>> = Some(List(2, 3, 4))
```

Applicative
-----------
```bash
phunkie > None()->pure(42)
Option<Int> = Some(42)

phunkie > ImmList()->pure(42)
List<Int> = List(42)

phunkie > Some(1)->apply(Some(function($x) { return $x + 1;}))
Option<Int> = Some(2)

phunkie > None()->apply(Some(function($x) { return $x + 1;}))
None = None

phunkie > ImmList(1,2,3)->apply(ImmList(function($x) { return $x + 1;}))
List<Int> = List(2, 3, 4)

phunkie > ImmList()->apply(ImmList(function($x) { return $x + 1;}))
List<Nothing> = List()

phunkie > Some(1)->map2(Some(2), function($x, $y) { return $x + $y; })
Option<Int> = Some(3)

phunkie > ImmList(1,2,3)->map2(ImmList(4,5,6), function($x, $y) { return $x + $y; })
List<Int> = List(5, 6, 7, 6, 7, 8, 7, 8, 9)
```

Monad
-----
```bash
phunkie > ImmList(1,2,3)->flatMap(function($x) { return Some($x + 1); })
List<Int> = List(2, 3, 4)

phunkie > ImmList(1,2,3)->flatMap(function($x) { return $x % 2 === 0 ? None() : Some($x + 1); })
List<Int> = List(2, 4)

phunkie > ImmList(1,2,3)->flatMap(function($x) { return None(); })
List<Nothing> = List()

phunkie > ImmList(1,2,3)->flatMap(function($x) { return ImmList($x + 1, $x + 2); })
List<Int> = List(2, 3, 3, 4, 4, 5)

phunkie > Some(1)->flatMap(function($x) { return Some($x + 1); })
Option<Int> = Some(2)

phunkie > Some(1)->flatMap(function($x) { return None(); })
None = None

phunkie > None()->flatMap(function($x) { return Some(42); })
None = None

phunkie > ImmList(1,2,3)->flatMap(function($x) { return ImmList(Some($x + 1)); })
List<Option<Int>> = List(Some(2),Some(3),Some(4))

phunkie > Some(Some(42))->flatten()
Option<Int> = Some(42)

phunkie > ImmList(ImmList(1,2,3))->flatten()
List<Int> = List(1, 2, 3)
```

Kleisli
-------
```bash
phunkie > $f = kleisli(function($x) { return Some($x + 1); })
phunkie > $g = kleisli(function($x) { return Some($x + 4); })
phunkie > $x = $f->andThen($g)
phunkie > $x->run(3)
Option<Int> = Some(8)
```

Monoid
------
```bash
phunkie > :import semigroup/*
Imported function \Phunkie\Functions\semigroup\combine()
Imported function \Phunkie\Functions\semigroup\zero()

phunkie > combine(1,1)
Int = 2

phunkie > combine("a","b")
String = "ab"

phunkie > combine([1,2,3], [4,5,6])
Array<Int> = [1, 2, 3, 4, 5, 6]

phunkie > combine(true, false)
Boolean = false

phunkie > combine(ImmList(1,2,3), ImmList(4,5,6))
List<Int> = List(1, 2, 3, 4, 5, 6)

phunkie > combine(Some(4), Some(2))
Option<Int> = Some(6)

phunkie > combine(Some("4"), Some("2"))
Option<String> = Some("42")

phunkie > ImmList(1,2,3)->combine(ImmList(4,5,6)
List<Int> = List(1, 2, 3, 4, 5, 6)

phunkie > zero(Option(42))
None = None

phunkie > Option(42)->zero()
None = None

phunkie > ImmList(1,2,3)->zero()
List<Nothing> = List()

phunkie > zero(rand(1,45))
Int = 0

phunkie > zero([1,2,3])
Array<Nothing> = []
```

Pattern Matching
----------------

###Working withLists

```php
<?php
use Phunkie\Types\ImmList;
use function Phunkie\PatternMatching\Referenced\ListWithTail;
use function Phunkie\PatternMatching\Referenced\ListNoTail;

function sum(ImmList $list): int { $on = match($list); switch(true) {
    case $on(Nil): return 0;
    case $on(ListNoTail($x, Nil)): return $x;
    case $on(ListWithTail($x, $xs)): return $x + sum($xs);}
}
```

###Validation monads wildcards

```php
<?php

$boom = function () { return Failure(Nel(new \Exception("Boom!"))); };
$on = match($boom()); switch (true) {
    case $on(Success(_)): return 2; break;
    case $on(Failure(_)): return 10; break;
}

$yay = function () { return Success("yay!"); };
$on = match($yay()); switch (true) {
    case $on(Failure(_)): return 2; break;
    case $on(Success(_)): return 10; break;
}
```

###Option wildcards

```php
<?php

$on = match(None()); switch (true) {
    case $on(None): return 10; break;
    case $on(Some(_)): return 2; break;
}
```

Lenses
------
```bash
phunkie > :import lens/*
Imported function \Phunkie\Functions\lens\trivial()
Imported function \Phunkie\Functions\lens\self()
Imported function \Phunkie\Functions\lens\fst()
Imported function \Phunkie\Functions\lens\snd()
Imported function \Phunkie\Functions\lens\contains()
Imported function \Phunkie\Functions\lens\member()

phunkie > trivial()->get(42) // returns Unit which does not print anything

phunkie > trivial()->set(42,34)
Int = 34

phunkie > self()->get(42)
Int = 42

phunkie > self()->set(42,34)
Int = 42

phunkie > fst()->get(Pair(1,2))
Int = 1

phunkie > fst()->set(3, Pair(1,2))
(Int, Int) = (3, 2)

phunkie > snd()->get(Pair(1,2))
Int = 2

phunkie > snd()->set(3, Pair(1,2))
(Int, Int) = (1, 3)

phunkie > $s = ImmSet(1,2,3)
$s : ImmSet<Int> = Set(1, 2, 3)

phunkie > contains(2)->get($s)
Boolean = true

phunkie > contains(4)->set($s, true)
ImmSet<Int> = Set(1, 2, 3, 4)

phunkie > contains(3)->set($s, false)
ImmSet<Int> = Set(1, 2)

phunkie > $m = ImmMap(["a" => 1, "b" => 2])
$m : ImmMap<String, Int> = Map("a" -> 1, "b" -> 2)

phunkie > member("b")->get($m)
Option<Int> = Some(2)

phunkie > member("b")->set($m, None())
ImmMap<String, Int> = Map("a" -> 1)

phunkie > member("b")->set($m, Some(3))
ImmMap<String, Int> = Map("a" -> 1, "b" -> 3)
```
