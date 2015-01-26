<?php
/**
 * Ticket model
 */
class Inchoo_Ticket_Model_Ticket
    extends Mage_Core_Model_Abstract
{
    /*
     * Add resource model on construct
     */
    protected function _construct()
    {
        $this->_init('inchoo_ticket/ticket');
    }
}