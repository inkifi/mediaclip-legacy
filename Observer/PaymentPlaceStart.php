<?php
namespace Mangoit\MediaclipHub\Observer;
use Magento\Customer\Model\Session;
use Magento\Framework\App\ObjectManager as OM;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order\Payment as OP;
/**
 * 2018-06-27 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
 * "Monitor orders and pass the «Payment Complete» ones to MediaClip Photobook in my Magento 2 store":
 * https://www.upwork.com/ab/f/contracts/20295611
 */
final class PaymentPlaceStart implements ObserverInterface {
	/**
	 * 2018-06-27
	 * @override
	 * @see ObserverInterface::execute()
	 * What events are triggered on an order placement? https://mage2.pro/t/3573
	 * @param Observer $ob
	 */
	function execute(Observer $ob) {
		$op = $ob['payment']; /** @var OP $op */
		$om = OM::getInstance(); /** @var OM $om */
		$session = $om->create(Session::class);
		$op->setAdditionalInformation('df_mediaclip_customer_id', $session->getMediaClipUserId());
	}
}