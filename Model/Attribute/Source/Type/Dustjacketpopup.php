<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Mangoit\MediaclipHub\Model\Attribute\Source\Type;

class Dustjacketpopup extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * Get all options
     * @return array
     */
    function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = mc_h()->getDustjacketpopup();
        }
        return $this->_options;
    }
}
