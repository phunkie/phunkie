# Installation

## Requirements

Phunkie requires:
- PHP 8.1 or higher
- Composer

## Installation via Composer

You can install Phunkie using Composer:

```bash
composer require phunkie/phunkie
```

This will add Phunkie as a dependency to your project and install it in your vendor directory.

## Phunkie Console

Phunkie comes with an interactive console that allows you to experiment with functional programming concepts.

```bash
composer require --dev phunkie/phunkie-console
```

After installation, you can find the console executable at:

```bash
vendor/bin/phunkie-console
```

For convenience, you may want to create a symlink:

```bash
$ mkdir bin
$ ln -s vendor/bin/phunkie-console $PWD/bin/phunkie
```

## Using the Console

To start the console, simply run:

```bash
$ bin/phunkie-console
Welcome to phunkie console.

Type in expressions to have them evaluated.

phunkie >
```

The console provides an interactive REPL (Read-Eval-Print Loop) where you can:
- Create and manipulate Phunkie data types
- Test functional programming concepts
- Experiment with type signatures
- Check kinds of types

Some useful console commands:
- `:help` - Display help information
- `:type <expr>` - Show the type of an expression
- `:kind <type>` - Show the kind of a type
- `:load <module>` - Loads a phunkie module
- `:quit` - Exit the console

Example console session:
```php
Welcome to Phunkie console
Type in expressions to have them evaluated.

phunkie> Some(42)
$var0: Option<Int> = Some(42)

phunkie> :type Some(42)
Option<Int>

phunkie> None()
$var1: None

phunkie> ImmList(1,2,3)
$var2: List<Int> = List(1,2,3)
``` 