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
$union = $set1->union($set2);       // Set(1, 2, 3, 4, 5)

// Intersection (common elements)
$common = $set1->intersect($set2);  // Set(3)

// Difference (elements in either set but not both)
$diff = $set1->diff($set2);         // Set(1, 2, 4, 5)

// Add element (plus or add)
$new = $set1->plus(4);              // Set(1, 2, 3, 4)
$new = $set1->add(4);               // Set(1, 2, 3, 4)

// Remove element (minus or remove)
$new = $set1->minus(2);             // Set(1, 3)
$new = $set1->remove(2);            // Set(1, 3)

// Subset checking
$set1->subsetOf($set2);             // false
ImmSet(3)->subsetOf($set1);         // true

// Proper subset checking
ImmSet(1, 2)->properSubsetOf($set1); // true
$set1->properSubsetOf($set1);        // false

// Superset checking
$set1->supersetOf(ImmSet(1, 2));     // true
$set1->supersetOf($set2);            // false

// Proper superset checking
$set1->properSupersetOf(ImmSet(1));  // true

// Disjoint checking
$set1->disjoint($set2);              // false (they share 3)
ImmSet(1, 2)->disjoint(ImmSet(4, 5)); // true

// Size/length
$set1->size();                       // 3
$set1->length();                     // 3

// Min and max
ImmSet(3, 1, 4, 5)->min();           // Some(1)
ImmSet(3, 1, 4, 5)->max();           // Some(5)
ImmSet()->min();                     // None

// Head (first element in insertion order)
$set1->head();                       // Some(1)
ImmSet()->head();                    // None

// Convert to list
$list = $set1->toList();             // ImmList(1, 2, 3)
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

## Foldable Operations

Sets implement the Foldable type class for reducing values:

```php
$set = ImmSet(1, 2, 3);

// Fold left (left-associative fold)
$sum = ($set->foldLeft(0))(fn($acc, $x) => $acc + $x);  // 6
$concat = ($set->foldLeft(""))(fn($acc, $x) => $acc . $x);  // "123"

// Fold right (right-associative fold)
$sum = ($set->foldRight(0))(fn($x, $acc) => $x + $acc);  // 6
$concat = ($set->foldRight(""))(fn($x, $acc) => $x . $acc);  // "321"

// Fold map (map each element and combine with monoid)
$result = $set->foldMap(fn($x) => [$x]);  // [1, 2, 3]
$strings = ImmSet("a", "b", "c")->foldMap(fn($x) => $x);  // "abc"

// Fold (general catamorphism)
$product = ($set->fold(1))(fn($acc, $x) => $acc * $x);  // 6
```

## Monoid Operations

Sets form a Monoid under union:

```php
$s1 = ImmSet(1, 2);
$s2 = ImmSet(2, 3);
$s3 = ImmSet(3, 4);

// Zero (identity element) - empty set
$empty = $s1->zero();                    // ImmSet()

// Combine (binary operation) - union
$combined = $s1->combine($s2);           // ImmSet(1, 2, 3)

// Monoid laws:
// Left identity: zero()->combine(s) == s
$empty->combine($s1);                    // ImmSet(1, 2)

// Right identity: s->combine(zero()) == s
$s1->combine($empty);                    // ImmSet(1, 2)

// Associativity: a->combine(b)->combine(c) == a->combine(b->combine(c))
$s1->combine($s2)->combine($s3);         // ImmSet(1, 2, 3, 4)
$s1->combine($s2->combine($s3));         // ImmSet(1, 2, 3, 4)
```

## Traversable Operations

Sets support filtering and partitioning:

```php
$set = ImmSet(1, 2, 3, 4, 5);

// Filter (keep elements that satisfy predicate)
$evens = $set->filter(fn($x) => $x % 2 === 0);  // ImmSet(2, 4)

// Filter not (keep elements that don't satisfy predicate)
$odds = $set->filterNot(fn($x) => $x % 2 === 0);  // ImmSet(1, 3, 5)

// Partition (split into two sets based on predicate)
$pair = $set->partition(fn($x) => $x % 2 === 0);
// Pair(ImmSet(2, 4), ImmSet(1, 3, 5))
list($evens, $odds) = $pair;
```

## Type Safety

Sets maintain type information and implement various type classes:
- Functor - map, as, void, zipWith
- Applicative - pure, apply, map2
- Monad - flatMap, flatten
- Foldable - foldLeft, foldRight, foldMap, fold
- Monoid - zero, combine
- Eq - equality comparison

## Best Practices

1. Use ImmSet when you need a collection of unique elements
2. Leverage set operations (union, intersect, diff) for mathematical set manipulations
3. Use functor and monad operations for transformations
4. Take advantage of foldable operations for aggregations
5. Use filter/partition for conditional processing
6. Remember that element order is preserved in insertion order (but should not be relied upon for semantics)
7. Use subset/superset operations for set relationship checks
8. Leverage the monoid instance for combining sets

## Implementation Notes

- Sets are truly immutable - operations return new sets
- Duplicate elements are automatically removed
- Object equality is used for comparing elements
- The implementation uses array internally for storage
- Set operations maintain the uniqueness property