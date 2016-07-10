<?php

namespace Meanbee\MagentoRoyalmail\Test\Unit\Model\Config;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class SourceTest
 */
abstract class SourceTestAbstract extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ArrayInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $source;

    public function testToOptionArray()
    {
        $options = $this->source->toOptionArray();
        foreach ($options as $option) {
            $this->assertArrayHasKey('value', $option);
            $this->assertArrayHasKey('label', $option);
        }
    }

    public function testToArray()
    {
        $array = $this->source->toArray();
        foreach ($array as $item) {
            $this->assertInternalType('array', $item);
            foreach ($item as $key => $value) {
                $this->assertInternalType('string', $key);
                $this->assertInstanceOf(\Magento\Framework\Phrase::class, $value);
            }
        }
    }
}
