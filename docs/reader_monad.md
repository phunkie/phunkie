# Reader Monad

The Reader monad represents computations that depend on some environment or configuration. It provides a way to compose operations that read from a shared context.

## What is Reader Monad?

A Reader is a function that takes some environment `A` and produces a result `B`. In Phunkie, it's implemented as `Reader<A, B>`.

```php
class Reader {
    public function __construct(callable $run) {
        // run: A -> B
    }
}
```

## Creating Readers

### Basic Reader
```php
use function Phunkie\Functions\reader\Reader;

$getConfig = Reader(fn(array $config) => $config['database']);
$result = $getConfig->run(['database' => 'mysql']); // 'mysql'
```

### Using ask
The `ask` function creates a Reader that returns the environment itself:
```php
use function Phunkie\Functions\reader\ask;

$reader = ask();
$result = $reader->run('environment'); // 'environment'
```

## Core Operations

### run
Executes the Reader with a given environment:
```php
$reader = Reader(fn($x) => $x * 2);
$result = $reader->run(21); // 42
```

### map
Transform the result while keeping the environment:
```php
$reader = Reader(fn(string $s) => strrev($s))
    ->map(fn(string $s) => strtoupper($s));

$result = $reader->run("hello"); // "OLLEH"
```

### flatMap
Chain Readers together:
```php
$getHost = Reader(fn($config) => $config['host']);
$getPort = Reader(fn($config) => $config['port']);

$getAddress = $getHost->flatMap(
    fn($host) => $getPort->map(
        fn($port) => "$host:$port"
    )
);

$config = ['host' => 'localhost', 'port' => 8080];
$result = $getAddress->run($config); // "localhost:8080"
```

### andThen
Compose Readers sequentially:
```php
$first = Reader(fn($x) => strrev($x));
$second = Reader(fn($x) => strtoupper($x));

$combined = $first->andThen($second);
$result = $combined->run("hello"); // "OLLEH"
```

## Kleisli Composition

Kleisli arrows (ReaderT) allow composition of functions that return monadic values:

```php
use Phunkie\Cats\Kleisli;
use function Phunkie\Functions\kleisli\kleisli;

$validateUser = kleisli(fn($input) => 
    Option(strlen($input) >= 3 ? $input : null)
);

$validateEmail = kleisli(fn($input) => 
    Option(filter_var($input, FILTER_VALIDATE_EMAIL) ? $input : null)
);

$validate = $validateUser->andThen($validateEmail);
$result = $validate->run("invalid"); // None
$result = $validate->run("user@example.com"); // Some("user@example.com")
```

## Common Use Cases

### 1. Configuration Management
```php
$getDatabaseConfig = Reader(fn($config) => $config['database']);
$getConnection = $getDatabaseConfig->map(fn($dbConfig) => 
    new DatabaseConnection($dbConfig)
);
```

### 2. Dependency Injection
```php
$getLogger = Reader(fn($container) => $container->get('logger'));
$logError = $getLogger->map(fn($logger) => 
    $logger->error("Something went wrong")
);
```

### 3. Environment-Dependent Operations
```php
$isDevelopment = Reader(fn($env) => $env['APP_ENV'] === 'development');
$getDebugInfo = $isDevelopment->flatMap(fn($isDev) => 
    Reader(fn($env) => $isDev ? $env['debug_info'] : null)
);
```

## Best Practices

1. Use Reader for environment-dependent computations
2. Compose complex operations from simple ones
3. Keep the environment immutable
4. Use Kleisli for monadic composition
5. Consider Reader for dependency injection

## Implementation Notes

- Reader is implemented as a callable that produces a value
- All operations preserve the environment
- Kleisli provides monadic composition
- The implementation supports type-safe operations
- Reader operations are lazy until run
