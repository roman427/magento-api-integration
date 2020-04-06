<?php
$installer = $this;
$installer->startSetup();

$table = $installer->getConnection()
	->newTable($installer->getTable('smsandvoucher/resultdata'))
	->addColumn('resultdata_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'identity' => true,
		'unsigned' => true,
		'nullable' => false,
		'primary' => true,
	), 'Id')
	->addColumn('resultdata', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
		'nullable' => false,
	), 'Result Data');
$installer->getConnection()->createTable($table);
$installer->endSetup();