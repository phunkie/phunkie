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

