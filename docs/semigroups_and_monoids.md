# Semigroups and Monoids

Semigroups and monoids are algebraic structures that provide a way to combine values. Phunkie implements these concepts to enable consistent and predictable value composition.

## Semigroups

A semigroup is a type that can be combined with another value of the same type using an associative operation.

### The Combine Operation

Phunkie provides a `combine` function that works with various types:

```php
use function Phunkie\Functions\semigroup\combine;

// Numbers
combine(1, 2);                  // 3
combine(1.5, 2.5);             // 4.0

// Strings
combine("Hello ", "World");     // "Hello World"

// Booleans
combine(true, false);           // false (logical AND)

// Arrays
combine([1, 2], [3, 4]);       // [1, 2, 3, 4]

// Multiple values
combine(1, 2, 3, 4);           // 10
```

### Semigroup Laws

The combine operation must satisfy the associative law:

```php
combine(combine($a, $b), $c) === combine($a, combine($b, $c))
```

## Monoids

A monoid extends the semigroup concept by adding an identity element (zero). In Phunkie, this is implemented through the `zero` function.

### Zero Values

```php
use function Phunkie\Functions\semigroup\zero;

zero(0);          // 0
zero(0.0);        // 0.0
zero("");         // ""
zero(true);       // true
zero([]);         // []
```

### Monoid Laws

Monoids must satisfy three laws:

1. Left Identity:
```php
combine(zero($a), $a) === $a
```

2. Right Identity:
```php
combine($a, zero($a)) === $a
```

3. Associativity (inherited from Semigroup):
```php
combine(combine($a, $b), $c) === combine($a, combine($b, $c))
```

## Built-in Implementations

### ImmList Monoid

ImmLists form a monoid under concatenation:

```php
use Phunkie\Types\ImmList;

$list1 = ImmList(1, 2);
$list2 = ImmList(3, 4);

// Zero
$list1->zero();               // Nil()

// Combine
$list1->combine($list2);      // ImmList(1, 2, 3, 4)
```

### Option Monoid

Options form a monoid when their contents can be combined:

```php
use Phunkie\Types\Option;

$opt1 = Some(1);
$opt2 = Some(2);
$none = None();

// Zero
$opt1->zero();               // None()

// Combine
$opt1->combine($opt2);       // Some(3)
$opt1->combine($none);       // Some(1)
$none->combine($opt1);       // Some(1)
```

## Working with Custom Types

To make your type a semigroup, implement the combine method:

```php
class MyType {
    public function combine($other): self {
        // Implement combining logic here
        return new self(/* combined result */);
    }
}
```

To make it a monoid, also implement the zero method:

```php
class MyType {
    public function zero() {
        // Return identity element
        return new self(/* identity value */);
    }
    
    public function combine($other): self {
        // Implement combining logic here
    }
}
```

## Best Practices

1. Use `combine()` for consistent value composition
2. Ensure your implementations satisfy the semigroup/monoid laws
3. Use `zero()` when you need an identity element
4. Leverage built-in implementations for standard types
5. Test custom implementations against the laws using `MonoidLaws` and `SemigroupLaws` traits

## Implementation Notes

- The `combine` function handles type checking and dispatching
- Unit values are treated as identity elements in combination
- Type errors are thrown for incompatible combinations
- Custom types can implement their own `combine` and `zero` methods
- The implementation supports both value types and objects
