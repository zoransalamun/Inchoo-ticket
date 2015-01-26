<?php
/**
 * Reply model
 */
class Inchoo_Ticket_Model_Reply
    extends Mage_Core_Model_Abstract
{
    /*
     * Add resource model on construct
     */
    protected function _construct()
    {
        $this->_init('inchoo_ticket/reply');
    }
}