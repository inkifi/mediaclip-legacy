<?php
namespace Mangoit\MediaclipHub\Model;
use Mangoit\MediaclipHub\Model\ResourceModel\Product as R;
class Product extends \Magento\Framework\Model\AbstractModel {
	/**
	 * 2019-02-27
	 * @override
	 * @see \Magento\Framework\Model\AbstractModel::_construct()
	 * @return void
	 */
	protected function _construct() {$this->_init(R::class);}

	function getMediaClipProductByLabel($label, $_module){
		$collection = $this->getCollection();
		if ($label) {
			$collection->addFieldToFilter(self::F__LABEL, $label);
		}
		if ($_module) {
			$collection->addFieldToFilter('module', $_module);
		}
		return $collection->getData();
	}

	function getMediaClipProductBySku($sku, $module){
		$c = ikf_product_c();
		if ($sku) {
			$c->addFieldToFilter(self::F__PLU, $sku);
			$c->addFieldToFilter('module', $module);
		}
		return $c->getData();
	}

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
	 * @used-by ikf_product()
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
	 * 2019-02-27
	 * @used-by \Inkifi\Mediaclip\H\AvailableForDownload\Pureprint::pOI()
	 * @return bool
	 */
	function sendJson() {return !!$this[self::F__FTP_JSON];}

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
	 * @used-by getMediaClipProductByLabel()
	 * @used-by \Mangoit\MediaclipHub\Block\Adminhtml\Product\Edit\Tab\ProductInformation::_prepareForm()
	 * @used-by \Mangoit\MediaclipHub\Block\Adminhtml\Product\Grid::_prepareColumns()
	 * @used-by \Mangoit\MediaclipHub\Helper\Data::getGiftingProduct()
	 * @used-by \Mangoit\MediaclipHub\Helper\Data::getPhotobookProduct()
	 * @used-by \Mangoit\MediaclipHub\Helper\Data::getPrintProduct()
	 * @used-by \Mangoit\MediaclipHub\Setup\InstallSchema::install()
	 */
	const F__LABEL = 'product_label';
}