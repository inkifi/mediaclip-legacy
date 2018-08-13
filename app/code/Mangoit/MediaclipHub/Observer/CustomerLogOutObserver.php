<?php

namespace Mangoit\MediaclipHub\Observer;

use Magento\Framework\Event\ObserverInterface;

class CustomerLogOutObserver implements ObserverInterface
{
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $session = $objectManager->get('Magento\Customer\Model\Session');
        $session->unsMediaClipToken();
        $session->unsMediaClipUserId();
        $session->unsMediaClipUserType();
        $session->unsMediaClipProjectId();
  
    }
}