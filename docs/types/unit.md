# Unit

Unit in Phunkie represents a type with exactly one value, similar to `void` in other languages but as a proper type. It's often used to represent the absence of a meaningful value while maintaining type safety.

## Creating Unit

There's only one way to create a Unit value, as it only has one possible value:

```php
// Create a Unit value
$unit = Unit();
// Unit is also an empty tuple
$unit = Tuple(); // same as Unit()
```

## Properties

Unit has several special properties:

```php
$unit = Unit();

// String representation
echo $unit->toString(); // "()"

// Type representation
echo $unit->showType(); // "Unit"

// Cannot access members
$unit->_1; // Throws Error: _1 is not a member of Unit

// Cannot modify
$unit->_1 = 42; // Throws Error: _1 is not a member of Unit

// Cannot copy
$unit->copy(['1' => 42]); // Throws Error: copy is not a member of Unit
```

## Use Cases

Unit is useful in several scenarios:

1. As a return type for functions that don't return meaningful values
2. As a placeholder in generic types that require a type parameter
3. To represent empty or void cases in sum types
4. As a neutral element in certain algebraic structures

## Best Practices

1. Use Unit instead of `null` or `void` when you need to represent the absence of a value
2. Remember that Unit is a singleton - all Unit values are equal
3. Use Unit in functional constructs that require a type but don't need a value
4. Don't try to access or modify Unit members - it has none

## Implementation Notes

- Unit extends Tuple but overrides all operations
- Unit is final and cannot be extended
- Unit has no members or properties
- All attempts to access or modify Unit throw errors
- Unit has a unique string representation "()"

