<?php
/**
 * Copyright © 2015 Mangoit. All rights reserved.
 */
namespace Mangoit\MediaclipHub\Model\ResourceModel;

/**
 * Product resource
 */
class Product extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource
     *
     * @return void
     */
    function _construct()
    {
        $this->_init('mediacliphub_product', 'id');
    }
}
