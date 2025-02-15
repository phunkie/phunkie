# Function1

Function1 in Phunkie represents a single-argument function that can be composed and transformed in a functional way. It provides a wrapper around PHP callables that enables function composition, currying, and type-safe operations.

## Creating Functions

There are several ways to create Function1 instances:

```php
// From a callable
$f = Function1(fn(int $x): int => $x + 1);
// Identity function
$id = Function1::identity(); // fn($x) => $x
// From a method
$f = Function1([$object, 'method']);
```

## Basic Operations

Function1 provides several basic operations for working with functions:

```php
// From a callable
$f = Function1(fn($x) => $x + 1);

// Apply the function
$result = $f(5);        // 6
// or
$result = $f->run(5);   // 6

// Get the underlying callable
$callable = $f->get();
```

## Function Composition

Function1 supports both forward and backward composition:

```php
$f = Function1(fn($x) => $x + 1);
$g = Function1(fn($x) => $x * 2);

// Forward composition (f andThen g)
$h = $f->andThen($g);
$result = $h(5);  // (5 + 1) * 2 = 12

// Backward composition (f compose g)
$h = $f->compose($g);
$result = $h(5);  // (5 * 2) + 1 = 11

// Multiple composition using compose function
use function Phunkie\Functions\function1\compose;

$f = fn($x) => $x + 1;
$g = fn($x) => $x * 2;
$h = fn($x) => $x - 3;

$composed = compose($f, $g, $h);
$result = $composed(5);  // ((5 - 3) * 2) + 1 = 5
```

## Functor Operations

Function1 implements the Functor type class:

```php
$f = Function1(fn($x) => $x + 1);

// Map over the output
$g = $f->map(fn($x) => $x * 2);
$result = $g(5);  // (5 + 1) * 2 = 12
```

## Applicative Operations

Function1 implements the Applicative type class:

```php
$f = Function1(fn($x) => $x + 1);

// Apply
$g = Function1(fn($x) => fn($y) => $x + $y);
$h = $f->apply($g);

// Pure
$constant = $f->pure(42);
```

## Type Safety

Function1 maintains type information and provides runtime type checking:

```php
// Type information is preserved
$f = Function1(fn(int $x): int => $x + 1);
echo $f->toString();  // "Function1(Int=>Int)"

// Type checking
$f = Function1(fn($x, $y) => $x + $y);  // Throws TypeError: Function1 takes a callable with 1 parameter
```

## Best Practices

1. Use Function1 when you need to compose functions or transform their outputs
2. Leverage type information for safer function composition
3. Take advantage of the applicative and functor operations for complex transformations
4. Use `andThen` for forward composition and `compose` for backward composition

## Implementation Notes

- Function1 only works with single-argument functions
- Type information is preserved through reflections
- The implementation supports both named functions and closures
- Function composition maintains type safety