<?php

namespace Saefy\Quars\Tests;

class ExampleTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test that true does in fact equal true
     */
    public function testTrueIsTrue()
    {
    	$Qs = new \saefy\quars\Quars();
        $this->assertTrue(true);
    }

    public function testQuarsOkay()
    {
    	$Q = new \saefy\quars\Quars();
        $this->assertTrue(true);
        $this->assertEquals($Q->okay(),'okay executed');
        $this->assertEquals($Q->okay(),'okay executed');
    }
}
