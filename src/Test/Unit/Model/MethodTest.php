<?php

namespace Meanbee\MagentoRoyalmail\Test\Unit\Model;

use Meanbee\MagentoRoyalmail\Model\Method;
use Meanbee\Royalmail\Carrier;

/**
 * Class MethodTest
 */
class MethodTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Carrier
     */
    protected $carrier;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->carrier = new Carrier();
    }

    /**
     * Test that all codes returned are 37 characters or shorter
     * We're trying to keep carrer_method under 40
     */
    public function testGetCode()
    {
        $carrierMethods= $this->carrier->getAllMethods();

        foreach ($carrierMethods as $carrierMethodCode => $carrierMethodName) {
            $method = new Method(
                $carrierMethodCode,
                $carrierMethodCode,
                $carrierMethodName
            );

            $this->assertTrue(strlen($method->getCode()) < 38);
        }
    }
}
