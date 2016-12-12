<?php
/***
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Meanbee\MagentoRoyalmail\Test\Unit\Model;

use Meanbee\MagentoRoyalmail\Model\Carrier;
use Meanbee\MagentoRoyalmail\Model\Config\Source\ParcelSize;
use Meanbee\Royalmail\Method;

/**
 * Class CarrierTest
 */
class CarrierTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Carrier
     */
    protected $model;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Shipping\Model\Rate\Result|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rateResultFactory;

    /**
     * @var \Magento\Shipping\Model\Rate\Result|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rateResult;

    /**
     * @var \Magento\Quote\Model\Quote\Address\RateResult\Method|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rateMethodFactory;

    /**
     * @var \Magento\Quote\Model\Quote\Address\RateResult\Method|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rateResultMethod;

    /**
     * @var \Meanbee\MagentoRoyalmail\Model\Rounder
     */
    protected $rounder;

    /**
     * @var \Meanbee\Royalmail\Carrier
     */
    protected $libCarrier;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->scopeConfig = $this->getMockBuilder('Magento\Framework\App\Config\ScopeConfigInterface')
            ->getMockForAbstractClass();
        $rateErrorFactory = $this->getMockBuilder('Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $logger = $this->getMockBuilder('Psr\Log\LoggerInterface')
            ->getMockForAbstractClass();
        $this->rateResultFactory = $this->getMockBuilder('Magento\Shipping\Model\Rate\ResultFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $this->rateResult = $this->getMockBuilder('Magento\Shipping\Model\Rate\Result')
            ->disableOriginalConstructor()
            ->getMock();
        $this->rateMethodFactory = $this->getMockBuilder('Magento\Quote\Model\Quote\Address\RateResult\MethodFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $this->rateResultMethod = $this->getMockBuilder('Magento\Quote\Model\Quote\Address\RateResult\Method')
            ->disableOriginalConstructor()
            ->getMock();
        $this->rounder = $this->getMockBuilder('Meanbee\MagentoRoyalmail\Model\Rounder')
            ->disableOriginalConstructor()
            ->setMethods(['round'])
            ->getMock();
        $this->libCarrier = $this->getMockBuilder('Meanbee\Royalmail\Carrier')
            ->disableOriginalConstructor()
            ->getMock();

        $this->rateResultFactory->expects($this->any())->method('create')->willReturn($this->rateResult);
        $this->rateMethodFactory->expects($this->any())->method('create')->willReturn($this->rateResultMethod);

        $this->model = new Carrier(
            $this->scopeConfig,
            $rateErrorFactory,
            $logger,
            $this->rateResultFactory,
            $this->rateMethodFactory,
            $this->rounder,
            $this->libCarrier
        );
        $this->model->setCarrier($this->libCarrier);
    }

    public function testGetAllowedMethods()
    {
        $methods = "internationalstandardletter,internationalstandardlargeletter";
        $code = 'internationalstandardletter';
        $name = "International Standard Letter";

        $this->scopeConfig
            ->expects($this->at(0))
            ->method('getValue')
            ->with()
            ->willReturn($methods);
        $this->mockAllMethods([$code => $name]);

        $methods = $this->model->getAllowedMethods();

        $this->assertArrayHasKey($code, $methods);
        $this->assertEquals($name, $methods[$code]);
    }

    public function testGetMethods()
    {
        $methods = ['internationalstandardletter' => "International Standard Letter"];
        $this->mockAllMethods($methods);
        $this->assertEquals($methods, $this->model->getMethods());
    }

    public function testCollectRatesNotActive()
    {
        $this->mockIsActive(false);
        $this->assertFalse($this->model->collectRates($this->getRateRequest()));
    }

    public function testCollectRatesNoAllowedMethods()
    {
        $this->mockIsActive(true);
        $rate = $this->getMethod('International Standard Small Parcel');

        $this->mockMethodsConfigured(false);
        $this->mockLibraryRates([$rate]);
        $this->mockAllMethods(['internationalstandardsmallparcel'
            => "International Standard Small Parcel"
        ]);
        $this->mockHasResults(false);

        $result = $this->model->collectRates($this->getRateRequest());
        $this->assertInstanceOf('Magento\Shipping\Model\Rate\Result', $result);

    }

    public function testCollectRatesDisabledMethods()
    {
        $this->mockIsActive(true);

        $rate = $this->getMethod('International Standard Medium Parcel');

        $this->mockMethodsConfigured('internationalstandardsmallparcel');
        $this->mockLibraryRates([$rate]);
        $this->mockAllMethods(['internationalstandardsmallparcel'
            => "International Standard Small Parcel"
        ]);
        $this->mockHasResults(false);

        $result = $this->model->collectRates($this->getRateRequest());
        $this->assertInstanceOf('Magento\Shipping\Model\Rate\Result', $result);
    }

    public function testCollectRatesMediumParcels()
    {
        $this->mockIsActive(true);
        $rate = $this->getMethod('International Standard Small Parcel');
        $this->mockMethodsConfigured('internationalstandardsmallparcel');
        $this->mockLibraryRates([$rate]);
        $this->mockParcelSize(ParcelSize::MEDIUM);
        $this->mockAllMethods(['internationalstandardsmallparcel'
            => "International Standard Small Parcel"
        ]);
        $this->mockHasResults(false);

        $result = $this->model->collectRates($this->getRateRequest());
        $this->assertInstanceOf('Magento\Shipping\Model\Rate\Result', $result);
    }

    public function testCollectRatesInLbs()
    {
        $this->mockIsActive(true);
        $this->mockWeightUnit('lbs');

        $this->mockMethodsConfigured('internationalstandardsmallparcel');

        $rateRequest = $this->getRateRequest();
        $rateRequest->expects($this->once())
            ->method('getPackageWeight')
            ->willReturn(1);

        $this->libCarrier
            ->expects($this->once())
            ->method('getRates')
            ->with(null, null, 0.453592, false)
            ->willReturn($this->getMethod('International Standard Small Parcel'));

        $this->mockAllMethods(['internationalstandardsmallparcel'
            => "International Standard Small Parcel"
        ]);

        $this->mockHasResults(false);

        $this->model->collectRates($rateRequest);

    }

    public function testCollectRates()
    {
        $this->mockIsActive(true);
        $rate = $this->getMethod('International Standard Small Parcel');
        $this->mockMethodsConfigured('internationalstandardsmallparcel');
        $this->mockLibraryRates([$rate]);
        $this->mockAllMethods(['internationalstandardsmallparcel'
            => "International Standard Small Parcel"
        ]);
        $this->mockHasResults(true);

        $result = $this->model->collectRates($this->getRateRequest());
        $this->assertInstanceOf('Magento\Shipping\Model\Rate\Result', $result);
    }

    /**
     * Get a shipping Method
     *
     * @param $name
     * @return Method
     */
    protected function getMethod($name)
    {
        return new Method(
            str_replace(' ', '_', strtoupper($name)),
            str_replace(' ', '', strtolower($name)),
            $name,
            'US',
            13.05,
            20,
            0.751,
            1,
            ParcelSize::SMALL
        );
    }

    /**
     * Get rate request
     *
     * @return \Magento\Quote\Model\Quote\Address\RateRequest
     */
    protected function getRateRequest()
    {
        /** @var \Magento\Quote\Model\Quote\Address\RateRequest $request */
        return $this->getMockBuilder('Magento\Quote\Model\Quote\Address\RateRequest')
            ->disableOriginalConstructor()
            ->setMethods(['getPackageWeight'])
            ->getMock();
    }

    /**
     * @param $result
     */
    protected function mockIsActive($result)
    {
        $this->scopeConfig
            ->expects($this->at(0))
            ->method('getValue')
            ->with('carriers/meanbee_royalmail/active')
            ->willReturn($result);
    }

    protected function mockWeightUnit($unit)
    {
        $this->scopeConfig
            ->expects($this->at(1))
            ->method('getValue')
            ->with(\Magento\Directory\Helper\Data::XML_PATH_WEIGHT_UNIT)
            ->willReturn($unit);
    }

    /**
     * @param $parcelSize
     */
    protected function mockParcelSize($parcelSize)
    {
        $this->scopeConfig
            ->expects($this->at(2))
            ->method('getValue')
            ->with('carriers/meanbee_royalmail/parcel_size')
            ->willReturn($parcelSize);
    }

    /**
     * @param $methods
     */
    protected function mockMethodsConfigured($methods)
    {
        $this->scopeConfig
            ->expects($this->at(3))
            ->method('getValue')
            ->with()
            ->willReturn($methods);
    }

    /**
     * @param $rates
     */
    protected function mockLibraryRates($rates)
    {
        $this->libCarrier
            ->expects($this->once())
            ->method('getRates')
            ->willReturn($rates);
    }

    /**
     * @param $allMethods
     */
    protected function mockAllMethods($allMethods)
    {
        $this->libCarrier
            ->expects($this->once())
            ->method('getAllMethods')
            ->willReturn($allMethods);
    }

    /**
     * @param bool $result
     */
    protected function mockHasResults($result = true)
    {
        $matcher = $this->atLeastOnce();
        if (!$result) {
            $matcher = $this->never();
        }

        $this->rateResult
            ->expects($matcher)
            ->method('append')
            ->with($this->rateResultMethod);
    }
}
