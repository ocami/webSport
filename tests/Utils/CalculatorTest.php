<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 14/01/2018
 * Time: 00:33
 */

namespace Tests\Utils;

use AppBundle\Utils\Calculator;



class CalculatorTest extends TestCase
{
    public function testAdd()
    {
        $calc = new Calculator();
        $result = $calc->add(30, 12);

        // assert that your calculator added the numbers correctly!
        $this->assertEquals(42, $result);
    }
}