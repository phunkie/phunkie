# Function1

Function1 in Phunkie represents a single-argument function that can be composed and transformed in a functional way. It provides a wrapper around PHP callables that enables function composition, currying, and type-safe operations with comprehensive type class support.

## Creating Functions

There are several ways to create Function1 instances:

```php
// From a closure
$f = Function1(fn(int $x): int => $x + 1);

// Identity function
$id = Function1::identity(); // fn($x) => $x

// From a named function
$f = Function1('strtoupper');

// From a method
$f = Function1([$object, 'method']);
```

## Basic Operations

Function1 provides several basic operations for working with functions:

```php
// Create a function
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
// g is applied first, then f
$h = $f->compose($g);
$result = $h(5);  // (5 * 2) + 1 = 11

// Combine (alias for compose)
$h = $f->combine($g);  // Same as compose

// Multiple composition using compose function
use function Phunkie\Functions\function1\compose;

$f = fn($x) => $x + 1;
$g = fn($x) => $x * 2;
$h = fn($x) => $x - 3;

$composed = compose($f, $g, $h);
$result = $composed(5);  // ((5 - 3) * 2) + 1 = 5
```

## Functor Operations

Function1 implements the Functor type class, allowing you to map over the function's output:

```php
$f = Function1(fn($x) => $x + 1);

// Map over the output (same as andThen for functions)
$g = $f->map(fn($x) => $x * 2);
$result = $g(5);  // (5 + 1) * 2 = 12

// Invariant map (same as map for functions)
$h = $f->imap(
    fn($x) => $x * 2,  // forward transformation
    fn($x) => $x / 2   // backward (unused for functions)
);
```

**Functor Laws:**
- Identity: `$f->map(fn($x) => $x)` === `$f`
- Composition: `$f->map($g)->map($h)` === `$f->map(fn($x) => $h($g($x)))`

## Applicative Operations

Function1 implements the Applicative type class:

```php
$f = Function1(fn($x) => $x + 1);
$g = Function1(fn($x) => $x * 2);

// Pure - creates a constant function
$constant = $f->pure(42);
$constant(1);     // 42
$constant(100);   // 42

// Apply - composes functions
$h = $f->apply($g);
$h(5);  // g(f(5)) = (5 + 1) * 2 = 12

// Map2 - combines two functions
$add = Function1(fn($x) => $x + 1);
$mult = Function1(fn($x) => $x * 2);

$combined = $add->map2($mult, fn($a, $b) => $a + $b);
$combined(5);  // (5 + 1) + (5 * 2) = 16
```

**Applicative Laws:**
- Identity: `$f->pure($x)->apply($v)` preserves `$v`
- Composition: Applicative composition distributes

## Monad Operations

Function1 implements the Monad type class, providing flatMap and flatten for Kleisli composition:

```php
// FlatMap - Kleisli composition
$double = Function1(fn($x) => $x * 2);

// Returns a function that creates an adder
$addX = fn($x) => Function1(fn($y) => $x + $y);

$composed = $double->flatMap($addX);
// Input 5: double(5) = 10, then create fn($y) => 10 + $y, apply to 5 => 15
$composed(5);  // 15

// Flatten - removes one level of nesting
$nested = Function1(fn($x) => Function1(fn($y) => $x + $y));
$flattened = $nested->flatten();
// Both functions receive the same input
$flattened(5);  // 5 + 5 = 10
```

**Monad Laws:**
- Left Identity: `$pure($a)->flatMap($f)` === `$f($a)`
- Right Identity: `$m->flatMap($pure)` === `$m`
- Associativity: `$m->flatMap($f)->flatMap($g)` === `$m->flatMap(fn($x) => $f($x)->flatMap($g))`

## Profunctor Operations

Function1 implements Profunctor operations, allowing transformation of both input and output:

```php
$double = Function1(fn(int $x): int => $x * 2);

// lmap - preprocess input (contravariant)
$doubleString = $double->lmap(fn(string $s) => (int)$s);
$doubleString("21");  // "21" -> 21 -> 42

// rmap - postprocess output (covariant, same as map)
$doubleToString = $double->rmap(fn(int $x) => (string)$x);
$doubleToString(21);  // 21 -> 42 -> "42"

// dimap - both input and output transformation
$stringDoubleString = $double->dimap(
    fn(string $s) => (int)$s,  // preprocess input
    fn(int $x) => (string)$x   // postprocess output
);
$stringDoubleString("21");  // "21" -> 21 -> 42 -> "42"
```

**Profunctor Intuition:**
- `lmap`: Adapt the input type (contravariant)
- `rmap`: Transform the output type (covariant)
- `dimap`: Do both at once

**Profunctor Laws:**
- Identity: `$f->dimap($id, $id)` === `$f`
- Composition: `$f->dimap($f . $g, $h . $i)` === `$f->dimap($g, $h)->dimap($f, $i)`

## Memoization

Function1 provides memoization for caching expensive computations:

```php
$expensive = Function1(function($n) {
    sleep(1);  // Simulate expensive computation
    return $n * $n;
});

$memoized = $expensive->memoize();

$memoized(5);  // Takes ~1 second, returns 25
$memoized(5);  // Instant (cached), returns 25
$memoized(6);  // Takes ~1 second, returns 36
```

**Memoization Notes:**
- Works best with scalar inputs (int, string, bool)
- Objects are cached by hash or string representation
- Arrays can be cached but may have performance implications
- Cache persists for the lifetime of the memoized function

## Type Safety

Function1 maintains type information and provides runtime type checking:

```php
// Type information is preserved
$f = Function1(fn(int $x): int => $x + 1);
echo $f->toString();  // "Function1(Int=>Int)"

// Get type arity
$f->getTypeArity();  // 2 (input and output types)

// Get type variables
$f->getTypeVariables();  // ["Int", "Int"]

// Type checking
$f = Function1(fn($x, $y) => $x + $y);
// Throws TypeError: Function1 takes a callable with 1 parameter

$f = Function1(fn() => 42);
// Throws TypeError: Function1 takes a callable with 1 parameter
```

## Pattern Matching

Function1 supports pattern matching with the underscore placeholder:

```php
$value = Function1(fn($x) => $x + 1);

$result = match($value) {
    Function1(_) => "It's a function!",
    default => "Not a function"
};
```

## Equality

Function1 supports equality checking using sample values:

```php
$f = Function1(fn($x) => $x * 2);
$g = Function1(fn($x) => $x + $x);

// Check if functions produce same results
$f->eqv($g, Some(5));  // true (both return 10 for input 5)
```

## Showable

Function1 implements Show for string representation:

```php
use function Phunkie\Functions\show\showValue;

$f = Function1(fn(int $x): string => (string)$x);
showValue($f);  // "Function1(Int=>String)"
```

## Best Practices

1. **Use Function1 for Composition**: Wrap functions in Function1 when you need to compose or transform them
2. **Leverage Type Safety**: Use typed functions for better error detection
3. **Use Memoization Wisely**: Only memoize pure functions with expensive computations
4. **Prefer Profunctor Operations**: Use `lmap`, `rmap`, and `dimap` for input/output transformations
5. **Understand Composition Direction**:
   - `andThen`: left-to-right (f then g)
   - `compose`: right-to-left (g then f)
6. **Use Monad Operations for Nested Functions**: flatMap is perfect for Kleisli composition

## Common Patterns

### Pipeline Pattern
```php
$pipeline = Function1(fn($x) => $x)
    ->andThen(fn($x) => $x * 2)
    ->andThen(fn($x) => $x + 1)
    ->andThen(fn($x) => (string)$x);

$pipeline(5);  // "11"
```

### Adapter Pattern
```php
// Adapt a function to work with different types
$intDouble = Function1(fn(int $x): int => $x * 2);

$stringDouble = $intDouble->dimap(
    fn(string $s) => (int)$s,
    fn(int $x) => (string)$x
);

$stringDouble("21");  // "42"
```

### Kleisli Composition
```php
// Composing functions that return wrapped values
$safe_divide = fn($x) => Function1(fn($y) =>
    $y == 0 ? None() : Some($x / $y)
);

$computation = Function1(fn($x) => $x * 2)
    ->flatMap($safe_divide);
```

## Implementation Notes

- Function1 only works with single-argument functions
- Type information is preserved through reflection
- The implementation supports both named functions and closures
- Function composition maintains type safety
- All operations preserve immutability
- Memoization uses a closure-scoped cache
- Profunctor operations enable powerful type adaptations

## Type Class Hierarchy

```
Kind
  └── Functor
        └── Applicative
              └── Monad
```

Function1 implements all of these type classes, providing:
- **Functor**: `map`, `imap`
- **Applicative**: `pure`, `apply`, `map2`
- **Monad**: `flatMap`, `flatten`
- **Profunctor**: `dimap`, `lmap`, `rmap`

## See Also

- [Composition Functions](/docs/functions/composition.md)
- [Monad Type Class](/docs/monads.md)
- [Functor Type Class](/docs/functors.md)
