<?php
namespace Mangoit\MediaclipHub\Observer;
 
use Magento\Framework\Event\ObserverInterface;
 
class quoteSaveBefore implements ObserverInterface
{
    /**
     * @var ObjectManagerInterface
     */
    protected $_objectManager;
 
    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->_objectManager = $objectManager;
    }
 
    /**
     * customer register event handler
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    function execute(\Magento\Framework\Event\Observer $observer)
    {
        
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $catalogSession = $objectManager->get('Magento\Customer\Model\Session');
        if (isset($_REQUEST ['projectId'])) {
            
            $projectId = $_REQUEST ['projectId'];
            $quote = $observer->getEvent()->getQuote();

            foreach ($quote->getAllItems() as $item) {
                $product = $objectManager->create('Magento\Catalog\Model\Product')->load($item->getProductId());
                if ($projectId) {
                    if ($item->getAttributeSetId() != 4 && $item->getMediaclipProjectId() == '') {
                        $item->setMediaclipProjectId($projectId);
                        $item->save();
                    }
                }
            }
            if ($product->getMediaclipDustjacketPopup()) {

                $catalogSession->setShowMediaclubSuggestDustJacketPrompt($product->getMediaclipDustjacketPopup());
            }
        }
    }
}