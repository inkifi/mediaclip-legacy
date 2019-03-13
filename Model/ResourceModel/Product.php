<?php
namespace Mangoit\MediaclipHub\Model\ResourceModel;
// 2019-03-13
class Product extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {
	/** 2019-03-13 */
	function _construct() {$this->_init('mediacliphub_product', 'id');}
}
