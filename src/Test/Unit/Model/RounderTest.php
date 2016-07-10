<?php
/***
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Meanbee\MagentoRoyalmail\Test\Unit\Model;

use Meanbee\MagentoRoyalmail\Model\Rounder;

/**
 * Class RounderTest
 */
class RounderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Rounder
     */
    protected $rounder;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->rounder = new Rounder();
    }

    public function testRound()
    {
        $this->assertEquals(1, $this->rounder->round('', 1));
    }

    public function testRoundPound()
    {
        $this->assertEquals(1, $this->rounder->round(Rounder::POUND, 0.53));
        $this->assertEquals(1, $this->rounder->round(Rounder::POUND, 1.03));
    }

    public function testRoundPoundUp()
    {
        $this->assertEquals(1, $this->rounder->round(Rounder::POUND_UP, 0.43));
        $this->assertEquals(1, $this->rounder->round(Rounder::POUND_UP, 0.53));
    }

    public function testRoundPoundDown()
    {
        $this->assertEquals(1, $this->rounder->round(Rounder::POUND_DOWN, 1.43));
        $this->assertEquals(1, $this->rounder->round(Rounder::POUND_DOWN, 1.53));
    }

    public function testRoundFifty()
    {
        $this->assertEquals(1.50, $this->rounder->round(Rounder::FIFTY, 1.49));
        $this->assertEquals(1, $this->rounder->round(Rounder::FIFTY, 1.23));
        $this->assertEquals(2, $this->rounder->round(Rounder::FIFTY, 1.75));
    }

    public function testRoundFiftyUp()
    {
        $this->assertEquals(1.50, $this->rounder->round(Rounder::FIFTY_UP, 1.49));
        $this->assertEquals(1.50, $this->rounder->round(Rounder::FIFTY_UP, 1.23));
        $this->assertEquals(2, $this->rounder->round(Rounder::FIFTY_UP, 1.75));
    }

    public function testRoundFiftyDown()
    {
        $this->assertEquals(1, $this->rounder->round(Rounder::FIFTY_DOWN, 1.49));
        $this->assertEquals(1.50, $this->rounder->round(Rounder::FIFTY_DOWN, 1.53));
        $this->assertEquals(1.50, $this->rounder->round(Rounder::FIFTY_DOWN, 1.90));
    }

    public function testRoundToZero()
    {
        $this->assertEquals(1, $this->rounder->round(Rounder::POUND_DOWN, 0.40));
    }

    public function testGetRoundingRules()
    {
        $rules = $this->rounder->getRoundingRules();
        $this->assertInternalType('array', $rules);
        $this->assertGreaterThan(0, count($rules));
    }
}
