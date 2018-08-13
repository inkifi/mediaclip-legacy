<?php 


namespace Mangoit\MediaclipHub\Controller\Index;
 
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
 
class RenewMediaclipToken extends Action
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
        $session = $this->_objectManager->get('Magento\Customer\Model\Session');
        //$session = Mage::getSingleton('core/session');
        if ($session->getMediaClipToken()) {
            $mediaclipToken = $session->getMediaClipToken()->token;
            $hubHelperResponse = $this->_objectManager->create('Mangoit\MediaclipHub\Helper\Data')->RenewToken($mediaclipToken);
            if (!empty($hubHelperResponse)) {
                $session->setMediaClipToken($hubHelperResponse);
                return $hubHelperResponse;
            } else {
                die('42');
               // return json_encode(array('redirectUrl' => Mage::getBaseUrl()."customer/account/login"));
            }
        }
    }
}
