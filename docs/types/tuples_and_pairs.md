# Tuples and Pairs

Tuples in Phunkie are immutable ordered collections of elements where each element can have a different type. Pairs are specialized tuples with exactly two elements that support additional type class instances.

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

// Pair accessor methods
$first = $pair->fst(); // "hello"
$second = $pair->snd(); // 42 (same as extract())

// Tuple get method (1-based)
$value = $tuple->get(2); // 25
```

## Assignment and Destructuring

Tuples support a special assignment syntax for destructuring:

```php
use function Phunkie\Functions\function1\compose;
use function Phunkie\Functions\tuple\assign;

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

## Type Classes

### Functor Operations

Both Tuples and Pairs implement the Functor type class:

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

For Pairs, `map` operates on the second element only:

```php
$pair = Pair("label", 42);
$result = $pair->map(fn($n) => $n * 2);  // Pair("label", 84)
```

### Bifunctor Operations (Pair only)

Pairs implement the Bifunctor type class, allowing independent transformations of both elements:

```php
$pair = Pair("hello", 42);

// Transform both elements
$result = $pair->bimap(
    fn($s) => strtoupper($s),
    fn($n) => $n * 2
);
// Pair("HELLO", 84)

// Transform only the first element
$result = $pair->first(fn($s) => strtoupper($s));
// Pair("HELLO", 42)

// Transform only the second element
$result = $pair->second(fn($n) => $n * 2);
// Pair("hello", 84)
```

### Foldable Operations

Both Tuples and Pairs implement the Foldable type class:

```php
// Tuple folds over all elements
$tuple = Tuple(1, 2, 3, 4);
$sum = ($tuple->foldLeft(0))(fn($acc, $x) => $acc + $x);  // 10

$list = ($tuple->foldRight([]))(fn($x, $acc) => [$x, ...$acc]);
// [1, 2, 3, 4]

// Convert to ImmList
$immList = $tuple->toImmList();  // ImmList(1, 2, 3, 4)

// Pair folds over the second element only
$pair = Pair("label", 42);
$result = ($pair->foldLeft(10))(fn($acc, $x) => $acc + $x);  // 52

$list = $pair->toList();  // ImmList(42)
```

### Comonad Operations (Pair only)

Pairs implement the Comonad type class, where the first element provides context and the second is the focus:

```php
$pair = Pair("context", 42);

// Extract the focus value (second element)
$value = $pair->extract();  // 42

// Duplicate: wrap in another layer
$nested = $pair->duplicate();
// Pair("context", Pair("context", 42))

// Extend: apply a function that operates on the whole Pair
$result = $pair->extend(fn($p) => $p->_1 . ": " . $p->extract());
// Pair("context", "context: 42")

$pair = Pair(10, 5);
$result = $pair->extend(fn($p) => $p->_1 + $p->_2);
// Pair(10, 15)

// coflatMap is an alias for extend
$result = $pair->coflatMap(fn($p) => $p->_1 + $p->_2);
```

## Utility Operations

### Pair-specific Utilities

```php
$pair = Pair("name", 42);

// Swap elements
$swapped = $pair->swap();  // Pair(42, "name")
$pair->swap()->swap();     // Pair("name", 42) - swap is involution

// Merge both elements with a function
$result = Pair(10, 32)->merge(fn($a, $b) => $a + $b);  // 42
$text = Pair("Hello, ", "World")->merge(fn($a, $b) => $a . $b);
// "Hello, World"

// Apply same function to both elements
$pair = Pair(3, 4);
$squared = $pair->mapBoth(fn($x) => $x * $x);  // Pair(9, 16)

// forEach with both elements
$pair->forEach(fn($k, $v) => echo "$k: $v\n");
// Prints: name: 42

// Check predicates on both elements
$pair = Pair(10, 20);
$pair->forallBoth(fn($x) => $x > 5, fn($y) => $y > 15);  // true
$pair->existsBoth(fn($x) => $x > 15, fn($y) => $y > 15); // true

// Check predicate on both elements (inherited from Tuple)
$pair->forall(fn($x) => $x > 5);  // true (applies to both elements)
$pair->exists(fn($x) => $x > 15); // true (at least one element matches)
```

### Tuple-specific Utilities

```php
$tuple = Tuple("a", "b", "c", "d");

// Sequence operations
$head = $tuple->head();  // "a"
$last = $tuple->last();  // "d"
$tail = $tuple->tail();  // Tuple("b", "c", "d")
$init = $tuple->init();  // Tuple("a", "b", "c")

// Slicing
$taken = $tuple->take(2);   // Tuple("a", "b")
$dropped = $tuple->drop(2); // Tuple("c", "d")

// Searching
$tuple = Tuple(1, 2, 3, 4, 5);
$contains = $tuple->contains(3);         // true
$exists = $tuple->exists(fn($x) => $x > 3);   // true
$forall = $tuple->forall(fn($x) => $x > 0);   // true
$found = $tuple->find(fn($x) => $x > 3);      // Some(4)

// Indexed operations
$result = $tuple->mapIndexed(fn($x, $i) => "$i:$x");
// Tuple("1:a", "2:b", "3:c", "4:d")

$tuple->forEachIndexed(fn($x, $i) => echo "$i => $x\n");
```

## Copying with Modifications

Both Tuples and Pairs support creating modified copies:

```php
$pair = Pair("a", 1);
$modified = $pair->copy(["_1" => "b"]);
// Pair("b", 1)

$tuple = Tuple("a", 1, true);
$modified = $tuple->copy(["_2" => 2, "_3" => false]);
// Tuple("a", 2, false)
```

## Type Safety and Display

Tuples and Pairs maintain type information for all elements:

```php
$pair = Pair("hello", 42);
echo $pair->showType();  // "(String, Int)"
echo $pair->toString();  // "Pair(hello, 42)"

$tuple = Tuple("name", 25, true);
echo $tuple->showType();  // "(String, Int, Boolean)"
echo $tuple->toString();  // "(name, 25, true)"

$arity = $tuple->getArity();  // 3
```

## Immutability

Tuples and Pairs are immutable - attempting to modify elements throws a TypeError:

```php
$pair = Pair("hello", 42);

// These will throw TypeError
$pair->_1 = "world";     // Error: Pairs are immutable
$pair->_3 = true;        // Error: Invalid index _3 for pair
```

## Type Class Laws

### Functor Laws

```php
// Identity: fa.map(x => x) == fa
$pair->map(fn($x) => $x) === $pair

// Composition: fa.map(f).map(g) == fa.map(x => g(f(x)))
$pair->map($f)->map($g) === $pair->map(fn($x) => $g($f($x)))
```

### Bifunctor Laws (Pair only)

```php
// Identity: bimap(id, id) == id
$pair->bimap(fn($x) => $x, fn($y) => $y) === $pair

// Composition
$pair->bimap($f1, $g1)->bimap($f2, $g2) ===
    $pair->bimap(fn($x) => $f2($f1($x)), fn($y) => $g2($g1($y)))
```

### Comonad Laws (Pair only)

```php
// Left identity: extract(duplicate(wa)) == wa.extract()
$pair->duplicate()->extract() === $pair->extract()

// Right identity: map(extract)(duplicate(wa)) == wa
$pair->duplicate()->map(fn($p) => $p->extract()) === $pair

// Associativity: duplicate(duplicate(wa)) == map(duplicate)(duplicate(wa))
```

## Best Practices

1. **Use Pairs for context/value patterns**: The first element provides context, the second is the value
2. **Leverage Bifunctor for Pairs**: Use `bimap`, `first`, `second` for clean transformations
3. **Use Comonad for context-dependent computations**: Extract values while preserving context
4. **Use Tuples for heterogeneous collections**: When you need 3+ different types together
5. **Prefer Foldable operations**: Use `foldLeft`/`foldRight` over manual iteration
6. **Use swap for perspective changes**: Convert between key-value and value-key
7. **Use merge to combine Pair elements**: Clean way to reduce a Pair to a single value

## Common Patterns

### Configuration with defaults

```php
$config = Pair("default_value", userValue);
$value = $config->snd();  // Get the value
$withContext = $config->extend(fn($p) =>
    $p->snd() ?? $p->fst()  // Use default if null
);
```

### Labeled computations

```php
$computation = Pair("Step 1", 42);
$next = $computation->extend(fn($p) =>
    Pair($p->_1 . " -> Step 2", $p->_2 * 2)
);
```

### Type-safe tuples

```php
// Instead of array(mixed $a, mixed $b, mixed $c)
function processData(Tuple $data): Tuple {
    return $data->map(fn($x) => transform($x));
}
```

## Implementation Notes

- Tuples and Pairs are truly immutable
- Elements are 1-based indexed with underscore prefix (_1, _2, etc.)
- Type information is preserved for all elements
- Pairs are specialized tuples with exactly two elements
- The implementation prevents inheritance outside the package
- Pair's Functor instance maps over the second element only
- Pair's Foldable instance folds over the second element only
- Tuple's Functor instance maps over all elements
- Tuple's Foldable instance folds over all elements
