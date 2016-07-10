<?php

namespace Meanbee\MagentoRoyalmail\Model\Config\Source;

use \Magento\Framework\Option\ArrayInterface;

/**
 * Class ParcelSize Backend system config array field renderer
 */
class ParcelSize implements ArrayInterface
{
    const SMALL = 'SMALL';
    const MEDIUM = 'MEDIUM';

    /**
     * Sets the option array for the small and medium
     * parcel option in admin section of the extension
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            [
                'value' => static::SMALL,
                'label' => __('Small Parcel (up to 2kg)')
            ],
            [
                'value' => static::MEDIUM,
                'label' => __('Medium Parcel')
            ]
        ];

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
