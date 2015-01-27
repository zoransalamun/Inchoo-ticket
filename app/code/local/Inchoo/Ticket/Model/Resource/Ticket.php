<?php
/**
 * Resource model for ticket
 */
class Inchoo_Ticket_Model_Resource_Ticket
    extends Mage_Core_Model_Resource_Db_Abstract
{
    /*
     * Define table
     */
    protected function _construct()
    {
        $this->_init('inchoo_ticket/ticket', 'ticket_id');
    }

    /*
     * Before save set created at
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        //Check if object is new
        if($object->isObjectNew()) {
            $object->setCreatedAt(Varien_Date::now());
        }
    }
}