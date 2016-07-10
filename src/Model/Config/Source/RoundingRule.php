<?php

namespace Meanbee\MagentoRoyalmail\Model\Config\Source;

use \Magento\Framework\Option\ArrayInterface;
use Meanbee\MagentoRoyalmail\Model\Rounder;

/**
 * Class RoundingRule Backend system config array field renderer
 */
class RoundingRule implements ArrayInterface
{
    const NONE = 'none';
    const POUND = 'pound';
    const POUND_UP = 'pound-up';
    const POUND_DOWN = 'pound-down';
    const FIFTY = 'fifty';
    const FIFTY_UP = 'fifty_up';
    const FIFTY_DOWN = 'fifty_down';

    /**
     * @var $rounder Rounder
     */
    protected $rounder;

    public function __construct(Rounder $rounder)
    {
        $this->rounder = $rounder;
    }

    /**
     * Get list of rounding rule options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];

        $rules = $this->rounder->getRoundingRules();
        foreach ($rules as $rule => $name) {
            $options[] = [
                'value' => $rule,
                'label' => __($name)
            ];
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
