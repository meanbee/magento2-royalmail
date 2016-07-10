<?php

namespace Meanbee\MagentoRoyalmail\Model;

/**
 * Class Rounder
 */
class Rounder
{
    const NONE = 'none';
    const POUND = 'pound';
    const POUND_UP = 'pound-up';
    const POUND_DOWN = 'pound-down';
    const FIFTY = 'fifty';
    const FIFTY_UP = 'fifty_up';
    const FIFTY_DOWN = 'fifty_down';

    /**
     * Round price based on configuration settings
     *
     * @param string $roundingRule
     * @param float $number
     * @return float
     */
    public function round($roundingRule, $number)
    {
        $old = $number;
        switch ($roundingRule) {
            case static::POUND:
                $number = round($number);
                break;
            case static::POUND_UP:
                $number = ceil($number);
                break;
            case static::POUND_DOWN:
                $number = floor($number);
                break;
            case static::FIFTY:
                $number = round($number * 2) / 2;
                break;
            case static::FIFTY_UP:
                $number = ceil($number * 2) / 2;
                break;
            case static::FIFTY_DOWN:
                $number = floor($number * 2) / 2;
                break;
        }

        // Incase it rounds to 0
        if ($number == 0) {
            $number = ceil($old);
        }

        return $number;
    }

    /**
     * Get rounding rules available.
     *
     * @return array
     */
    public function getRoundingRules()
    {
        return [
            static::NONE => 'No rounding performed',
            static::POUND => 'Round to the nearest pound',
            static::POUND_UP => 'Round to the next whole pound',
            static::POUND_DOWN => 'Round to the previous whole pound',
            static::FIFTY => 'Round to the nearest 50p or whole pound',
            static::FIFTY_UP => 'Round to the next 50p or whole pound',
            static::FIFTY_DOWN => 'Round to the previous 50p or whole pound',
        ];
    }
}
