<?php

namespace Meanbee\MagentoRoyalmail\Test\Unit\Model\Config\Source;

use Meanbee\MagentoRoyalmail\Model\Config\Source\Methods;
use Meanbee\MagentoRoyalmail\Test\Unit\Model\Config\SourceTestAbstract;

/**
 * Class MethodsTest
 */
class MethodsTest extends SourceTestAbstract
{

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $carrier = $this->getMockBuilder('Meanbee\MagentoRoyalmail\Model\Carrier')
            ->disableOriginalConstructor()
            ->setMethods(['getMethods'])
            ->getMock();

        $carrier->expects($this->once())
            ->method('getMethods')
            ->willReturn(['internationalstandardletter' => __("International Standard Letter")]);

        $this->source = new Methods($carrier);
    }
}
