<?php 


namespace Mangoit\MediaclipHub\Controller\Customer;
 
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
 
class Logincheck extends Action
{
        /**
     * @var Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;
 
    /**
     * @param Context     $context
     * @param PageFactory $resultPageFactory
     */
    function __construct(
        Context $context,
        PageFactory $resultPageFactory
 
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        parent::__construct($context);
 
    }
 
    function execute()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        echo $objectManager->get('Magento\Customer\Model\Session')->isLoggedIn();
        exit;
    }
}
