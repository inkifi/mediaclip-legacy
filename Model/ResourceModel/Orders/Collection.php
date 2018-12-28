<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mangoit\MediaclipHub\Model\ResourceModel\Orders;

/**
 * Moduless Collection
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Initialize resource collection
     *
     * @return void
     */
    function _construct()
    {
        $this->_init('Mangoit\MediaclipHub\Model\Orders', 'Mangoit\MediaclipHub\Model\ResourceModel\Orders');
    }
}
