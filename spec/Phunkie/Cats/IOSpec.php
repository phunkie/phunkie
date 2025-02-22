<?php

namespace spec\Phunkie\Cats;

use Phunkie\Cats\IO;
use Md\Unit\TestCase;

class IOSpec extends TestCase
{
    /**
     * @test
     */
    public function it_delays_execution_until_run()
    {
        $executed = false;
        $io = new IO(function() use (&$executed) {
            $executed = true;
            return 42;
        });

        $this->assertFalse($executed);
        $this->assertEquals(42, $io->run());
        $this->assertTrue($executed);
    }

    /**
     * @test
     */
    public function it_implements_map()
    {
        $io = new IO(fn() => 21);
        $doubled = $io->map(fn($x) => $x * 2);
        
        $this->assertEquals(42, $doubled->run());
    }

    /**
     * @test
     */
    public function it_implements_flatMap()
    {
        $io = new IO(fn() => 21);
        $result = $io->flatMap(fn($x) => new IO(fn() => $x * 2));
        
        $this->assertEquals(42, $result->run());
    }

    /**
     * @test
     */
    public function it_sequences_operations()
    {
        $operations = [];
        
        $first = new IO(function() use (&$operations) {
            $operations[] = 'first';
            return 1;
        });
        
        $second = new IO(function() use (&$operations) {
            $operations[] = 'second';
            return 2;
        });

        $combined = $first->flatMap(fn($x) => 
            $second->map(fn($y) => $x + $y)
        );

        $this->assertEquals(3, $combined->run());
        $this->assertEquals(['first', 'second'], $operations);
    }

    /**
     * @test
     */
    public function it_maintains_referential_transparency()
    {
        $counter = 0;
        $io = new IO(function() use (&$counter) {
            $counter++;
            return $counter;
        });

        // The IO action itself is pure - creating it doesn't increment counter
        $this->assertEquals(0, $counter);

        // Each run executes the effect
        $this->assertEquals(1, $io->run());
        $this->assertEquals(2, $io->run());
        $this->assertEquals(3, $io->run());
    }
} 