<?php
/*
 * Block for ticket data
 * -ticket is loaded in viewAction in frontend controller
 */
class Inchoo_Ticket_Block_View_Content_Ticket
    extends Mage_Core_Block_Template
{
    /*
     * Get saved ticket data
     */
    public function getTicket()
    {
        return Mage::registry('ticket');
    }

    /*
     * Get customer name from save ticket data
     */
    public function getCustomerName()
    {
        $customerId = Mage::registry('ticket')->getCustomerId();
        if($customerId>0) {
            $customer = Mage::getModel('customer/customer')->load($customerId);
            return $customer->getName();
        }
        else return '';
    }
}