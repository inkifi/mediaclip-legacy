<?php
namespace Mangoit\MediaclipHub\Block\Adminhtml;

class Product extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        
        $this->_controller = 'adminhtml_product';/*block grid.php directory*/
        $this->_blockGroup = 'Mangoit_MediaclipHub';
        $this->_headerText = __('Manage Mediaclip Product');
        $this->_addButtonLabel = __('Add New Product');
        parent::_construct();
    }
}
