# Functors

A functor is a type class that represents the ability to map over a structure while preserving its shape. In Phunkie, functors provide a consistent way to transform values inside containers.

## What is a Functor?

A functor is any type `F<A>` that implements a `map` operation which can transform values of type `A` to type `B` while preserving the structure of `F`. The interface in Phunkie is:

```php
interface Functor extends Invariant {
    public function map(callable $f): Kind;
    public function lift($f): callable;
    public function as($b): Kind;
    public function void(): Kind;
    public function zipWith($f): Kind;
}
```

## Core Operations

### map

The fundamental operation of a functor:

```php
// Option functor
$option = Some(42);
$result = $option->map(fn($x) => $x * 2); // Some(84)

// List functor
$list = ImmList(1, 2, 3);
$result = $list->map(fn($x) => $x * 2); // ImmList(2, 4, 6)

// Function functor
$f = Function1(fn($x) => $x + 1);
$g = $f->map(fn($x) => $x * 2); // Function1(fn($x) => ($x + 1) * 2)
```

### lift

Convert a function to work with functorial values:

```php
$option = Some(42);
$lifted = $option->lift(fn($x) => $x + 1);
$result = $lifted(Some(42)); // Some(43)
```

### as

Replace all values with a constant while preserving structure:

```php
$list = ImmList(1, 2, 3);
$result = $list->as("a"); // ImmList("a", "a", "a")
```

### void

Replace all values with Unit:

```php
$option = Some(42);
$result = $option->void(); // Some(Unit())
```

### zipWith

Combine values with their transformed results:

```php
$list = ImmList(1, 2, 3);
$result = $list->zipWith(fn($x) => $x * 2);
// ImmList(Pair(1, 2), Pair(2, 4), Pair(3, 6))
```

## Functor Laws

All functors must satisfy two fundamental laws:

1. Identity: `fa->map(id) === fa`
```php
$list = ImmList(1, 2, 3);
$list->map(fn($x) => $x) === $list
```

2. Composition: `fa->map(f)->map(g) === fa->map(fn($x) => g(f($x)))`
```php
$f = fn($x) => $x + 1;
$g = fn($x) => $x * 2;

$option = Some(42);
$option->map($f)->map($g) === 
$option->map(fn($x) => $g($f($x)))
```

## Functor Composition

Functors can be composed to work with nested structures:

```php
use Phunkie\Cats\Functor\FunctorComposite;

// Compose Option and List functors
$f = new FunctorComposite(Option::kind);
$composed = $f->compose(ImmList::kind);

// Work with nested types
$data = ImmList(Some(1), None(), Some(2));
$result = $composed->map($data, fn($x) => $x + 1);
// ImmList(Some(2), None(), Some(3))
```

## Common Functors in Phunkie

- Option<A>
- ImmList<A>
- Function1<A, B>
- ImmSet<A>
- ImmMap<K, V>
- Validation<E, A>

## Best Practices

1. Use map for simple transformations
2. Leverage functor composition for nested structures
3. Use lift to adapt regular functions
4. Remember that map preserves structure
5. Ensure your functors satisfy the functor laws

## Implementation Notes

- Functors extend the Invariant interface
- map is the core operation
- Other operations (as, void, zipWith) are derived from map
- Functor composition maintains type safety
- Laws ensure consistent and predictable behavior
