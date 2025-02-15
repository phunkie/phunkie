# Monads

Monads are a powerful abstraction in functional programming that allow you to chain operations while handling effects like optionality, state, or IO. In Phunkie, monads extend the Applicative type class and add operations for sequencing computations.

## What is a Monad?

A monad in Phunkie consists of:
1. A type constructor M<A>
2. A way to wrap a value (pure/return)
3. A way to compose monadic functions (flatMap/bind)

The interface is defined as:

```php
interface Monad extends FlatMap {
    public function flatten(): Kind;
}

interface FlatMap extends Functor {
    public function flatMap(callable $f): Kind;
}
```

## Common Monads in Phunkie

### Option Monad

```php
// Chain computations that might fail
$result = Some(42)
    ->flatMap(fn($x) => Some($x + 1))
    ->flatMap(fn($x) => $x < 50 ? Some($x) : None());

// None propagates through the chain
$result = None()
    ->flatMap(fn($x) => Some($x + 1)); // None
```

### List Monad

```php
// Sequence operations on lists
$result = ImmList(1, 2, 3)
    ->flatMap(fn($x) => ImmList($x, $x * 2));
// ImmList(1, 2, 2, 4, 3, 6)

// Flatten nested lists
$nested = ImmList(ImmList(1), ImmList(2, 3));
$flat = $nested->flatten(); // ImmList(1, 2, 3)
```

### Function1 Monad

```php
$f = Function1(fn($x) => $x + 1);
$g = Function1(fn($x) => $x * 2);

// Compose functions with flatMap
$h = $f->flatMap(fn($x) => $g); 
$result = $h(5); // (5 + 1) * 2 = 12
```

## Monad Laws

All monads must satisfy three fundamental laws:

1. Left Identity: `pure(a)->flatMap(f) === f(a)`
```php
$f = fn($x) => Some($x + 1);
Some(42)->flatMap($f) === $f(42)
```

2. Right Identity: `m->flatMap(pure) === m`
```php
Some(42)->flatMap(fn($x) => Some($x)) === Some(42)
```

3. Associativity: `m->flatMap(f)->flatMap(g) === m->flatMap(fn($x) => f($x)->flatMap(g))`
```php
$f = fn($x) => Some($x + 1);
$g = fn($x) => Some($x * 2);

Some(42)->flatMap($f)->flatMap($g) === 
Some(42)->flatMap(fn($x) => $f($x)->flatMap($g))
```

## Composing Monads

Monads can be composed using monad transformers:

```php
// Compose Option with List
$data = OptionT(ImmList(Some(1), None(), Some(2)));

// Map and flatMap work with the nested structure
$result = $data
    ->map(fn($x) => $x + 1)
    ->flatMap(fn($x) => OptionT(ImmList(Some($x * 2))));
```

## Best Practices

1. Use monads to sequence operations with effects
2. Leverage flatMap for dependent computations
3. Use monad transformers to work with nested effects
4. Ensure your monadic operations satisfy the monad laws
5. Consider using applicative when operations are independent

## Implementation Notes

- Monads extend both Functor and Applicative
- flatMap is the key operation for sequencing
- flatten helps work with nested monadic structures
- Monad transformers handle composition of different monads
- Laws ensure consistent and predictable behavior
