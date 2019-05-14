<?php
namespace Mangoit\MediaclipHub\Model;
use Inkifi\Pwinty\Setup\UpgradeSchema as Schema;
use Magento\Sales\Model\Order as O;
use Mangoit\MediaclipHub\Model\ResourceModel\Orders as R;
use Mangoit\MediaclipHub\Model\ResourceModel\Orders\Collection as C;
/**
 * 2019-03-13
 * A `mediaclip_orders` row is associated with an unique `magento_order_id`,
 * the following query returns an empty result set:
 *	SELECT * FROM
 *		mediaclip_orders mo1
 *	INNER JOIN
 *		mediaclip_orders mo2
 *	ON
 *			mo1.id <> mo2.id
 *		AND
 *			mo1.magento_order_id = mo2.magento_order_id
 *	ORDER BY mo1.created_at DESC;
 */
class Orders extends \Magento\Framework\Model\AbstractModel {
	/** 2019-03-13 */
	function _construct() {$this->_init(R::class);}

	/**
	 * 2019-03-13 This flag is never used.
	 * @used-by \Inkifi\Mediaclip\H\AvailableForDownload::_p()
	 */
	function markAsAvailableForDownload() {
		$this->setData(self::F__DOWNLOAD_STATUS, 1);
		$this->save();
	}

	/**
	 * 2019-04-03
	 * @used-by \Inkifi\Pwinty\Controller\Index\Index::execute()
	 * @return O
	 */
	function o() {return dfc($this, function() {return df_order($this->oidMagento());});}

	/**
	 * 2019-04-03
	 * @param string $v «58312» or «staging-58312»
	 */
	function oidMagentoSet($v) {$this[self::F__MAGENTO_ORDER_ID] = $v;}

	/**
	 * 2019-04-03 Currently, the value is only set to the database, but it is never retrieved from there.
	 * @used-by \Inkifi\Pwinty\Controller\Index\Index::execute()
	 * @param string $v «MQ121286142GB»
	 */
	function trackingNumberSet($v) {$this[self::$F__TRACKING_NUMBER] = $v;}

	/**
	 * 2019-04-03 Currently, the value is only set to the database, but it is never retrieved from there.
	 * @used-by \Inkifi\Pwinty\Controller\Index\Index::execute()
	 * @param string $v «http://www.royalmail.com/portal/rm/track?trackNumber=MQ121286142GB»
	 */
	function trackingUrlSet($v) {$this[self::$F__TRACKING_URL] = $v;}

	/**
	 * 2019-04-03
	 * @used-by o()
	 * @return int
	 */
	private function oidMagento() {return ikf_eti($this[self::F__MAGENTO_ORDER_ID]);}

	/**
	 * 2019-03-13
	 * 2019-04-02
	 * There are no Mediaclip orders assigned to multiple Magento orders at once.
	 * SELECT magento_order_id, COUNT(*) c FROM mediaclip_orders GROUP BY magento_order_id HAVING c > 1;
	 * >  MySQL returned an empty result set (i.e. zero rows).
	 * https://stackoverflow.com/a/688551
	 * @used-by \Inkifi\Mediaclip\Event::mo()
	 * @param string $oidE		«58312» or «staging-58312»
	 * @return self
	 */
	static function byOId($oidE) {return self::by(self::F__MAGENTO_ORDER_ID, ikf_ite($oidE));}

	/**
	 * 2019-04-03
	 * @used-by byOId()
	 * @param string $k
	 * @param string|int $v
	 * @return self
	 */
	private static function by($k, $v) {
		$c = C::i();
		$c->addFieldToFilter($k, ['eq' => $v]);
		df_assert_eq(1, $c->count());
		return $c->getFirstItem();
	}

	/**
	 * 2019-03-13
	 * @used-by markAsAvailableForDownload()
	 * @used-by \Mangoit\MediaclipHub\Setup\UpgradeSchema::upgrade()
	 */
	const F__DOWNLOAD_STATUS = 'order_download_status';

	/**
	 * 2019-04-03
	 * @used-by byOId()
	 * @used-by oidMagento()
	 * @used-by oidMagentoSet()
	 * @used-by \Inkifi\MissingOrder\Observer\DataProvider\SearchResult::execute()
	 * @used-by \Inkifi\MissingOrder\Processor::eligible()
	 * @used-by \Mangoit\MediaclipHub\Observer\CheckoutSuccess::post()
	 * @used-by \Mangoit\MediaclipHub\Setup\UpgradeSchema::upgrade()
	 */
	const F__MAGENTO_ORDER_ID = 'magento_order_id';

	/**
	 * 2019-04-03
	 * @used-by \Mangoit\MediaclipHub\Observer\CheckoutSuccess::post()
	 * @used-by \Mangoit\MediaclipHub\Setup\UpgradeSchema::upgrade()
	 */
	const F__MEDIACLIP_ORDER_DETAILS = 'mediaclip_order_details';

	/**
	 * 2019-04-03
	 * @used-by \Mangoit\MediaclipHub\Observer\CheckoutSuccess::post()
	 * @used-by \Mangoit\MediaclipHub\Setup\UpgradeSchema::upgrade()
	 */
	const F__MEDIACLIP_ORDER_ID = 'mediaclip_order_id';

	/**
	 * 2019-04-03
	 * @used-by trackingNumberSet()
	 */
	private static $F__TRACKING_NUMBER = 'tracking_number';

	/**
	 * 2019-04-03
	 * @used-by trackingUrlSet()
	 */
	private static $F__TRACKING_URL = 'tracking_url';
}