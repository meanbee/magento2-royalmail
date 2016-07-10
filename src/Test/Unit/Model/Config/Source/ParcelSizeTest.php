<?php

namespace Meanbee\MagentoRoyalmail\Test\Unit\Model\Config\Source;

use Meanbee\MagentoRoyalmail\Model\Config\Source\ParcelSize;
use Meanbee\MagentoRoyalmail\Test\Unit\Model\Config\SourceTestAbstract;

/**
 * Class ParcelSizeTest
 */
class ParcelSizeTest extends SourceTestAbstract
{

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->source = new ParcelSize();
    }
}
