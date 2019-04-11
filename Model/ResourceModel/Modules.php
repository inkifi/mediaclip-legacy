<?php
namespace Mangoit\MediaclipHub\Model\ResourceModel;
// 2019-04-11
class Modules extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {
    /** 2019-04-11 */
    function _construct() {$this->_init('mediacliphub_modules', 'id');}

	/**
	 * 2019-04-11
	 * @used-by \Mangoit\MediaclipHub\Model\Product::bySku()
	 * @param string $code
	 * @return int
	 */
    static function idByCode($code) {return df_fetch_col_int(
    	'mediacliphub_modules', 'id', 'module_code', strtolower($code)
	);}
}
