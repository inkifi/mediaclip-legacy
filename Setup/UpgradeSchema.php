<?php
namespace Mangoit\MediaclipHub\Setup;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Mangoit\MediaclipHub\Model\Orders as MO;
use Mangoit\MediaclipHub\Model\Product as mProduct;
class UpgradeSchema implements UpgradeSchemaInterface {
	private $attributeSetFactory;
	private $categorySetupFactory;
	private $eavSetupFactory;

	function __construct(
		EavSetupFactory $eavSetupFactory,
		AttributeSetFactory $attributeSetFactory,
		CategorySetupFactory $categorySetupFactory,
		ModuleDataSetupInterface $setup2
	) {
		$this->eavSetupFactory = $eavSetupFactory;
		$this->attributeSetFactory = $attributeSetFactory;
		$this->categorySetupFactory = $categorySetupFactory;
		$this->setup2 = $setup2;
	}

	function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
	{
		$setup->startSetup();

		if (version_compare($context->getVersion(), '1.0.1') < 0) {
			$eavSetup = $this->eavSetupFactory->create(['setup' => $this->setup2]);

			$attributeSetGroup = 'Mediaclip Options';
			$attributeSetGroup2 = 'Mediaclip Additional Tabs';
			$eavSetup->addAttribute(
				\Magento\Catalog\Model\Product::ENTITY,
				'media_clip_product',
				[
					'class' => '',
					'backend' => '',
					'default' => '',
					'frontend' => '',
					'visible' => true,
					'label' => 'Enable MediaClip',
					'required' => false,
					'input' => 'boolean',
					'unique' => false,
					'searchable' => false,
					'filterable' => false,
					'comparable' => false,
					'user_defined' => false,
					'visible_on_front' => false,
					'group' => $attributeSetGroup,
					'used_in_product_listing' => false,
					//'source' => 'Mangoit\Sales\Model\Attribute\Source\Models',
					'source' => '',
					'sort_order' => 1,
					'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
				]
			);
			$eavSetup->addAttribute(
				\Magento\Catalog\Model\Product::ENTITY,
				'mediaclip_module',
				[
					'class' => '',
					'backend' => '',
					'default' => '',
					'type' => 'varchar',
					'frontend' => '',
					'visible' => true,
					'label' => 'Select MediaClip Module',
					'required' => true,
					'input' => 'select',
					'unique' => false,
					'searchable' => false,
					'filterable' => false,
					'comparable' => false,
					'user_defined' => false,
					'visible_on_front' => false,
					'group' => $attributeSetGroup,
					'used_in_product_listing' => false,
					//'source' => 'Mangoit\Sales\Model\Attribute\Source\Models',
					'source' => 'Mangoit\MediaclipHub\Model\Attribute\Source\Type\Mediaclipmodule',
					'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
					'sort_order' => 2,
				]
			);
			$eavSetup->addAttribute(
				\Magento\Catalog\Model\Product::ENTITY,
				'mediaclip_photobook_product',
				[
					'class' => '',
					'backend' => '',
					'default' => '',
					'type' => 'varchar',
					'frontend' => '',
					'visible' => true,
					'label' => 'Select Photobook Product',
					'required' => true,
					'input' => 'select',
					'unique' => false,
					'searchable' => false,
					'filterable' => false,
					'comparable' => false,
					'user_defined' => false,
					'visible_on_front' => false,
					'group' => $attributeSetGroup,
					'used_in_product_listing' => false,
					//'source' => 'Mangoit\Sales\Model\Attribute\Source\Models',
					'source' => 'Mangoit\MediaclipHub\Model\Attribute\Source\Type\Photobookproduct',
					'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
					'sort_order' => 3,
				]
			);
			$eavSetup->addAttribute(
				\Magento\Catalog\Model\Product::ENTITY,
				'mediaclip_gifting_product',
				[
					'class' => '',
					'backend' => '',
					'default' => '',
					'type' => 'varchar',
					'frontend' => '',
					'visible' => true,
					'label' => 'Select Gifting Product',
					'required' => true,
					'input' => 'select',
					'unique' => false,
					'searchable' => false,
					'filterable' => false,
					'comparable' => false,
					'user_defined' => false,
					'visible_on_front' => false,
					'group' => $attributeSetGroup,
					'used_in_product_listing' => false,
					//'source' => 'Mangoit\Sales\Model\Attribute\Source\Models',
					'source' => 'Mangoit\MediaclipHub\Model\Attribute\Source\Type\Giftingproduct',
					'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
					'sort_order' => 4,
				]
			);
			$eavSetup->addAttribute(
				\Magento\Catalog\Model\Product::ENTITY,
				'mediaclip_print_product',
				[
					'class' => '',
					'backend' => '',
					'default' => '',
					'type' => 'varchar',
					'frontend' => '',
					'visible' => true,
					'label' => 'Select Print Product',
					'required' => true,
					'input' => 'select',
					'unique' => false,
					'searchable' => false,
					'filterable' => false,
					'comparable' => false,
					'user_defined' => false,
					'visible_on_front' => false,
					'group' => $attributeSetGroup,
					'used_in_product_listing' => false,
					//'source' => 'Mangoit\Sales\Model\Attribute\Source\Models',
					'source' => 'Mangoit\MediaclipHub\Model\Attribute\Source\Type\Printsproduct',
					'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
					'sort_order' => 4,
				]
			);
			$eavSetup->addAttribute(
				\Magento\Catalog\Model\Product::ENTITY,
				'media_clip_extrasheetamt',
				[
					'class' => '',
					'backend' => '',
					'default' => '',
					'frontend' => '',
					'visible' => true,
					'label' => 'Photobook Additional Sheet Price',
					'required' => false,
					'input' => 'text',
					'unique' => false,
					'searchable' => false,
					'filterable' => false,
					'comparable' => false,
					'user_defined' => false,
					'visible_on_front' => false,
					'group' => $attributeSetGroup,
					'used_in_product_listing' => false,
					//'source' => 'Mangoit\Sales\Model\Attribute\Source\Models',
					'source' => '',
					'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
					'sort_order' => 2,
				]
			);
			$eavSetup->addAttribute(
				\Magento\Catalog\Model\Product::ENTITY,
				'mediaclip_dustjacket_popup',
				[
					'class' => '',
					'backend' => '',
					'default' => '',
					'frontend' => '',
					'visible' => true,
					'label' => 'Select Dust Jacket Popup Type',
					'required' => true,
					'type' => 'varchar',
					'input' => 'select',
					'unique' => false,
					'searchable' => false,
					'filterable' => false,
					'comparable' => false,
					'user_defined' => false,
					'visible_on_front' => false,
					'group' => $attributeSetGroup,
					'used_in_product_listing' => false,
					//'source' => 'Mangoit\Sales\Model\Attribute\Source\Models',
					'source' => 'Mangoit\MediaclipHub\Model\Attribute\Source\Type\Dustjacketpopup',
					'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
					'sort_order' => 7,
				]
			);
			$eavSetup->addAttribute(
				\Magento\Catalog\Model\Product::ENTITY,
				self::P__UPLOAD_FOLDER,
				[
					'class' => '',
					'backend' => '',
					'default' => '',
					'frontend' => '',
					'visible' => true,
					'label' => 'MediaClip Upload Folder',
					'required' => true,
					'type' => 'varchar',
					'input' => 'select',
					'unique' => false,
					'searchable' => false,
					'filterable' => false,
					'comparable' => false,
					'user_defined' => false,
					'visible_on_front' => false,
					'group' => $attributeSetGroup,
					'used_in_product_listing' => false,
					//'source' => 'Mangoit\Sales\Model\Attribute\Source\Models',
					'source' => 'Mangoit\MediaclipHub\Model\Attribute\Source\Type\Mediaclipuploadfolder',
					'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
					'sort_order' => 7,
				]
			);
			$eavSetup->addAttribute(
				\Magento\Catalog\Model\Product::ENTITY,
				'mediaclip_extratab_one_title',
				[
					'class' => '',
					'backend' => '',
					'default' => '',
					'frontend' => '',
					'visible' => true,
					'label' => 'Extra Tab-1 Title',
					'required' => false,
					'input' => 'text',
					'unique' => false,
					'searchable' => false,
					'filterable' => false,
					'comparable' => false,
					'user_defined' => false,
					'visible_on_front' => false,
					'group' => $attributeSetGroup2,
					'used_in_product_listing' => false,
					//'source' => 'Mangoit\Sales\Model\Attribute\Source\Models',
					'source' => '',
					'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
					'sort_order' => 1,
				]
			);
			$eavSetup->addAttribute(
				\Magento\Catalog\Model\Product::ENTITY,
				'mediaclip_extratab_one_content',
				[
					'class' => '',
					'backend' => '',
					'default' => '',
					'frontend' => '',
					'visible' => true,
					'label' => 'Extra Tab-1 Content',
					'required' => false,
					'type' => 'text', //backend_type
					'input'  => 'textarea',
					'note' => 'Add title for additional tab for the product.',
					'unique' => false,
					'wysiwyg_enabled' => true,
					'visible_on_front' => true,
					'is_html_allowed_on_front' => true,
					'searchable' => false,
					'filterable' => false,
					'comparable' => false,
					'user_defined' => false,
					'visible_on_front' => false,
					'group' => $attributeSetGroup2,
					'used_in_product_listing' => false,
					//'source' => 'Mangoit\Sales\Model\Attribute\Source\Models',
					'source' => '',
					'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
					'sort_order' => 1,
				]
			);
			$eavSetup->addAttribute(
				\Magento\Catalog\Model\Product::ENTITY,
				'mediaclip_extratab_two_title',
				[
					'class' => '',
					'backend' => '',
					'default' => '',
					'frontend' => '',
					'visible' => true,
					'label' => 'Extra Tab-2 Title',
					'required' => false,
					'input' => 'text',
					'unique' => false,
					'searchable' => false,
					'filterable' => false,
					'comparable' => false,
					'user_defined' => false,
					'visible_on_front' => false,
					'group' => $attributeSetGroup2,
					'used_in_product_listing' => false,
					//'source' => 'Mangoit\Sales\Model\Attribute\Source\Models',
					'source' => '',
					'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
					'sort_order' => 1,
				]
			);
			$eavSetup->addAttribute(
				\Magento\Catalog\Model\Product::ENTITY,
				'mediaclip_extratab_two_content',
				[
					'class' => '',
					'backend' => '',
					'default' => '',
					'frontend' => '',
					'visible' => true,
					'label' => 'Extra Tab-2 Content',
					'required' => false,
					'type' => 'text', //backend_type
					'input'  => 'textarea',
					'note' => 'Add title for additional tab for the product.',
					'unique' => false,
					'wysiwyg_enabled' => true,
					'visible_on_front' => true,
					'is_html_allowed_on_front' => true,
					'searchable' => false,
					'filterable' => false,
					'comparable' => false,
					'user_defined' => false,
					'visible_on_front' => false,
					'group' => $attributeSetGroup2,
					'used_in_product_listing' => false,
					//'source' => 'Mangoit\Sales\Model\Attribute\Source\Models',
					'source' => '',
					'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
					'sort_order' => 1,
				]
			);
			$eavSetup->addAttribute(
				\Magento\Catalog\Model\Product::ENTITY,
				'mediaclip_minimum_prints_allow',
				[
					'class' => '',
					'backend' => '',
					'default' => '',
					'frontend' => '',
					'visible' => true,
					'label' => 'Allow Minimum Prints',
					'required' => false,
					'input' => 'boolean', // Input type
					'note' => 'Set this to yes only if want to allow minimum prints for the base price.',
					'unique' => false,
					'searchable' => false,
					'filterable' => false,
					'comparable' => false,
					'user_defined' => false,
					'visible_on_front' => false,
					'group' => $attributeSetGroup,
					'used_in_product_listing' => false,
					//'source' => 'Mangoit\Sales\Model\Attribute\Source\Models',
					'source' => '',
					'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
					'sort_order' => 1,
				]
			);
			$eavSetup->addAttribute(
				\Magento\Catalog\Model\Product::ENTITY,
				'mediaclip_minimum_prints_count',
				[
					'class' => '',
					'backend' => '',
					'default' => '',
					'frontend' => '',
					'visible' => true,
					'label' => 'Min. Prints Allowed',
					'required' => false,
					'input' => 'text', // Input type
					'note' => 'Set minimum prints allowed to purchase in base price.',
					'unique' => false,
					'searchable' => false,
					'filterable' => false,
					'comparable' => false,
					'user_defined' => false,
					'visible_on_front' => false,
					'group' => $attributeSetGroup,
					'used_in_product_listing' => false,
					//'source' => 'Mangoit\Sales\Model\Attribute\Source\Models',
					'source' => '',
					'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
					'sort_order' => 2,
				]
			);
			$eavSetup->addAttribute(
				\Magento\Catalog\Model\Product::ENTITY,
				'mediaclip_extra_prints_price',
				[
					'class' => '',
					'backend' => '',
					'default' => '',
					'frontend' => '',
					'visible' => true,
					'label' => 'Additional Prints Price',
					'required' => false,
					'input' => 'text', // Input type
					'note' => 'Set additional price to apply per additional print after minimum prints allowed.',
					'unique' => false,
					'searchable' => false,
					'filterable' => false,
					'comparable' => false,
					'user_defined' => false,
					'visible_on_front' => false,
					'group' => $attributeSetGroup,
					'used_in_product_listing' => false,
					//'source' => 'Mangoit\Sales\Model\Attribute\Source\Models',
					'source' => '',
					'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
					'sort_order' => 3,
				]
			);
			$eavSetup->addAttribute(
				\Magento\Catalog\Model\Product::ENTITY,
				'mediaclip_product_supplier',
				[
					'class' => '',
					'backend' => '',
					'default' => '',
					'frontend' => '',
					'visible' => true,
					'label' => 'Supplier',
					'required' => true,
					'type'    => 'varchar', //backend_type
					'input'   => 'select',
					'unique' => false,
					'searchable' => false,
					'filterable' => false,
					'comparable' => false,
					'user_defined' => false,
					'visible_on_front' => false,
					'group' => $attributeSetGroup,
					'used_in_product_listing' => false,
					//'source' => 'Mangoit\Sales\Model\Attribute\Source\Models',
					'source' => 'Mangoit\MediaclipHub\Model\Attribute\Source\Type\Mediaclipsuppliers',
					'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
					'sort_order' => 7,
				]
			);
		}
		if (version_compare($context->getVersion(), '1.0.2') < 0) {

			$categorySetup = $this->categorySetupFactory->create(['setup' => $this->setup2]);
			$attributeSet = $this->attributeSetFactory->create();
			$entityTypeId = $categorySetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
			$attributeSetId = $categorySetup->getDefaultAttributeSetId($entityTypeId); // Default attribute set Id

			$data3 = [
				'attribute_set_name' => 'Print', // custom attribute set name
				'entity_type_id' => $entityTypeId,
				'sort_order' => 50,
			];
			$attributeSet->setData($data3);
			$attributeSet->validate();
			$attributeSet->save();
			$attributeSet->initFromSkeleton($attributeSetId)->save();
		}
		if (version_compare($context->getVersion(), '1.0.3') < 0) {

			$categorySetup = $this->categorySetupFactory->create(['setup' => $this->setup2]);
			$attributeSet = $this->attributeSetFactory->create();
			$entityTypeId = $categorySetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
			$attributeSetId = $categorySetup->getDefaultAttributeSetId($entityTypeId); // Default attribute set Id
			$data1 = [
				'attribute_set_name' => 'Photobook', // custom attribute set name
				'entity_type_id' => $entityTypeId,
				'sort_order' => 50,
			];
			$attributeSet->setData($data1);
			$attributeSet->validate();
			$attributeSet->save();
			$attributeSet->initFromSkeleton($attributeSetId)->save();
			$attributeSet1 = $this->attributeSetFactory->create();
			$entityTypeId1 = $categorySetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
			$attributeSetId1 = $categorySetup->getDefaultAttributeSetId($entityTypeId1); // Default attribute set Id
			$data3 = [
				'attribute_set_name' => 'Gifting', // custom attribute set name
				'entity_type_id' => $entityTypeId1,
				'sort_order' => 50,
			];
			$attributeSet1->setData($data3);
			$attributeSet1->validate();
			$attributeSet1->save();
			$attributeSet1->initFromSkeleton($attributeSetId1)->save();
		}
		if (version_compare($context->getVersion(), '1.0.4') < 0) {

			$table1 = $setup->getConnection()
				->newTable($setup->getTable('mediaclip'))
				->addColumn(
					'id',
					\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					null,
					['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
					'ID'
				)
				->addColumn(
					'project_id',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					null,
					['unsigned' => true],
					'Project ID'
				)
				->addColumn(
					'created_at',
					\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
					null,
					['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
					'Creation Time'
				)
				->addColumn(
					'updated_at',
					\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
					null,
					['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
					'Update Time'
				)->addColumn(
					'user_id',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					null,
					['unsigned' => true],
					'User ID'
				)->addColumn(
					'store_product_id',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					null,
					['unsigned' => true],
					'Store Product ID'
				)->addColumn(
					'project_details',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					null,
					['unsigned' => true],
					'Project Details'
				)->setComment('Mangoit Mediaclip');

				$table2 = $setup->getConnection()
				->newTable($setup->getTable('mediaclip_orders'))
				->addColumn(
					'id',
					\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					null,
					['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
					'ID'
				)
				->addColumn(
					'magento_order_id',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					null,
					['unsigned' => true],
					'Magento Order ID'
				)
				->addColumn(
					'created_at',
					\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
					null,
					['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
					'Creation Time'
				)
				->addColumn(
					'updated_at',
					\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
					null,
					['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
					'Update Time'
				)->addColumn(
					'mediaclip_order_id',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					null,
					['unsigned' => true],
					'Media Clip Id'
				)->addColumn(
					'mediaclip_order_details',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					null,
					['unsigned' => true],
					'Mediaclip Order Details'
				)->addColumn(
					MO::F__DOWNLOAD_STATUS,
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					null,
					['unsigned' => true],
					'Order Download Status'
				)->setComment('Mangoit Mediaclip Orders');

				$setup->getConnection()->createTable($table1);
				$setup->getConnection()->createTable($table2);
		}


		if (version_compare($context->getVersion(), '1.0.5') < 0) {
			$tableName = $setup->getTable('quote_item');
			// Changes here.
			$columns = [
				'mediaclip_project_id' => [
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'nullable' => true,
					'default' => '',
					'comment' => 'Mediaclip Project Id',
				],
			];

			$connection = $setup->getConnection();
			foreach ($columns as $name => $definition) {
				$connection->addColumn($tableName, $name, $definition);
			}
		}
		if (version_compare($context->getVersion(), '1.0.6') < 0) {
			$tableName = $setup->getTable('sales_order_item');
			// Changes here.
			$columns = [
				'mediaclip_project_id' => [
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'nullable' => true,
					'default' => '',
					'comment' => 'Mediaclip Project Id',
				],
				self::OI__ITEM_DOWNLOAD_STATUS => [
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'nullable' => true,
					'default' => 0,
					'comment' => 'Item Download Status',
				],
			];

			$connection = $setup->getConnection();
			foreach ($columns as $name => $definition) {
				$connection->addColumn($tableName, $name, $definition);
			}
		}
		if (version_compare($context->getVersion(), '1.0.7') < 0) {
			$tableName = $setup->getTable('sales_order');
			// Changes here.
			$columns = [
				'mediaclip_order_flag' => [
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					'nullable' => false,
					'default' => 0,
					'comment' => 'Mediaclip Order Flag',
				]
			];

			$connection = $setup->getConnection();
			foreach ($columns as $name => $definition) {
				$connection->addColumn($tableName, $name, $definition);
			}
		}


		if (version_compare($context->getVersion(), '1.0.8') < 0) {
			$tableName = $setup->getTable('mediacliphub_product');
			// Changes here.
			$columns = [
				mProduct::F__INCLUDE_QUANTITY_IN_JSON => [
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					'nullable' => true,
					'default' => 0,
					'comment' => 'Include qantity in json flag',
				]
			];

			$connection = $setup->getConnection();
			foreach ($columns as $name => $definition) {
				$connection->addColumn($tableName, $name, $definition);
			}
		}

		if (version_compare($context->getVersion(), '1.0.9') < 0) {
			$tableName = $setup->getTable('mediacliphub_product');
			// Changes here.
			$columns = [
				mProduct::F__JSON_CODE => [
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'nullable' => false,
					'comment' => 'json code will show in json file',
				]
			];

			$connection = $setup->getConnection();
			foreach ($columns as $name => $definition) {
				$connection->addColumn($tableName, $name, $definition);
			}
		}
		$setup->endSetup();
	}

	/**
	 * 2019-02-26 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
	 * @used-by upgrade()
	 * @used-by \Inkifi\Mediaclip\H\AvailableForDownload\Pwinty::_p()
	 */
	const OI__ITEM_DOWNLOAD_STATUS = 'item_download_status';

	/**
	 * 2019-03-13 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
	 * @used-by upgrade()
	 * @used-by \Inkifi\Mediaclip\Event::folder()
	 */
	const P__UPLOAD_FOLDER = 'mediaclip_upload_folder';
}
