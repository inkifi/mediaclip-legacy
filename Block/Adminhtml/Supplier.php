<?php
namespace Mangoit\MediaclipHub\Block\Adminhtml;

class Supplier extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        
        $this->_controller = 'adminhtml_supplier';/*block grid.php directory*/
        $this->_blockGroup = 'Mangoit_MediaclipHub';
        $this->_headerText = __('Mediaclip Supplier');
        $this->_addButtonLabel = __('Add Supplier');
        parent::_construct();
    }
}
