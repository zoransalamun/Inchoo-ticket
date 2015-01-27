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

    /*
      * After save increase ticket replies number
      */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        //Check if object is new
        if($object->isObjectNew()) {
            if($object->getStatusChange() == 0) {
                //Increment ticket replies number
                $tableName = $this->getTable('inchoo_ticket/ticket');
                $sql = 'UPDATE `' . $tableName . '`'
                    . ' SET replies=replies+1 '
                    . ' WHERE ticket_id=? ';

                $this->_getConnection('write')->query($sql, array(
                    $object->getTicketId()
                ));
            }
        }
    }

}