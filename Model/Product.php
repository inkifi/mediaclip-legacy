<?php
namespace Mangoit\MediaclipHub\Model;
use Magento\Catalog\Model\Product as P;
use Mangoit\MediaclipHub\Model\ResourceModel\Modules as ModulesR;
use Mangoit\MediaclipHub\Model\ResourceModel\Product as R;
// 2019-03-13
class Product extends \Magento\Framework\Model\AbstractModel {
	/**
	 * 2019-04-11
	 * @used-by \Inkifi\Pwinty\AvailableForDownload::images()
	 * @return string «gold»
	 */
	function frameColor() {return $this[self::F__FRAME_COLOUR];}

	/**
	 * 2019-02-27
	 * @used-by \Inkifi\Mediaclip\H\AvailableForDownload\Pureprint::pOI()
	 * @return bool
	 */
	function includeQuantityInJson() {return !!$this[self::F__INCLUDE_QUANTITY_IN_JSON];}

	/**
	 * 2019-03-12
	 * @used-by \Inkifi\Mediaclip\H\AvailableForDownload\Pureprint::pOI()
	 * @return string|null
	 */
	function jsonCode() {return $this[self::F__JSON_CODE];}

	/**
	 * 2019-03-12
	 * @used-by \Inkifi\Mediaclip\H\AvailableForDownload\Pureprint::writeLocal()
	 * @return string|null
	 */
	function label() {return $this[self::F__LABEL];}

	/**
	 * 2019-03-04
	 * @used-by \Inkifi\Mediaclip\API\Entity\Order\Item::mProduct()
	 * @param string $v «INKIFI-VP»
	 */
	function loadByPlu($v) {$this->load($v, self::F__PLU);}

	/**
	 * 2019-03-04
	 * @used-by \Inkifi\Mediaclip\H\AvailableForDownload\Pureprint::pOI()
	 * @return string «INKIFI-VP»
	 */
	function plu() {return $this[self::F__PLU];}

	/**
	 * 2019-04-24
	 * @used-by \Inkifi\Pwinty\AvailableForDownload::images()
	 * @return string|null «FRA-INSTA-30X30»
	 */
	function pwintyProductSku() {return $this[self::F__PWINTY_PRODUCT_NAME];}

	/**
	 * 2019-02-27
	 * @used-by \Inkifi\Mediaclip\H\AvailableForDownload\Pureprint::pOI()
	 * @used-by \Inkifi\Pwinty\AvailableForDownload::images()
	 * @return bool
	 */
	function sendJson() {return !!$this[self::F__FTP_JSON];}

	/**
	 * 2019-02-27
	 * @override
	 * @see \Magento\Framework\Model\AbstractModel::_construct()
	 */
	protected function _construct() {$this->_init(R::class);}

	/**
	 * 2019-04-11
	 * @used-by frameColor()
	 * @used-by \Mangoit\MediaclipHub\Block\Adminhtml\Product\Edit\Tab\ProductInformation::_prepareForm()
	 * @used-by \Mangoit\MediaclipHub\Setup\UpgradeSchema::upgrade()
	 */
	const F__FRAME_COLOUR = 'frame_colour';

	/**
	 * 2019-02-27
	 * @used-by sendJson()
	 * @used-by \Mangoit\MediaclipHub\Block\Adminhtml\Product\Edit\Tab\ProductInformation::_prepareForm()
	 * @used-by \Mangoit\MediaclipHub\Setup\InstallSchema::install()
	 */
	const F__FTP_JSON = 'ftp_json';

	/**
	 * 2019-02-27
	 * @used-by includeQuantityInJson()
	 * @used-by \Mangoit\MediaclipHub\Block\Adminhtml\Product\Edit\Tab\ProductInformation::_prepareForm()
	 * @used-by \Mangoit\MediaclipHub\Setup\UpgradeSchema::upgrade()
	 */
	const F__INCLUDE_QUANTITY_IN_JSON = 'include_quantity_in_json';

	/**
	 * 2019-03-12
	 * @used-by jsonCode()
	 * @used-by \Mangoit\MediaclipHub\Block\Adminhtml\Product\Edit\Tab\ProductInformation::_prepareForm()
	 * @used-by \Mangoit\MediaclipHub\Setup\UpgradeSchema::upgrade()
	 */
	const F__JSON_CODE = 'json_code';

	/**
	 * 2019-03-04
	 * @used-by getMediaClipProductBySku()
	 * @used-by loadByPlu()
	 * @used-by plu()
	 * @used-by \Mangoit\MediaclipHub\Block\Adminhtml\Product\Edit\Tab\ProductInformation::_prepareForm()
	 * @used-by \Mangoit\MediaclipHub\Block\Adminhtml\Product\Grid::_prepareColumns()
	 * @used-by \Mangoit\MediaclipHub\Controller\Product\Edit::getMediaclipProductData()
	 * @used-by \Mangoit\MediaclipHub\Helper\Data::getGiftingProduct()
	 * @used-by \Mangoit\MediaclipHub\Helper\Data::getMediaClipProductName()
	 * @used-by \Mangoit\MediaclipHub\Helper\Data::getPhotobookProduct()
	 * @used-by \Mangoit\MediaclipHub\Helper\Data::getPrintProduct()
	 * @used-by \Mangoit\MediaclipHub\Setup\InstallSchema::install()
	 */
	const F__PLU = 'plu';

	/**
	 * 2019-03-12
	 * @used-by label()
	 * @used-by \Mangoit\MediaclipHub\Block\Adminhtml\Product\Edit\Tab\ProductInformation::_prepareForm()
	 * @used-by \Mangoit\MediaclipHub\Block\Adminhtml\Product\Grid::_prepareColumns()
	 * @used-by \Mangoit\MediaclipHub\Helper\Data::getGiftingProduct()
	 * @used-by \Mangoit\MediaclipHub\Helper\Data::getPhotobookProduct()
	 * @used-by \Mangoit\MediaclipHub\Helper\Data::getPrintProduct()
	 * @used-by \Mangoit\MediaclipHub\Setup\InstallSchema::install()
	 */
	const F__LABEL = 'product_label';

	/**
	 * 2019-04-11
	 * @used-by pwintyProduct()
	 * @used-by \Mangoit\MediaclipHub\Block\Adminhtml\Product\Edit\Tab\ProductInformation::_prepareForm()
	 * @used-by \Mangoit\MediaclipHub\Setup\UpgradeSchema::upgrade()
	 */
	const F__PWINTY_PRODUCT_NAME = 'pwinty_product_name';

	/**
	 * 2019-05-01
	 * @used-by frameColor()
	 * @used-by \Mangoit\MediaclipHub\Block\Adminhtml\Product\Edit\Tab\ProductInformation::_prepareForm()
	 * @used-by \Inkifi\Pwinty\Setup\UpgradeSchema::_process()
	 */
	const F__PWINTY_SHIPPING_METHOD = 'shipping_method';

	/**
	 * 2019-04-11
	 * @used-by \Mangoit\MediaclipHub\Controller\Product\Edit::mProduct()
	 * @param P $p
	 * @param string $module
	 * @return self|null
	 */
	static function byProduct(P $p, $module) {
		$moduleCode = strtolower($module); /** @var string $moduleCode */
		return !($optionId = $p["mediaclip_{$moduleCode}_product"]) ? null :
			self::bySku($optionId, $moduleCode)
		;
	}

	/**
	 * 2019-04-11
	 * @used-by byProduct()
	 * @used-by \Mangoit\MediaclipHub\Controller\Product\Edit::getMediaClipProductSku()
	 * @param string $sku
	 * @param string|int $module
	 * @return self|null
	 */
	static function bySku($sku, $module) {
		$c = ikf_product_c();
		if ($sku) {
			$c->addFieldToFilter(self::F__PLU, $sku);
			$c->addFieldToFilter('module', ctype_digit($module) ? $module : ModulesR::idByCode($module));
		}
		return $c->count() ? $c->getFirstItem() : null;
	}
}