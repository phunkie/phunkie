# Composability of Functions

Function composability is a core concept in functional programming that allows building complex operations from simpler ones. Phunkie provides several tools and patterns for working with composable functions.

## Pure Functions

Pure functions are functions that:
1. Always produce the same output for the same input
2. Have no side effects
3. Don't depend on external state

Example of a pure function:

```php
// Pure function
$add = fn(int $a, int $b): int => $a + $b;

// Always returns the same result for same inputs
$add(2, 3);  // 5
$add(2, 3);  // 5 (always)
```

## Higher-order Functions

Higher-order functions are functions that either:
1. Take other functions as parameters
2. Return functions as results
3. Or both

```php
use Phunkie\Types\Function1;

// Function that takes a function as parameter
$map = Function1(fn($f) => fn($x) => $f($x));

// Function that returns a function
$multiply = fn($x) => fn($y) => $x * $y;
$times2 = $multiply(2);
$times2(4);  // 8
```

## Currying

Currying is the technique of converting a function that takes multiple arguments into a sequence of functions that each take a single argument. Phunkie provides built-in support for currying:

```php
use function Phunkie\Functions\currying\curry;
use function Phunkie\Functions\currying\uncurry;

// Currying a two-argument function
$add = fn($a, $b) => $a + $b;
$curriedAdd = curry($add);

// Now we can partially apply arguments
$add5 = $curriedAdd(5);
$result = $add5(3);  // 8

// Uncurrying converts back to multi-argument form
$normalAdd = uncurry($curriedAdd);
$result = $normalAdd(5, 3);  // 8

// Partial application with placeholder
$result = applyPartially(
    ['message'], 
    ['Hello'], 
    fn($msg) => "$msg World"
);
```

## Composing Functions

Function composition allows you to combine multiple functions into a single function. Phunkie provides several ways to compose functions:

```php
use Phunkie\Types\Function1;

// Basic function composition
$f = Function1(fn($x) => $x + 1);
$g = Function1(fn($x) => $x * 2);

// Forward composition (f andThen g)
$h = $f->andThen($g);
$result = $h(5);  // (5 + 1) * 2 = 12

// Backward composition (f compose g)
$h = $f->compose($g);
$result = $h(5);  // (5 * 2) + 1 = 11

// Combining functions with semigroup
use function Phunkie\Functions\semigroup\combine;

$increment = fn($x) => $x + 1;
$double = fn($x) => $x * 2;
$subtract3 = fn($x) => $x - 3;

$composed = combine($increment, $double, $subtract3);
$result = $composed(5);  // ((5 + 1) * 2) - 3 = 9
```

## Function1 Type Class Implementations

Function1 in Phunkie implements several type classes that enable powerful function compositions:

### Functor
```php
$f = Function1(fn($x) => $x + 1);
$g = $f->map(fn($x) => $x * 2);  // Same as andThen
```

### Applicative
```php
$f = Function1(fn($x) => $x + 1);
$g = Function1(fn($x) => fn($y) => $x + $y);
$h = $f->apply($g);
```

### Eq (Equality)
```php
$f = Function1(fn($x) => $x + 1);
$g = Function1(fn($x) => $x + 1);
$areEqual = $f->eqv($g, Some(42));  // true
```

## Best Practices

1. Prefer pure functions whenever possible
2. Use currying to create reusable function transformations
3. Leverage function composition to build complex operations
4. Take advantage of Function1's type class implementations
5. Use the semigroup combine operation for multi-function composition
