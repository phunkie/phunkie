# Composability of Everything

One of the key strengths of functional programming is the ability to compose different abstractions together. Phunkie provides several ways to compose types, type classes, and functions.

## Type Class Composition

Type classes in Phunkie can be composed to build more powerful abstractions.

### Functor Composition

Functors can be composed to create new functors that work with nested types:

```php
use Phunkie\Cats\Functor\FunctorComposite;

// Compose Option and List functors
$f = new FunctorComposite(Option::kind);
$composed = $f->compose(ImmList::kind);

// Work with nested types
$data = ImmList(Some(1), None(), Some(2));
$result = $composed->map($data, fn($x) => $x + 1);
// ImmList(Some(2), None(), Some(3))
```

### Monad Composition

Monads can be composed using monad transformers:

```php
// Using OptionT to compose Option with List
$data = OptionT(ImmList(Some(1), None(), Some(2)));

// Map over the nested structure
$result = $data->map(fn($x) => $x + 1);
// OptionT(ImmList(Some(2), None(), Some(3)))

// FlatMap with nested types
$result = $data->flatMap(fn($x) => OptionT(ImmList(Some($x + 1))));
```

## Combining Different Types

Phunkie provides several ways to combine different types safely:

### Using Applicative

```php
$maybeNumber = Some(42);
$maybeString = Some("hello");

// Combine values using applicative
$combined = map2(
    fn($n, $s) => "$s: $n",
    $maybeNumber,
    $maybeString
); // Some("hello: 42")
```

### Using Traverse

```php
$numbers = ImmList(1, 2, 3);

// Convert List<A> to Option<List<A>>
$result = $numbers->traverse(fn($x) => 
    $x > 0 ? Some($x) : None()
); // Some(ImmList(1, 2, 3))

// Fails if any element fails
$result = $numbers->traverse(fn($x) => 
    $x > 2 ? Some($x) : None()
); // None
```

### Using Natural Transformations

Natural transformations allow converting between different type constructors while preserving structure:

```php
use Phunkie\Cats\NaturalTransformation;

// Convert from Option to List
$optionToList = new NaturalTransformation(
    fn($opt) => $opt->isDefined() ? 
        ImmList($opt->get()) : 
        ImmList()
);

$result = $optionToList(Some(42)); // ImmList(42)
$result = $optionToList(None()); // ImmList()
```

## Composition Patterns

### Function Composition with Effects

Compose functions that work with effects using monadic composition:

```php
$f = fn($s) => Option($s . "e");
$g = fn($s) => Option($s . "l");
$h = fn($s) => Option($s . "o");

// Compose monadic functions
$hello = mcompose($f, $g, $g, $h);

$result = $hello(Option("h")); // Some("hello")
```

### Lens Composition

Lenses can be composed to work with nested structures:

```php
use function Phunkie\Functions\lens\makeLenses;

$user = ImmMap([
    "name" => "Jack",
    "address" => ImmMap([
        "city" => "London",
        "country" => ImmMap([
            "code" => "UK"
        ])
    ])
]);

// Create and compose lenses
$lenses = makeLenses("address", "country", "code");
$codeLens = combine($lenses->address, $lenses->country, $lenses->code);

// Use composed lens
$code = $codeLens->get($user); // Some("UK")
```

### Validation Composition

Compose multiple validations using applicative:

```php
$validateName = fn($name) => 
    strlen($name) > 2 ? 
        Success($name) : 
        Failure("Name too short");

$validateAge = fn($age) => 
    $age >= 18 ? 
        Success($age) : 
        Failure("Must be 18 or older");

// Compose validations
$validatePerson = map2(
    fn($name, $age) => ["name" => $name, "age" => $age],
    $validateName("Bob"),
    $validateAge(20)
);
```

## Best Practices

1. Use type class composition for working with nested types
2. Leverage monad transformers for complex compositions
3. Use natural transformations to convert between type constructors
4. Compose lenses for working with nested data structures
5. Use applicative composition for independent operations
6. Prefer monadic composition for sequential operations

## Implementation Notes

- Functor composition maintains type safety
- Monad transformers handle the complexity of nested monadic types
- Natural transformations preserve the structure of types
- Lens composition helps manage nested immutable data
- Type class laws ensure consistent composition behavior
