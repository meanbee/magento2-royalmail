<?php

namespace Meanbee\MagentoRoyalmail\Model;

/**
 * Class Method
 */
class Method
{
    /**
     * The ID of the method rule.
     *
     * @var string
     */
    protected $id;


    /**
     * The shipping code name of the method
     *
     * @var string
     */
    protected $code;

    /**
     * The clean shipping method name of the shipping method
     *
     * @var string
     */
    protected $name;

    /**
     * The country code of the method
     *
     * @var string
     */
    protected $countryCode;

    /**
     * Price of method
     *
     * @var string
     */
    protected $price;

    /**
     * Maximum value of package that is insured
     *
     * @var string
     */
    protected $insuranceValue;

    /**
     * The minimum weight the shipping method can accommodate
     *
     * @var string
     */
    protected $minimumWeight;

    /**
     * The maximum weight the shipping method can accommodate
     *
     * @var string
     */
    protected $maximumWeight;

    /**
     * The parcel size, only applies to small and medium parcels
     *
     * @var string
     */
    protected $size;

    /**
     * Dictionary to replace strings within method codes.
     * Without shortening, method codes are too long and Magento rejects order.
     *
     * @var $methodDitionary
     */
    protected static $methodDictionary = array(
        'economy' => 'eco',
        'express' => 'xp',
        'firstclass' => '1st',
        'international' => 'i11l',
        'large' => 'lg',
        'letter' => 'ltr',
        'medium' => 'med',
        'parcelforce' => 'pf',
        'parcel' => 'prcl',
        'saturday' => 'sat',
        'standard' => 'std',
        'secondclass' => '2nd',
        'signedfor' => 'signed',
        'small' => 'sm',
        'specialdelivery' => 'special',
        'trackedandsigned' => 'tracksign',
        'trackedsigned' => 'tracksign',
        'worldwide' => 'ww',
    );

    /**
     * Method constructor.
     *
     * @param string $id             - Method unique identifier
     * @param string $code           - Method code
     * @param string $name           - Method label
     * @param string $countryCode    - Country code of method
     * @param string $price          - Price of method
     * @param string $insuranceValue - Insurance value of method
     * @param string $minimumWeight  - Minimum weight the method can have
     * @param string $maximumWeight  - Maximum weight the method can have
     * @param null   $size           - Parcel size, only applies to sm and md parcels
     */
    public function __construct(
        $id,
        $code,
        $name,
        $countryCode = null,
        $price = null,
        $insuranceValue = null,
        $minimumWeight = null,
        $maximumWeight = null,
        $size = null
    ) {
        $this->id = $id;
        $this->code = str_replace(
            array_keys(self::$methodDictionary),
            array_values(self::$methodDictionary),
            $code
        );
        $this->name = $name;
        $this->countryCode = $countryCode;
        $this->price = $price;
        $this->insuranceValue = $insuranceValue;
        $this->minimumWeight = $minimumWeight;
        $this->maximumWeight = $maximumWeight;
        $this->size = $size;
    }

    /**
     * The unique ID of a method
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * The method code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * The clean shipping method name of the shipping method
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * The country code of the method
     *
     * @return string
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }

    /**
     * Price of method
     *
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Maximum value of package that is insured
     *
     * @return string
     */
    public function getInsuranceValue()
    {
        return $this->insuranceValue;
    }

    /**
     * The minimum weight the shipping method can accommodate
     *
     * @return string
     */
    public function getMinimumWeight()
    {
        return $this->minimumWeight;
    }

    /**
     * The maximum weight the shipping method can accommodate
     *
     * @return string
     */
    public function getMaximumWeight()
    {
        return $this->maximumWeight;
    }

    /**
     * The parcel size, only applies to small and medium parcels
     *
     * @return string
     */
    public function getSize()
    {
        return $this->size;
    }

}
