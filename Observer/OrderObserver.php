<?php
/**
 * Copyright © Cryozonic Ltd. All rights reserved.
 *
 * @package    Cryozonic_StripePayments
 * @copyright  Copyright © Cryozonic Ltd (http://cryozonic.com)
 * @license    Commercial (See http://cryozonic.com/licenses/stripe.html for details)
 */

namespace Mangoit\MediaclipHub\Observer;

use Magento\Framework\Event\Observer;
use Magento\Payment\Observer\AbstractDataAssignObserver;

class OrderObserver extends AbstractDataAssignObserver
{
    
    function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $payment = $order->getPayment()->getState();
        $_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $debugData = $_objectManager->create('Psr\Log\LoggerInterface');
        //$debugData->info(json_encode($order->debug()));
        //$debugData->info(json_encode($payment));
        $debugData->info('Inside OrderObserver observer Mangoit Mediaclip -');
        $debugData->info(json_encode($order->getPayment()->getData()));
    }
}
