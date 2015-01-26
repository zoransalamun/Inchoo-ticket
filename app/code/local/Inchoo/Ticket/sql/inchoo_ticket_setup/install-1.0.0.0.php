<?php
/*
 * Installation for Inchoo ticket manager
 */
$installer = $this;
$installer->startSetup();

/*
 * Create table for tickets
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('inchoo_ticket/ticket'))
    ->addColumn('ticket_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Ticket ID')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT
    ), 'Created at')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ), 'Website id')
    ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ), 'Customer id')
    ->addColumn('replies', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ), 'Replies')
    ->addColumn('subject', Varien_Db_Ddl_Table::TYPE_TEXT, 256, array(
        'nullable'  => false,
    ), 'Subject')
    ->addColumn('message', Varien_Db_Ddl_Table::TYPE_TEXT, 10000, array(
        'nullable'  => false,
    ), 'Message')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
        'nullable'  => false,
    ), 'Status');
$installer->getConnection()->createTable($table);

/*
 * Create table for replies
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('inchoo_ticket/reply'))
    ->addColumn('reply_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Reply ID')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT
    ), 'Created at')
    ->addColumn('ticket_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ), 'Ticket id')
    ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ), 'Customer id')
    ->addColumn('admin_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ), 'Admin id')
    ->addColumn('status_change', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ), 'status_change')
    ->addColumn('message', Varien_Db_Ddl_Table::TYPE_TEXT, 10000, array(
        'nullable'  => false,
    ), 'Message');
$installer->getConnection()->createTable($table);