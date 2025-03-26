<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    public function testArrayContainsValue(): void
    {
        $array = [1, 2, 3];
        $this->assertContains(2, $array);
    }

    public function testStringContainsSubstring(): void
    {
        $string = "Hello World";
        $this->assertStringContainsString("World", $string);
    }

    public function testExceptionIsThrown(): void
    {
        $this->expectException(InvalidArgumentException::class);
        throw new InvalidArgumentException("Test exception");
    }

    public function testTypeCheck(): void
    {
        $value = 42;
        $this->assertIsInt($value);
    }
}
