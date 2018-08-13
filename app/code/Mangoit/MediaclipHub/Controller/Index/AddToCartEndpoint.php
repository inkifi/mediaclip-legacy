<?php 


namespace Mangoit\MediaclipHub\Controller\Index;
 
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
 
class AddToCartEndpoint extends Action
{
        /**
     * @var Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;
 
    /**
     * @param Context     $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
 
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        parent::__construct($context);
 
    }
 
    public function execute()
    {
        $json = file_get_contents('php://input');
        $obj = json_decode($json, true);
        $model = $this->_objectManager->create('Mangoit\MediaclipHub\Model\Mediaclip')->load($obj['projectId'], 'project_id')->setProjectDetails(json_encode($obj))->save();
    }
}
