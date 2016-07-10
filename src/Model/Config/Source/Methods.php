<?php

namespace Meanbee\MagentoRoyalmail\Model\Config\Source;

use \Magento\Framework\Option\ArrayInterface;
use Meanbee\MagentoRoyalmail\Model\Carrier;


/**
 * Class Methods Backend system config field renderer
 */
class Methods implements ArrayInterface
{

    /**
     * @var Carrier $carrier
     */
    protected $carrier;

    public function __construct(Carrier $carrier)
    {
        $this->carrier = $carrier;
    }

    /**
     * Sets the option array for the small and medium
     * parcel option in admin section of the extension
     *
     * @return array
     */
    public function toOptionArray()
    {
        $methods = $this->carrier->getMethods();

        $options = [];
        foreach ($methods as $value => $label) {
            $options[] = ['value' => $value, 'label' => $label];
        }

        return $options;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return array_map(function ($array) {
            return [$array['value'] => $array['label']];
        }, $this->toOptionArray());
    }
}
