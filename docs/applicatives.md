# Applicatives

Applicative functors (or just applicatives) are a step up from regular functors, providing more powerful ways to combine computations. They allow you to apply wrapped functions to wrapped values.

## What is an Applicative?

An applicative functor is a type constructor that implements:
1. The `pure` operation to wrap a value
2. The `apply` operation to apply a wrapped function to a wrapped value

In Phunkie, applicatives are defined by the following interface:

```php
interface Applicative extends Functor {
    public static function pure($a): Applicative;
    public function apply(Applicative $f): Applicative;
}
```

## Examples with Options

Here's how applicatives work with Options:

```php
$maybeAdd = Some(fn ($x) => $x + 1);
$maybeValue = Some(41);

// Using apply to apply the wrapped function
$result = $maybeValue->apply($maybeAdd); // Some(42)

// When dealing with None
$maybeValue = None();
$result = $maybeValue->apply($maybeAdd); // None
```

## List Applicative

Lists also form an applicative functor:

```php
$fs = ImmList(
    fn ($x) => $x * 2,
    fn ($x) => $x + 1
);
$xs = ImmList(1, 2, 3);

// Applies each function to each value
$result = $xs->apply($fs); // ImmList(2, 4, 6, 2, 3, 4)
```

## Practical Uses

Applicatives are particularly useful when:
1. Combining independent computations
2. Validating multiple fields
3. Performing parallel operations

### Form Validation Example

```php
class ValidationResult implements Applicative {
    // ... implementation details ...
}

$validateName = function($name) {
    return strlen($name) > 2 
        ? Success($name) 
        : Failure("Name too short");
};

$validateAge = function($age) {
    return $age >= 18 
        ? Success($age) 
        : Failure("Must be 18 or older");
};

$person = function($name, $age) {
    return ["name" => $name, "age" => $age];
};

$result = Success($person)
    ->apply(validateName("Bob"))
    ->apply(validateAge(20));
```

## Laws

Applicatives must satisfy certain laws:

1. Identity:
```php
pure(id)->apply($v) === $v
```

2. Composition:
```php
pure(compose)apply($u)->apply($v)->apply($w) === $u->apply($v->apply($w))
```

3. Homomorphism:
```php
pure($f)->apply(pure($x)) === pure($f($x))
```

4. Interchange:
```php
$u->apply(pure($y)) === pure(function($f) use($y) { return $f($y); })->apply($u)
```

These laws ensure that applicatives behave consistently and predictably.
