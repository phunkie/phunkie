# Unit

Unit in Phunkie represents a type with exactly one value, similar to `void` in other languages but as a proper type. It's often used to represent the absence of a meaningful value while maintaining type safety.

Unit is a singleton type where all instances are equal, and it forms a trivial monoid where combining any two Units always produces Unit.

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

## Type Class Operations

### Equality (Eq)

All Unit values are equal to each other, as Unit is a singleton type:

```php
$unit1 = Unit();
$unit2 = Unit();

$unit1->eqv($unit2); // true - all Units are equal
```

The equality satisfies all the Eq laws:
- **Reflexivity**: `x->eqv(x)` is always true
- **Symmetry**: `x->eqv(y)` equals `y->eqv(x)`
- **Transitivity**: if `x->eqv(y)` and `y->eqv(z)`, then `x->eqv(z)`

### Monoid Operations

Unit forms a trivial monoid where:
- The identity element is Unit itself
- Combining any two Units always produces Unit

```php
$unit = Unit();

// Get the zero (identity) element
$zero = $unit->zero(); // Unit()

// Combine two Units
$combined = $unit->combine(Unit()); // Unit()

// Monoid laws are satisfied:
// Left identity: zero()->combine(x) == x
$unit->zero()->combine($unit)->eqv($unit); // true

// Right identity: x->combine(zero()) == x
$unit->combine($unit->zero())->eqv($unit); // true

// Associativity: (a->combine(b))->combine(c) == a->combine(b->combine(c))
$a = Unit();
$b = Unit();
$c = Unit();
$a->combine($b)->combine($c)->eqv($a->combine($b->combine($c))); // true
```

## Use Cases

Unit is useful in several scenarios:

1. **As a return type for side-effecting functions**:
```php
function log($message) {
    echo $message . "\n";
    return Unit();
}
```

2. **As a placeholder in generic types that require a type parameter**:
```php
Option(Unit()); // Some(Unit) or None
```

3. **To represent empty or void cases in sum types**:
```php
Either(Unit(), $value); // Left(Unit) or Right($value)
```

4. **As the identity element in monoid operations**:
```php
// Unit is useful when you need a neutral element
$result = $list->foldLeft(Unit(), fn($acc, $x) => {
    performSideEffect($x);
    return Unit();
});
```

## Best Practices

1. Use Unit instead of `null` or `void` when you need to represent the absence of a value
2. Remember that Unit is a singleton - all Unit values are equal
3. Use Unit in functional constructs that require a type but don't need a value
4. Don't try to access or modify Unit members - it has none
5. Leverage Unit's monoid properties when you need a trivial combining operation

## Implementation Notes

- Unit extends Tuple but overrides all operations
- Unit is final and cannot be extended
- Unit has no members or properties
- All attempts to access or modify Unit throw errors
- Unit has a unique string representation "()"
- Unit implements Eq and forms a Monoid
- All Unit values are structurally equal

