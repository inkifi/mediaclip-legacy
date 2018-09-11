<?php 


namespace Mangoit\MediaclipHub\Controller\Index;
 
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
 
class Customizeproject extends Action
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
		/**
		 * 2018-07-30 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
		 * The previous code was:
		 * 		$tokenObj = $this->_objectManager->get('Magento\Customer\Model\Session');
		 * 		if ($tokenObj->getMediaClipToken()) {
		 * It led to the error sometimes:
		 * «Object of class Magento\Customer\Model\Session\Interceptor could not be converted to string
		 * in app/code/Mangoit/MediaclipHub/Helper/Data.php on line 537»
		 */
        $s = $this->_objectManager->get(Session::class); /** @var Session $s */
        $t = $s->getMediaClipToken(); /** @var object|null $t */
        $resultPage = $this->_resultPageFactory->create();
        //$resultPage->getConfig()->getTitle()->prepend(__(' heading '));
        $params = $this->getRequest()->getParams();
        if (mc_h()->checkUserToken($params)) {
           
            $block = $resultPage->getLayout()
                    ->createBlock('Magento\Framework\View\Element\Template')
                    ->setTemplate('Mangoit_MediaclipHub::mediaclip.phtml')
                    ->setFeedback(!$t ? null : $t->token)
                    ->toHtml();
                 echo $block;   
            //echo json_encode(array('data' => $block));
            exit;
            //$this->getResponse()->setBody($block);
        }else{
            die('46');
        }
    }
}
