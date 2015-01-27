<?php
/*
 * Upgrade for version 1.0.0.1
 */

/*
 * Tickets table
 */
$installer  = $this;

//customer_id index
$indexFields = array('customer_id');
$installer->getConnection()->addIndex(
    $installer->getTable('inchoo_ticket/ticket'),
    $installer->getIdxName('inchoo_ticket/ticket', $indexFields),
    $indexFields
);

//Remove created at column default value
$installer->getConnection()->modifyColumn(
    $installer->getTable('inchoo_ticket/ticket'),//tableName
    'created_at',//columnName
    array(
        'type' => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        'nullable' => true,
        'default' => null,
        'comment' => 'Created At'
    )//definition
);


/*
 * Replies table
 */

//customer_id index
$indexFields = array('customer_id');
$installer->getConnection()->addIndex(
    $installer->getTable('inchoo_ticket/reply'),
    $installer->getIdxName('inchoo_ticket/reply', $indexFields),
    $indexFields
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(//$fkName
        'inchoo_ticket/reply',
        'ticket_id',
        'inchoo_ticket/ticket',
        'ticket_id'
    ),
    $installer->getTable('inchoo_ticket/reply'),//$tableName
    'ticket_id',//$columnName
    $installer->getTable('inchoo_ticket/ticket'),//$refTableName
    'ticket_id');

//Remove created at column default value
$installer->getConnection()->modifyColumn(
    $installer->getTable('inchoo_ticket/reply'),//tableName
    'created_at',//columnName
    array(
        'type' => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        'nullable' => true,
        'default' => null,
        'comment' => 'Created At'
    )//definition
);