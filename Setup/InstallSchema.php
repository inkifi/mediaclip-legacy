<?php
namespace Mangoit\MediaclipHub\Setup;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Mangoit\MediaclipHub\Model\Product as mProduct;
class InstallSchema implements InstallSchemaInterface {
    /**
     * {@inheritdoc}
     */
    function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
    
        $installer = $setup;

        $installer->startSetup();

        /**
         * Create table 'mediacliphub_modules'
         */
        $table1 = $installer->getConnection()->newTable(
            $installer->getTable('mediacliphub_modules')
        )
        ->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'mediacliphub_modules'
        )
        ->addColumn(
            'module_code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'Module Code'
        )
        ->addColumn(
            'module_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'Module Name'
        )->addColumn(
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
        )
        ->setComment(
            'Mangoit MediaclipHub mediacliphub_modules'
        );
        /**
         * Create table 'mediacliphub_product'
         */
        $table2 = $installer->getConnection()->newTable(
            $installer->getTable('mediacliphub_product')
        )
        ->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'mediacliphub_product'
        )
        ->addColumn(
            mProduct::F__LABEL,
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'Product Label'
        )->addColumn(
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
        )
        ->addColumn(
            mProduct::F__PLU,
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'PLU'
        )->addColumn(
            'product_theme',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'Mediaclip Product Theme Associated'
        )->addColumn(
            'product_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'Mediaclip Product ID'
        )
        ->addColumn(
            'module',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'Module'
        )
        ->addColumn(
            mProduct::F__FRAME_COLOUR,
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            '64k',
            [],
            'Frame Colour'
        )
        ->addColumn(
            mProduct::F__PWINTY_PRODUCT_NAME,
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            '64k',
            [],
            'Pwinty Product'
        )
        ->addColumn(
            'dust_jacket_popup',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            '64k',
            [],
            'Dust Jacket Popup'
        )
        ->addColumn(
            mProduct::F__FTP_JSON,
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            '64k',
            [],
            'Ftp Json'
        )
        
        ->setComment(
            'Mangoit MediaclipHub mediacliphub_product'
        );
        /**
         * Create table 'mediacliphub_theme'
         */
        $table3 = $installer->getConnection()->newTable(
            $installer->getTable('mediacliphub_theme')
        )
        ->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'ID'
        )
        ->addColumn(
            'label',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'Mediaclip Theme Label'
        )->addColumn(
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
        )
        ->addColumn(
            'theme_url',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'Mediaclip Theme URL'
        )
        ->setComment(
            'Mangoit MediaclipHub mediacliphub_theme'
        );
        /**
         * Create table 'mediacliphub_supplier'
         */
        $table4 = $installer->getConnection()->newTable(
            $installer->getTable('mediacliphub_supplier')
        )
        ->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'ID'
        )
        ->addColumn(
            'title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'Mediaclip Supplier Name'
        )->addColumn(
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
        )
        ->addColumn(
            'domain',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'Mediaclip Supplier Domain'
        )->addColumn(
            'value',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'Mediaclip Supplier Value'
        )
        ->setComment(
            'Mangoit MediaclipHub mediacliphub_supplier'
        );
        
        $installer->getConnection()->createTable($table1);
        $installer->getConnection()->createTable($table2);
        $installer->getConnection()->createTable($table3);
        $installer->getConnection()->createTable($table4);
        $installer->endSetup();
    }
}
