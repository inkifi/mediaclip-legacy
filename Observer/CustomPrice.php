<?php
    /**
     * Webkul Hello CustomPrice Observer
     *
     * @category    Webkul
     * @package     Webkul_Hello
     * @author      Webkul Software Private Limited
     *
     */
    namespace Mangoit\MediaclipHub\Observer;
 
    use Magento\Framework\Event\ObserverInterface;
    use Magento\Framework\App\RequestInterface;
 
    class CustomPrice implements ObserverInterface
    {
        public function execute(\Magento\Framework\Event\Observer $observer) {

            $item = $observer->getEvent()->getData('quote_item');
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $session = $objectManager->get('Magento\Customer\Model\Session');
            //$session->setCustomPrice($price);         
            //echo "<pre>"; print_r($item->getProduct()->getData('price')); die('18');
            //echo "<pre>"; print_r($item->getProduct()->getData('mediaclip_extra_prints_price')); die('18');
            //echo "<pre>"; print_r($item->getProduct()->getData('price')); die('18');
            $item = ( $item->getParentItem() ? $item->getParentItem() : $item );
            $price = $session->getCustomPriceObserver(); //set your price here
            //print_r($price); die('18');
            $item->setCustomPrice($price);
            $item->setOriginalCustomPrice($price);
            $item->getProduct()->setIsSuperMode(true);
        }
 
    }