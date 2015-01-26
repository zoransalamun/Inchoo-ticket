<?php
/**
 * Resource model for reply
 */
class Inchoo_Ticket_Model_Resource_Reply
    extends Mage_Core_Model_Resource_Db_Abstract
{
    /*
     * Define table
     */
    protected function _construct()
    {
        $this->_init('inchoo_ticket/reply', 'reply_id');
    }
}