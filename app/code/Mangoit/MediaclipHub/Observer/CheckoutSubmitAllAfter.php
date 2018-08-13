<?php
namespace Mangoit\MediaclipHub\Observer;
use Magento\Catalog\Model\Product as P;
use Magento\Customer\Model\Session;
use Magento\Directory\Model\Country;
use Magento\Eav\Api\AttributeSetRepositoryInterface as IAttributeSet;
use Magento\Framework\App\ObjectManager as OM;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Api\OrderRepositoryInterface as IOrderRepository;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Item as OI;
use Magento\Sales\Model\Order\Payment as OP;
use Magento\Sales\Model\OrderRepository;
use Mangoit\MediaclipHub\Helper\Data as mHelper;
use Mangoit\MediaclipHub\Model\Supplier as mSupplier;
/**
 * 2018-06-27 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
 * "Monitor orders and pass the «Payment Complete» ones to MediaClip Photobook in my Magento 2 store":
 * https://www.upwork.com/ab/f/contracts/20295611
 */
final class CheckoutSubmitAllAfter implements ObserverInterface {
	/**
	 * 2018-06-27
	 * @override
	 * @see ObserverInterface::execute()
	 * What events are triggered on an order placement? https://mage2.pro/t/3573
	 * @param Observer $ob
	 */
	function execute(Observer $ob) {
		$o = $ob['order']; /** @var O $o */
		if ('pending_payment' === $o->getStatus()) {
			$om = OM::getInstance(); /** @var OM $om */
			$session = $om->create(Session::class);
			$op = $o->getPayment(); /** @var OP $op */
			$op->setAdditionalInformation('df_mediaclip_customer_id', $session->getMediaClipUserId());
			$op->save();
		}
	}
}