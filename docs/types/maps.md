# Immutable Maps

Immutable Maps (ImmMap) in Phunkie are key-value collections that cannot be modified after creation. They provide a type-safe, functional way to work with associative data structures.

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
$map = ImmMap(["hello" => "there"]);

// Get value (returns Option)
$map->get("hello");        // Some("there")
$map->get("missing");      // None

// Check if key exists
$map->contains("hello");   // true

// Get with default value
$map->getOrElse("hello", "default");  // "there"
$map->getOrElse("missing", "default"); // "default"

// Get all keys
$map->keys();   // ["hello"]

// Get all values
$map->values(); // ["there"]
```

## Immutable Operations

Maps provide operations that return new maps without modifying the original:

```php
$map = ImmMap(["hello" => "there", "hi" => "here"]);

// Add or update entry
$newMap = $map->plus("hey", "hello");  

// Remove entry
$newMap = $map->minus("hi");

// Copy map
$copy = $map->copy();
```

## Functor Operations

ImmMap implements the Functor type class, allowing you to map over key-value pairs:

```php
$map = ImmMap(["a" => 1, "b" => 2, "c" => 3]);

// Map over key-value pairs
$result = $map->map(fn(Pair $kv) => Pair($kv->_1, $kv->_2 + 1));
// ImmMap(["a" => 2, "b" => 3, "c" => 4])

// Transform all values to a single value
$result = $map->as(Pair("a", 0));
// ImmMap(["a" => 0])

// Transform all values using wildcard
$result = $map->as(Pair(_, 0));
// ImmMap(["a" => 0, "b" => 0, "c" => 0])

// Convert all values to Unit
$result = $map->void();
// ImmMap(["a" => Unit(), "b" => Unit(), "c" => Unit()])

// Zip values with a function
$result = $map->zipWith(fn($x) => $x * 2);
// ImmMap(["a" => Pair(1, 2), "b" => Pair(2, 4), "c" => Pair(3, 6)])
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
3. Use the functor operations for bulk transformations
4. Take advantage of the type safety for more reliable code
5. Use `getOrElse()` when you need a default value

## Implementation Notes

- Maps are truly immutable - attempts to modify them directly will throw exceptions
- Keys are automatically promoted to their corresponding immutable types
- Maps can use any type as keys, including objects
- The implementation uses `SplObjectStorage` internally for efficient storage