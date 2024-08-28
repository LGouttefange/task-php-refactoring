<?php

namespace Tests;

use App\Specification\IsEuropeanAlphaCode;
use PHPUnit\Framework\TestCase;

class IsEuropeanAlphaCodeTest extends TestCase
{
    private IsEuropeanAlphaCode $isEuropeanAlphaCode;

    protected function setUp(): void
    {
        $this->isEuropeanAlphaCode = new IsEuropeanAlphaCode(); 
    }

    public function test_lithuania_is_in_eu()
    {
        $this->assertTrue($this->isEuropeanAlphaCode->isSatisfiedBy('LT'));
    }
    public function test_great_britain_is_not_in_eu()
    {
        $this->assertFalse($this->isEuropeanAlphaCode->isSatisfiedBy('GB'));
    }
}
