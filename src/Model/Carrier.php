<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Meanbee\MagentoRoyalmail\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Shipping\Model\Rate\ResultFactory;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Magento\Quote\Model\Quote\Address\RateResult\Method;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Meanbee\Royalmail\Carrier as LibCarrier;
use Psr\Log\LoggerInterface;

/**
 * Class Carrier Royal Mail shipping model
 */
class Carrier extends AbstractCarrier implements CarrierInterface
{
    /**
     * Carrier's code
     *
     * @var string
     */
    protected $_code = 'meanbee_royalmail';

    /**
     * Whether this carrier has fixed rates calculation
     *
     * @var bool
     */
    protected $_isFixed = true;

    /**
     * @var ResultFactory
     */
    protected $rateResultFactory;

    /**
     * @var MethodFactory
     */
    protected $rateMethodFactory;

    /**
     * @var LibCarrier
     */
    protected $carrier;

    /**
     * @var Rounder
     */
    protected $rounder;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param ErrorFactory $rateErrorFactory
     * @param LoggerInterface $logger
     * @param ResultFactory $rateResultFactory
     * @param MethodFactory $rateMethodFactory
     * @param Rounder $rounder
     * @param LibCarrier $carrier
     * @param array $data
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ErrorFactory $rateErrorFactory,
        LoggerInterface $logger,
        ResultFactory $rateResultFactory,
        MethodFactory $rateMethodFactory,
        Rounder $rounder,
        LibCarrier $carrier,
        array $data = []
    ) {
        $this->rateResultFactory = $rateResultFactory;
        $this->rateMethodFactory = $rateMethodFactory;
        $this->rounder = $rounder;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    /**
     * Collect and get rates for storefront
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param RateRequest $request
     * @return DataObject|bool|null
     * @api
     */
    public function collectRates(RateRequest $request)
    {
        /**
         * Make sure that Shipping method is enabled
         */
        if (!$this->isActive()) {
            return false;
        }

        $unit = $this->_scopeConfig->getValue(
            \Magento\Directory\Helper\Data::XML_PATH_WEIGHT_UNIT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $weight = $this->getPackageWeightInKg($request->getPackageWeight(), $unit);

        $methods = $this->getCarrier()->getRates(
            $request->getDestCountryId(),
            $request->getPackageValue(),
            $weight
        );

        $methods = $this->removeUnusedParcelSizes($methods, $weight);

        $result = $this->rateResultFactory->create();

        $allowedMethods = $this->getAllowedMethods();

        if (empty($allowedMethods)) {
            return $result;
        }

        /** @var \Meanbee\RoyalMail\Method $method */
        foreach ($methods as $method) {
            if (!array_key_exists($method->getCode(), $allowedMethods)) {
                continue;
            }

            /** @var Method $rate */
            $rate = $this->rateMethodFactory->create();
            $rate->setData('carrier', $this->getCarrierCode());
            $rate->setData('carrier_title', $this->getConfigData('title'));
            $rate->setData('method_title', $method->getName());
            $rate->setData('method', $method->getCode());
            $rate->setPrice(
                $this->rounder->round(
                    $this->getConfigData('rounding_rule'),
                    $this->getFinalPriceWithHandlingFee($method->getPrice())
                )
            );
            $result->append($rate);
        }

        return $result;
    }


    /**
     * Gets the methods selected in the admin area of the extension
     * to ensure that not allowed methods can be removed in the collect
     * rates method
     *
     * @return array
     */
    public function getAllowedMethods()
    {
        $configMethods = explode(',', $this->getConfigData('allowed_methods'));
        $allMethods = $this->getMethods();

        return array_intersect_key($allMethods, array_flip($configMethods));
    }
    /**
     * Gets the clean method names from the royal mail library data
     * class. These names link directly to method names, but are used
     * to ensure that duplicates are not created as similar names
     * exists for multiple methods.
     *
     * @return array
     */
    public function getMethods()
    {
        return $this->getCarrier()->getAllMethods();
    }

    /**
     * @return LibCarrier
     */
    public function getCarrier()
    {
        /**
         * Bug in Magento, when production mode is enabled
         * if you're trying to inject an external library, magento won't discover
         * the correct dependencies. Even if it is clearly defined in di.xml.
         * This odd behaviour results in an instance of ObjectManager being injected.
         * Solution is to skip DI, and instantiate yourself.
         *
         * @TODO Once issue is resolved, we can use the constructor instantiated $carrier object.
         * @link https://github.com/magento/magento2/issues/6739
         */
        if (!$this->carrier) {
            $this->carrier = new LibCarrier();
        }

        return $this->carrier;
    }

    /**
     * @deprecated
     * @param $libCarrier
     * @return $this
     */
    public function setCarrier($libCarrier)
    {
        $this->carrier = $libCarrier;
        return $this;
    }

    /**
     * Get package weight in Kilograms converting from lbs if necessary.
     *
     * @param $weight
     * @param $unit
     * @return mixed
     */
    protected function getPackageWeightInKg($weight, $unit)
    {
        if ($unit == 'lbs') {
            $weight = $weight * 0.453592;
        }

        return $weight;
    }

    /**
     * Both small and medium sized parcels can serve up to 2KG.
     * Configuration option determines which size we show to customer.
     *
     * @param \Meanbee\RoyalMail\Method[] $methods
     * @param int $weight
     * @return \Meanbee\RoyalMail\Method[]
     */
    protected function removeUnusedParcelSizes($methods, $weight)
    {
        $parcelSize = $this->getConfigData('parcel_size');
        if ($weight <= 2 && $parcelSize) {
            foreach ($methods as $key => $method) {
                if ($method->getSize() && $method->getSize() != $parcelSize) {
                    unset($methods[$key]);
                }
            }
        }

        return $methods;
    }
}
