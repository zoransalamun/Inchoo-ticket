<?php
/**
 * Collection for ticket
 */
class Inchoo_Ticket_Model_Resource_Ticket_Collection
    extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /*
     * Define resource model
     */
    public function _construct()
    {
        $this->_init('inchoo_ticket/ticket');
    }
}