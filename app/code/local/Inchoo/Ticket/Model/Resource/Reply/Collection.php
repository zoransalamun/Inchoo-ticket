<?php
/**
 * Collection for reply
 */
class Inchoo_Ticket_Model_Resource_Reply_Collection
    extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /*
     * Define resource model
     */
    public function _construct()
    {
        $this->_init('inchoo_ticket/reply');
    }
}