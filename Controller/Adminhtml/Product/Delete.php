<?php
namespace Mangoit\MediaclipHub\Controller\Adminhtml\Product;

class Delete extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    function execute()
    {
        $id = $this->getRequest()->getParam('id');
        try {
                $banner = $this->_objectManager->get('Mangoit\MediaclipHub\Model\Product')->load($id);
                $banner->delete();
                $this->messageManager->addSuccess(
                    __('Delete successfully !')
                );
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        $this->_redirect('*/*/');
    }
}
