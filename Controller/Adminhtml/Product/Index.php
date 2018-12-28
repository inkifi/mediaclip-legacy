<?php
namespace Mangoit\MediaclipHub\Controller\Adminhtml\Product;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Backend\Model\View\Result\Page
     */
    protected $resultPage;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
    
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    function execute()
    {
        
        $this->resultPage = $this->resultPageFactory->create();
        $this->resultPage->setActiveMenu('Mangoit_Product::product');
        $this->resultPage ->getConfig()->getTitle()->set((__('Product')));
        return $this->resultPage;
    }
}
