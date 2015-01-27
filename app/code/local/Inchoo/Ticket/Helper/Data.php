<?php

class Inchoo_Ticket_Helper_Data extends Mage_Core_Helper_Abstract
{
    /*
     * Get replies number for ticket
     * -we will get collection without loading resource model data,
     *  only count replies
     * status_change - we only count when status change is 0,
     *  becasue reply item with status change greater than 0 is not answer
     */
    public function getRepliesNumber($ticketId = 0)
    {
        $replyCollection = Mage::getModel('inchoo_ticket/reply')
            ->getCollection()
            ->addFilter('ticket_id', $ticketId)
            ->addFilter('status_change', 0);

        return $replyCollection->getSize();
    }

    /*
     * Check string range
     */
    public function isValidLength($string,$minLength,$maxLength) {
        if(strlen($string)>=$minLength && strlen($string)<=$maxLength) {
            return true;
        }
        else return false;
    }
}