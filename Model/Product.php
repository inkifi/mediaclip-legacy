<?php
namespace Mangoit\MediaclipHub\Model;
class Product extends \Magento\Framework\Model\AbstractModel {
    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
	 * 2019-02-27
	 * @override
	 * @see \Magento\Framework\Model\AbstractModel::_construct()
     * @return void
     */
    protected function _construct() {$this->_init('Mangoit\MediaclipHub\Model\ResourceModel\Product');}

    function getMediaClipProductByLabel($label, $_module){
        $collection = $this->getCollection();
        if ($label) {
            $collection->addFieldToFilter('product_label', $label);
        }
        if ($_module) {
            $collection->addFieldToFilter('module', $_module);
        }
        return $collection->getData();
    }

    function getMediaClipProductBySku($sku, $module){
        $collection = $this->getCollection();
        if ($sku) {
            $collection->addFieldToFilter('plu', $sku);
            $collection->addFieldToFilter('module', $module);
        }
        return $collection->getData();
    }

	/**
	 * 2019-02-27
	 * @used-by \Inkifi\Mediaclip\H\AvailableForDownload\Pureprint::pOI()
	 * @return bool
	 */
    function includeQuantityInJson() {return !!$this[self::F__INCLUDE_QUANTITY_IN_JSON];}

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
}