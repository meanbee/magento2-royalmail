<?php

namespace Meanbee\MagentoRoyalmail\Test\Unit\Model\Config\Source;

use Meanbee\MagentoRoyalmail\Model\Config\Source\RoundingRule;
use Meanbee\MagentoRoyalmail\Model\Rounder;

/**
 * Class RoundingRuleTest
 */
class RoundingRuleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Rounder|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rounder;
    /**
     * @var RoundingRule|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $roundingRule;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->rounder = $this->getMockBuilder('Meanbee\MagentoRoyalmail\Model\Rounder')
            ->disableOriginalConstructor()
            ->getMock();

        $this->roundingRule = new RoundingRule($this->rounder);
    }

    public function testToOptionArray()
    {
        $rules = ['none' => 'No rounding performed'];
        $this->mockGetRoundingRules($rules);

        $options = $this->roundingRule->toOptionArray();
        $this->assertEquals(1, count($options));

        $option = array_pop($options);
        $this->assertEquals('none', $option['value']);
        $this->assertEquals('No rounding performed', $option['label']);
    }

    public function testToArray()
    {
        $rules = ['none' => 'No rounding performed'];
        $this->mockGetRoundingRules($rules);

        $options = $this->roundingRule->toArray();

        foreach ($options as $option) {
            $this->assertInternalType('array', $option);
            foreach ($option as $key => $value) {
                $this->assertInternalType('string', $key);
                $this->assertInstanceOf(\Magento\Framework\Phrase::class, $value);
            }
        }
    }


    protected function mockGetRoundingRules($rules)
    {
        $this->rounder
            ->expects($this->once())
            ->method('getRoundingRules')
            ->willReturn($rules);
    }
}
