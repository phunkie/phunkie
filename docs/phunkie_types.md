# Phunkie Types

Phunkie provides a rich set of functional data types inspired by Scala and Haskell. These types are immutable and implement various type classes for functional operations.

## Core Types

### Option

Represents optional values - a value that may or may not exist:

```php
// Some represents presence of a value
$some = Some(42);
$result = $some->map(fn($x) => $x * 2); // Some(84)

// None represents absence
$none = None();
$result = $none->map(fn($x) => $x * 2); // None
```

### ImmList

An immutable linked list:

```php
// Create a list
$list = ImmList(1, 2, 3);

// Empty list
$empty = Nil();

// List operations
$head = $list->head; // 1
$tail = $list->tail; // ImmList(2, 3)
$mapped = $list->map(fn($x) => $x * 2); // ImmList(2, 4, 6)
```

### Function1

Represents a single-argument function with composition capabilities:

```php
$f = Function1(fn($x) => $x + 1);
$g = Function1(fn($x) => $x * 2);

// Function composition
$h = $f->andThen($g); // x + 1, then * 2
$result = $h(5); // 12

// Alternative composition
$h = $g->compose($f); // Same as above
```

### ImmMap

An immutable key-value map:

```php
// Create a map
$map = ImmMap(["hello" => "world"]);

// Access values
$value = $map->get("hello"); // Some("world")
$missing = $map->get("missing"); // None

// Transform values
$upper = $map->map(fn($v) => strtoupper($v));
```

### ImmSet

An immutable set of unique values:

```php
// Create a set
$set = ImmSet(1, 2, 2, 3); // Duplicates removed

// Set operations
$union = $set->union(ImmSet(3, 4)); // ImmSet(1, 2, 3, 4)
$intersect = $set->intersect(ImmSet(2, 3)); // ImmSet(2, 3)
```

### Tuple and Pair

Fixed-size heterogeneous collections:

```php
// Pair (2-tuple)
$pair = Pair("hello", 42);
$first = $pair->_1; // "hello"
$second = $pair->_2; // 42

// Larger tuples
$tuple = Tuple("a", 1, true);
$values = [$tuple->_1, $tuple->_2, $tuple->_3]; // ["a", 1, true]
```

### Unit

Represents the absence of a specific value (similar to void):

```php
$unit = Unit();
echo $unit->toString(); // "()"
```

### Validation

For handling validation results with error accumulation:

```php
// Success case
$success = Success("Valid value");

// Failure case with error
$failure = Failure("Invalid input");

// Combine validations
$result = map2(
    fn($name, $age) => ["name" => $name, "age" => $age],
    validateName("Bob"),
    validateAge(20)
);
```

## Type Class Implementations

Most Phunkie types implement the following type classes:

- Functor (map)
- Applicative (pure, apply)
- Monad (flatMap)
- Foldable (fold, foldLeft, foldRight)
- Show (toString)
- Eq (equality comparison)

## Working with Types

### Pattern Matching

```php
$result = match(true) {
    $on(Some($x)) => "Got $x",
    $on(None()) => "Nothing",
    $on(ImmList($head, ...$tail)) => "List starting with $head",
};
```

### Type Safety

Phunkie types maintain type information:

```php
$option = Some(42);
echo $option->showType(); // "Option<Int>"

$list = ImmList(1, 2, 3);
echo $list->showType(); // "List<Int>"
```

## Best Practices

1. Use Option instead of null values
2. Prefer immutable collections (ImmList, ImmMap, ImmSet)
3. Leverage type class implementations for consistent behavior
4. Use pattern matching for safe value extraction
5. Take advantage of composition capabilities

## Implementation Notes

- All types are truly immutable
- Type class laws are enforced
- Pattern matching is type-safe
- Composition is supported at multiple levels
- Performance considerations are balanced with functional purity
