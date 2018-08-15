<?php
/**
 * Copyright © 2015 Mangoit. All rights reserved.
 */
namespace Mangoit\MediaclipHub\Model\ResourceModel;

/**
 * Modules resource
 */
class Orders extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('mediaclip_orders', 'id');
    }
}
