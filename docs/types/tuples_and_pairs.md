# Tuples and Pairs

Tuples in Phunkie are immutable ordered collections of elements where each element can have a different type. Pairs are specialized tuples with exactly two elements.

## Creating Tuples and Pairs

There are several ways to create tuples and pairs:

```php
// Empty tuple (Unit)
$unit = Tuple();
// Pair (2-tuple)
$pair = Pair("hello", 42);
// or
$pair = Tuple("hello", 42);
// Tuple with more elements
$tuple = Tuple("name", 25, true);
```

## Accessing Elements

Tuples and Pairs use 1-based indexing with underscore prefix:

```php
$pair = Pair("hello", 42);
$tuple = Tuple("name", 25, true);
// Pair access
$first = $pair->_1; // "hello"
$second = $pair->_2; // 42
// Tuple access
$first = $tuple->_1; // "name"
$second = $tuple->_2; // 25
$third = $tuple->_3; // true
// Using helper functions for pairs
use function Phunkie\Functions\pair\_1;
use function Phunkie\Functions\pair\_2;
$first = _1($pair); // "hello"
$second = _2($pair); // 42
```

## Assignment and Destructuring

Tuples support a special assignment syntax for destructuring:

```php
// Assign tuple elements to variables
$name = $age = $isStudent = null;
(compose(assign($name, $age, $isStudent)))(Tuple("John", 25, true));
echo $name;       // "John"
echo $age;        // 25
echo $isStudent;  // true

// Works with pairs too
$name = $age = null;
(compose(assign($name, $age)))(Pair("John", 25));
```

## Functor Operations

Tuples implement the Functor type class:

```php
$tuple = Tuple(1, 2, 3);

// Map over all elements
$result = $tuple->map(fn($x) => $x * 2);  // Tuple(2, 4, 6)

// Convert all elements to a single value
$zeros = $tuple->as(0);  // Tuple(0, 0, 0)

// Convert all elements to Unit
$units = $tuple->void(); // Tuple(Unit(), Unit(), Unit())

// Zip with a function
$zipped = $tuple->zipWith(fn($x) => $x * 2);
// Tuple(Pair(1, 2), Pair(2, 4), Pair(3, 6))
```

## Type Safety

Tuples and Pairs maintain type information for all elements:

```php
$pair = Pair("hello", 42);
echo $pair->showType();  // "(String, Int)"

$tuple = Tuple("name", 25, true);
echo $tuple->toString(); // "(name, 25, true)"
```

## Immutability

Tuples and Pairs are immutable:

```php
$pair = Pair("hello", 42);

// These will throw TypeError
$pair->_1 = "world";     // Error: Pairs are immutable
$pair->_3 = true;        // Error: Invalid index _3 for pair
```

## Best Practices

1. Use Pairs when you need to group exactly two related values
2. Use Tuples when you need to group more than two values
3. Consider using case classes for more complex data structures
4. Leverage the functor operations for bulk transformations
5. Use the assignment function for clean destructuring

## Implementation Notes

- Tuples and Pairs are truly immutable
- Elements are 1-based indexed with underscore prefix (_1, _2, etc.)
- Type information is preserved for all elements
- Pairs are specialized tuples with exactly two elements
- The implementation prevents inheritance outside the package