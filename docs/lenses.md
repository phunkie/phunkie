# Lenses

Lenses are a functional programming abstraction that provides a way to focus on a specific part of a data structure and perform operations on it. In Phunkie, lenses offer a composable way to view and modify nested data structures.

## What is a Lens?

A lens consists of two functions:
- A getter that focuses on a part of a larger structure
- A setter that updates that focused part within the larger structure

```php
class Lens {
    public function get($a);        // Gets the focused value
    public function set($b, $a);    // Sets a new value
    public function mod($f, $a);    // Modifies the focused value using a function
}
```

## Core Operations

### get
Retrieves the focused value:
```php
$userNameLens = new Lens(
    fn(User $user) => $user->getName(),
    fn(Name $name, User $user) => $user->copy(["name" => $name])
);
$name = $userNameLens->get($user); // Gets user's name
```

### set
Updates the focused value:
```php
$newUser = $userNameLens->set(new Name("Chuck Norris"), $user);
```

### mod
Modifies the focused value using a function:
```php
$upperCaseName = $userNameLens->mod(
    fn(Name $name) => new Name(strtoupper($name->getName())), 
    $user
);
```

## Lens Composition

Lenses can be composed to focus on deeply nested structures:

```php
$lenses = makeLenses("address", "country", "code");
$codeLens = combine($lenses->address, $lenses->country, $lenses->code);

$countryCode = $codeLens->get($user); // Gets nested country code
```

## Lens Laws

Lenses must satisfy three fundamental laws:

1. Identity Law: Getting and then setting back the same value changes nothing
```php
$lens->set($lens->get($a), $a) === $a
```

2. Retention Law: Setting a value and then getting it returns the value that was set
```php
$lens->get($lens->set($b, $a)) === $b
```

3. Double Set Law: Setting twice is the same as setting once
```php
$lens->set($c, $lens->set($b, $a)) === $lens->set($c, $a)
```

## Built-in Lenses

Phunkie provides several built-in lens constructors:

- `trivial()`: A lens that focuses on nothing
- `self()`: A lens that focuses on the whole structure
- `fst()`: A lens that focuses on the first element of a Pair
- `snd()`: A lens that focuses on the second element of a Pair
- `contains()`: A lens for checking/updating Set membership
- `member()`: A lens for accessing/modifying Map values
- `makeLenses()`: Creates lenses for object properties or array keys

## Common Use Cases

1. Object Property Access
```php
$lenses = makeLenses("name");
$userName = $lenses->name->get($user);
```

2. Nested Data Structures
```php
$lenses = makeLenses("address", "street");
$streetLens = combine($lenses->address, $lenses->street);
```

3. Collection Manipulation
```php
$mapLens = member("key");
$value = $mapLens->get($immMap);
```

## Best Practices

1. Use lenses for complex data structures
2. Compose lenses for deep nesting
3. Verify lens laws for custom implementations
4. Use `makeLenses()` for common property access
5. Leverage lens composition for reusability

## Implementation Notes

- Lenses are immutable
- They preserve data structure integrity
- Support for various Phunkie types (ImmMap, Pair, etc.)
- Custom lens creation via class extension
- Composition maintains type safety
