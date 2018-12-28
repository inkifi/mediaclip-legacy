<?php 


namespace Mangoit\MediaclipHub\Controller\Index;
 
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
 
class Requestmediaclipordersave extends Action
{
    /**
     * @var \Magento\Framework\HTTP\Client\Curl
     */
    protected $_curl;
     
    /**
     * @param Context                             $context
     * @param \Magento\Framework\HTTP\Client\Curl $curl
     */
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
        \Magento\Framework\HTTP\Client\Curl $curl,
        PageFactory $resultPageFactory
 
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        $this->_curl = $curl;
        parent::__construct($context);
 
    }
 
    function execute()
    { 
        $response = $this->_curl->getBody();
        /*$json = file_get_contents('php://input');
        $obj = json_decode($json, true);*/
        echo "string";
        echo "<pre>"; print_r($obj);
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $model = $objectManager->create('Mangoit\MediaclipHub\Model\Orders');
        //$model = Mage::getModel('mediaclub/mediacluborders');
        echo "string123";
        $model->setData($response)->save();
        die('40');
        return true;
    }
}
