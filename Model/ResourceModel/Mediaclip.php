<?php
/**
 * Copyright © 2015 Mangoit. All rights reserved.
 */
namespace Mangoit\MediaclipHub\Model\ResourceModel;

/**
 * Modules resource
 */
class Mediaclip extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource
     *
     * @return void
     */
    function _construct()
    {
        $this->_init('mediaclip', 'id');
    }
}
