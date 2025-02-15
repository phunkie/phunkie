# IO Monad

The IO monad represents computations that may perform input/output operations. In Phunkie, it provides a way to handle side effects in a pure functional manner.

## What is IO Monad?

IO is a monad that wraps computations which interact with the outside world. It allows you to:
- Separate pure and impure code
- Compose IO operations
- Delay execution until needed

```php
abstract class IO {
    abstract public function run();
}
```

## Creating IO Actions

Use the `io` function to create IO actions:

```php
use function Phunkie\Functions\io\io;

// Create an IO that reads from stdin
$readLine = io(fn() => fgets(STDIN));

// Create an IO that writes to stdout
$writeLine = io(fn() => fwrite(STDOUT, "Hello, World!\n"));
```

## Core Operations

### run
Executes the IO action:
```php
$io = io(fn() => "Hello, World!");
$result = $io->run(); // "Hello, World!"
```

### map
Transform the result of an IO action:
```php
$readAndUpper = io(fn($input) => strtoupper($input));
```

### flatMap
Chain IO actions:
```php
$readAndEcho = io(fn($input) => 
    io(fn() => fwrite(STDOUT, $input))
);
```

### andThen
Sequence IO actions:
```php
$greet = io(fn() => fwrite(STDOUT, "What's your name?\n"))
    ->andThen(io(fn($name) => "Hello, " . trim($name) . "!\n"))
    ->andThen(fn($greeting) => 
        io(fn() => fwrite(STDOUT, $greeting))
    );
```

## Common Use Cases

### 1. File Operations
```php
$readFile = io(fn() => file_get_contents("input.txt"));
$writeFile = fn($content) => io(
    fn() => file_put_contents("output.txt", $content)
);

$copyFile = $readFile->flatMap($writeFile);
```

### 2. Database Operations
```php
$query = io(fn() => $db->query("SELECT * FROM users"));
$process = fn($results) => io(
    fn() => array_map(fn($row) => $row['name'], $results)
);

$getNames = $query->flatMap($process);
```

### 3. Network Requests
```php
$fetch = io(fn() => file_get_contents("https://api.example.com"));
$parse = fn($response) => io(
    fn() => json_decode($response, true)
);

$getData = $fetch->flatMap($parse);
```

## Best Practices

1. Use IO for all side effects
2. Keep IO actions small and focused
3. Compose complex operations from simple ones
4. Delay execution until necessary
5. Handle errors appropriately

## Implementation Notes

- IO actions are lazy - they only execute when run() is called
- The implementation supports composition through map and flatMap
- andThen allows simple sequencing of actions
- All operations preserve referential transparency
- Error handling should be considered in the wrapped functions

## Common Pitfalls

1. Running IO actions too early
2. Not handling potential errors
3. Mixing pure and impure code
4. Complex nested IO operations
5. Side effects outside of IO

## Type Safety

IO operations maintain type safety through PHP's type system:

```php
// Type-safe IO chain
$getNumber = io(fn(): int => intval(fgets(STDIN)))
    ->map(fn(int $n): string => "Got number: $n")
    ->flatMap(fn(string $msg): IO => 
        io(fn() => fwrite(STDOUT, $msg))
    );
```

## Writing IO Operations

Here's how to write your own IO operations:

```php
class ReadFile extends IO
{
    private $filename;
    
    public function __construct(string $filename)
    {
        $this->filename = $filename;
    }
    
    public function run()
    {
        return file_get_contents($this->filename);
    }
}

class WriteFile extends IO
{
    private $filename;
    private $content;
    
    public function __construct(string $filename, string $content)
    {
        $this->filename = $filename;
        $this->content = $content;
    }
    
    public function run()
    {
        return file_put_contents($this->filename, $this->content);
    }
}
```

## Creating a Main Program

Here's how to structure a program that uses IO:

```php
class Program
{
    public static function main(array $argv): IO
    {
        // Parse command line arguments
        $filename = $argv[1] ?? "default.txt";
        
        // Create the program flow
        return io(fn() => "Reading from: $filename\n")
            ->flatMap(fn($msg) => io(fn() => fwrite(STDOUT, $msg)))
            ->flatMap(fn($_) => new ReadFile($filename))
            ->flatMap(fn($content) => 
                io(fn() => fwrite(STDOUT, "Content: $content\n"))
            )
            ->map(fn($_) => "Operation completed successfully!");
    }
}

// Usage in script.php:
$program = Program::main($argv);
$result = $program->run();
echo $result . PHP_EOL;
```

A more complex example with error handling:

```php
class Program
{
    public static function main(array $argv): IO
    {
        return io(function() use ($argv) {
            return match (true) {
                isset($argv[1], $argv[2]) => Pair($argv[1], $argv[2]),
                default => Left("Usage: php script.php <input-file> <output-file>")
            };
        })
        ->flatMap(fn($result) => 
            Either($result)
                ->fold(
                    fn($error) => io(fn() => throw new \InvalidArgumentException($error)),
                    fn(Pair $files) => self::processFiles($files->_1, $files->_2)
                )
        )
        ->flatMap(fn($args) => 
            self::processFiles($args[1], $args[2])
        )
        ->map(fn($stats) => 
            "Processed {$stats['bytes']} bytes successfully!"
        );
    }
    
    private static function processFiles(
        string $input, 
        string $output
    ): IO
    {
        return new ReadFile($input)
            ->flatMap(fn($content) => 
                new WriteFile($output, strtoupper($content))
            )
            ->map(fn($_) => [
                'bytes' => filesize($output)
            ]);
    }
}

// Usage with error handling:
Attempt(() => {
    $program = Program::main($argv);
    $result = $program->run();
    return $result;
})
->map(fn($result) => $result . PHP_EOL)
->fold(
    fn($error) => fwrite(STDERR, "Error: " . $error->getMessage() . PHP_EOL),
    fn($success) => fwrite(STDOUT, $success)
);
```

This structure provides several benefits:
1. Clear separation of IO operations
2. Composable program flow
3. Delayed execution until `run()`
4. Proper error handling
5. Type-safe operations
