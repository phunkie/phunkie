# Immutable Maps

Immutable Maps (ImmMap) in Phunkie are key-value collections that cannot be modified after creation. They provide a type-safe, functional way to work with associative data structures.

ImmMap implements several type class interfaces:
- **Functor**: Transform values while preserving structure
- **Foldable**: Fold/reduce maps to single values
- **Traverse**: Sequence effects through maps
- **Monoid** (via ops): Combine maps with empty map as identity

## Creating Maps

There are several ways to create immutable maps:

```php
// Empty map
$empty = ImmMap();

// From associative array
$map = ImmMap(["hello" => "there"]);

// From key-value pairs
$map = ImmMap(
    "key1", "value1",
    "key2", "value2"
);

// Using objects as keys
$map = ImmMap(
    new AccountNumber(1), new Account("John Smith"),
    new AccountNumber(2), new Account("Chuck Norris")
);
```

## Basic Operations

Maps provide several basic operations for accessing and querying data:

```php
$map = ImmMap(["hello" => "there", "hi" => "here"]);

// Get value (returns Option)
$map->get("hello");        // Some("there")
$map->get("missing");      // None

// Check if key exists
$map->contains("hello");   // true

// Get with default value
$map->getOrElse("hello", "default");  // "there"
$map->getOrElse("missing", "default"); // "default"

// Get all keys
$map->keys();   // ["hello", "hi"]

// Get all values
$map->values(); // ["there", "here"]

// Check if empty
$map->isEmpty(); // false
ImmMap()->isEmpty(); // true

// Get size
$map->size(); // 2
```

## Immutable Operations

Maps provide operations that return new maps without modifying the original:

```php
$map = ImmMap(["hello" => "there", "hi" => "here"]);

// Add or update entry
$newMap = $map->plus("hey", "hello");
$updated = $map->updated("hello", "world"); // Alias for plus

// Remove entry
$newMap = $map->minus("hi");
$removed = $map->removed("hi"); // Alias for minus

// Copy map
$copy = $map->copy();

// Copy with updates
$modified = $map->copy(["hi" => "world"]);
```

## Functor Operations

ImmMap implements the Functor type class, allowing you to map over key-value pairs:

```php
$map = ImmMap(["a" => 1, "b" => 2, "c" => 3]);

// Map over key-value pairs (receives Pair)
$result = $map->map(fn(Pair $kv) => Pair($kv->_1, $kv->_2 + 1));
// ImmMap(["a" => 2, "b" => 3, "c" => 4])

// Map over values only
$result = $map->mapValues(fn($v) => $v * 2);
// ImmMap(["a" => 2, "b" => 4, "c" => 6])

// Map over keys only
$result = ImmMap([1 => "a", 2 => "b"])->mapKeys(fn($k) => "key_" . $k);
// ImmMap(["key_1" => "a", "key_2" => "b"])

// Map with both key and value
$result = $map->mapWithKey(fn($k, $v) => "$k:$v");
// ImmMap(["a" => "a:1", "b" => "b:2", "c" => "c:3"])

// Transform all values to a single value
$result = $map->as(Pair("a", 0));
// ImmMap(["a" => 0])

// Transform all values using wildcard
$result = $map->as(Pair(_, 0));
// ImmMap(["a" => 0, "b" => 0, "c" => 0])

// Convert all values to Unit
$result = $map->void();
// ImmMap(["a" => Unit(), "b" => Unit(), "c" => Unit()])

// Zip values with their transformation
$result = $map->zipWith(fn($x) => $x * 2);
// ImmMap(["a" => Pair(1, 2), "b" => Pair(2, 4), "c" => Pair(3, 6)])
```

## Foldable Operations

ImmMap implements Foldable, allowing you to reduce maps to single values:

```php
$map = ImmMap(["a" => 1, "b" => 2, "c" => 3]);

// Fold left (left-to-right)
$sum = $map->foldLeft(0)(fn($acc, $pair) => $acc + $pair->_2);
// 6

// Fold right (right-to-left)
$sum = $map->foldRight(0)(fn($pair, $acc) => $acc + $pair->_2);
// 6

// Fold with initial value
$result = $map->fold(0)(fn($acc, $pair) => $acc + $pair->_2);
// 6

// Fold map - map elements to a monoid and combine
$lists = $map->foldMap(fn($pair) => ImmList($pair->_2));
// ImmList(1, 2, 3)
```

## Traversable Operations

ImmMap implements Traverse for sequencing effects:

```php
// Sequence - turn a map of Options into an Option of map
$map = ImmMap(["a" => Some(1), "b" => Some(2)]);
$result = $map->sequence();
// Some(ImmMap(["a" => 1, "b" => 2]))

$map2 = ImmMap(["a" => Some(1), "b" => None()]);
$result2 = $map2->sequence();
// None()

// Traverse - map and sequence in one operation
$map = ImmMap(["a" => 1, "b" => 2]);
$result = $map->traverse(fn($pair) => Some($pair->_2 * 2));
// Some(ImmMap(["a" => 2, "b" => 4]))
```

## Filter Operations

Filter maps based on predicates:

```php
$map = ImmMap(["a" => 1, "b" => 2, "c" => 3, "d" => 4]);

// Filter by key-value pair
$evens = $map->filter(fn($pair) => $pair->_2 % 2 === 0);
// ImmMap(["b" => 2, "d" => 4])

// Filter by keys only
$result = $map->filterKeys(fn($k) => $k !== "b");
// ImmMap(["a" => 1, "c" => 3, "d" => 4])

// Filter by values only
$result = $map->filterValues(fn($v) => $v > 2);
// ImmMap(["c" => 3, "d" => 4])
```

## Monoid Operations

ImmMap has monoid operations for combining maps:

```php
$map1 = ImmMap(["a" => 1, "b" => 2]);
$map2 = ImmMap(["b" => 3, "c" => 4]);

// Identity element (empty map)
$empty = $map1->zero();
// ImmMap()

// Combine maps (right map values take precedence)
$combined = $map1->combine($map2);
// ImmMap(["a" => 1, "b" => 3, "c" => 4])

// Monoid laws
$map1->zero()->combine($map1)->eqv($map1);        // true (left identity)
$map1->combine($map1->zero())->eqv($map1);        // true (right identity)
```

## Advanced Operations

### FlatMap

Map a function that returns a map, then flatten:

```php
$map = ImmMap(["a" => 1, "b" => 2]);
$result = $map->flatMap(fn($pair) => ImmMap([
    $pair->_1 . "1" => $pair->_2,
    $pair->_1 . "2" => $pair->_2 * 2
]));
// ImmMap(["a1" => 1, "a2" => 2, "b1" => 2, "b2" => 4])
```

### Zip

Combine two maps by matching keys:

```php
$map1 = ImmMap(["a" => 1, "b" => 2, "c" => 3]);
$map2 = ImmMap(["a" => "x", "b" => "y", "d" => "z"]);

// Zip into pairs
$result = $map1->zip($map2);
// ImmMap(["a" => Pair(1, "x"), "b" => Pair(2, "y")])

// Zip with a combining function
$result = $map1->zipWithMap($map2, fn($v1, $v2) => "$v1-$v2");
// ImmMap(["a" => "1-x", "b" => "2-y"])
```

### Partition

Split a map based on a predicate:

```php
$map = ImmMap(["a" => 1, "b" => 2, "c" => 3, "d" => 4]);
$partitioned = $map->partition(fn($pair) => $pair->_2 % 2 === 0);
// Pair(ImmMap(["b" => 2, "d" => 4]), ImmMap(["a" => 1, "c" => 3]))

$evens = $partitioned->_1;   // ImmMap(["b" => 2, "d" => 4])
$odds = $partitioned->_2;    // ImmMap(["a" => 1, "c" => 3])
```

### GroupBy

Group map entries by a computed key:

```php
$map = ImmMap(["a" => 1, "b" => 2, "c" => 1, "d" => 3]);
$grouped = $map->groupBy(fn($pair) => $pair->_2);
// ImmMap(
//   1 => ImmList(Pair("a", 1), Pair("c", 1)),
//   2 => ImmList(Pair("b", 2)),
//   3 => ImmList(Pair("d", 3))
// )
```

### Convert to List

Convert map to a list of key-value pairs:

```php
$map = ImmMap(["a" => 1, "b" => 2]);
$list = $map->toList();
// ImmList(Pair("a", 1), Pair("b", 2))
```

## Type Safety

Maps in Phunkie maintain type information for both keys and values:

```php
// Type information is preserved
$map = ImmMap(["a" => 1]);
$map->getTypeVariables();  // [String, Integer]
$map->getTypeArity();      // 2
```

## String Representation

Maps have a clear string representation for debugging:

```php
$map = ImmMap(["hi" => "here", "hello" => "there"]);
echo $map->toString();  // Map("hi" -> "here", "hello" -> "there")
```

## Best Practices

1. Use ImmMap when you need a key-value structure that won't change
2. Leverage the Option return type from `get()` for safe value access
3. Use `mapValues()` when you only need to transform values (more efficient than `map()`)
4. Use foldable operations for aggregations
5. Use `sequence()` when working with maps of effects (Option, Either, etc.)
6. Take advantage of the type safety for more reliable code
7. Use `getOrElse()` when you need a default value
8. Use filter operations for conditional transformations
9. Use monoid operations to merge multiple maps

## Implementation Notes

- Maps are truly immutable - attempts to modify them directly will throw exceptions
- Keys are automatically promoted to their corresponding immutable types
- Maps can use any type as keys, including objects
- The implementation uses `SplObjectStorage` internally for efficient storage
- All operations that return new maps maintain full immutability
- Filter, map, and fold operations work on `Pair<K,V>` objects

## Comparison with Other Structures

| Feature | ImmMap | ImmList | ImmSet |
|---------|--------|---------|--------|
| Key-value pairs | Yes | No | No |
| Ordered | By insertion | Yes | No |
| Duplicates | No (keys) | Yes | No |
| Lookup | O(1) | O(n) | O(1) |
| Best for | Dictionaries | Sequences | Unique items |
