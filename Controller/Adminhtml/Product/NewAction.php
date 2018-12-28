<?php
namespace Mangoit\MediaclipHub\Controller\Adminhtml\Product;

use Magento\Backend\App\Action;

class NewAction extends \Magento\Backend\App\Action
{
    function execute()
    {
        $this->_forward('edit');
    }
}
