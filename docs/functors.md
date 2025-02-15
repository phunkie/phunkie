# Functors

A functor is one of the most basic and important concepts in functional programming. In essence, a functor is a type that implements a `map` operation while following certain laws.

## Understanding Functors

A functor is any type `F<A>` that has an operation `map` which can transform a value of type `A` to type `B` while preserving the structure of `F`. In Phunkie, functors are defined by: 

```php
interface Functor {
    public function map(callable $f): Functor;
}
```

## Common Functors in Phunkie

### Options

```php
$option = Some(42);

$doubled = $some->map(fn ($x) => $x * 2); 

$none = None();

$doubled = $none->map(fn ($x) => $x * 2); // None
```

### List Functor

```php
$list = List(1, 2, 3);
$squared = $list->map(fn ($x) => $x * $x);  // List(1, 4, 9)
```

## Functor Laws

All functors must satisfy two fundamental laws:

1. Identity Law:

```php
$functor->map(fn ($x) => $x) === $functor;
```

2. Composition Law:

```php
$f->map($g)->map($h) === $f->map(fn ($x) => $h($g($x)));
```
