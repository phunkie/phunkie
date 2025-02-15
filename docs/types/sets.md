# Immutable Sets

Immutable Sets (ImmSet) in Phunkie are unordered collections of unique elements that cannot be modified after creation. They implement various functional programming patterns and type classes.

## Creating Sets

There are several ways to create immutable sets:

```php
// Empty set
$empty = ImmSet();
// Set with elements (duplicates are automatically removed)
$set = ImmSet(1, 2, 3, 2); // Set(1, 2, 3)
// Set with objects
$set = ImmSet(
Item(1),
Item(2),
Item(3)
);
```

## Basic Operations

Sets provide several basic operations:

```php
// Check if element exists
$set->contains(2);     // true
$set->contains(42);    // false

// Check if empty
$set->isEmpty();       // false

// Convert to array
$set->toArray();       // [1, 2, 3]
```

## Set Operations

Sets support standard mathematical set operations:

```php
$set1 = ImmSet(1, 2, 3);
$set2 = ImmSet(3, 4, 5);

// Union (combine sets)
$union = $set1->union($set2);      // Set(1, 2, 3, 4, 5)

// Intersection (common elements)
$common = $set1->intersect($set2);  // Set(3)

// Difference (elements in either set but not both)
$diff = $set1->diff($set2);         // Set(1, 2, 4, 5)

// Add element
$new = $set1->plus(4);             // Set(1, 2, 3, 4)

// Remove element
$new = $set1->minus(2);            // Set(1, 3)
```

## Functor Operations

Sets implement the Functor type class:

```php
$set = ImmSet(1, 2, 3);

// Map
$doubled = $set->map(fn($x) => $x * 2);  // Set(2, 4, 6)

// Convert all elements to a single value
$zeros = $set->as(0);                     // Set(0, 0, 0)

// Convert all elements to Unit
$units = $set->void();                    // Set(Unit(), Unit(), Unit())

// Zip with function
$zipped = $set->zipWith(fn($x) => $x * 2);
// Set(Pair(1, 2), Pair(2, 4), Pair(3, 6))
```

## Applicative Operations

Sets implement the Applicative type class:

```php
// Apply functions to values
$result = ImmSet(1)->apply(ImmSet(fn($x) => $x + 1));  // Set(2)

// Pure
$single = pure(ImmSet)(42);  // Set(42)

// Map2
$sum = map2(fn($x, $y) => $x + $y)(ImmSet(1))(ImmSet(2));  // Set(3)
```

## Monad Operations

Sets implement the Monad type class:

```php
// FlatMap
$result = ImmSet(1, 2)->flatMap(fn($x) => ImmSet($x, $x + 1));
// Set(1, 2, 2, 3)

// Flatten
$nested = ImmSet(ImmSet(1), ImmSet(2));
$flat = flatten($nested);  // Set(1, 2)

// Monad composition
$xs = ImmSet("h");
$f = fn($s) => ImmSet($s . "e");
$g = fn($s) => ImmSet($s . "l");
$h = fn($s) => ImmSet($s . "o");
$hello = mcompose($f, $g, $g, $h);
$result = $hello($xs);  // Set("hello")
```

## Type Safety

Sets maintain type information and implement various type classes:
- Functor
- Applicative
- Monad
- Eq

## Best Practices

1. Use ImmSet when you need a collection of unique elements
2. Leverage set operations for mathematical set manipulations
3. Use functor and monad operations for transformations
4. Take advantage of the type class implementations
5. Remember that element order is not guaranteed

## Implementation Notes

- Sets are truly immutable - operations return new sets
- Duplicate elements are automatically removed
- Object equality is used for comparing elements
- The implementation uses array internally for storage
- Set operations maintain the uniqueness property