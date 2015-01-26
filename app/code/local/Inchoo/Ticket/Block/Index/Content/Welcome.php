<?php
/*
 * Welcome block for Inchoo ticket index
 */
class Inchoo_Ticket_Block_Index_Content_Welcome
    extends Mage_Core_Block_Template
{
    public function getCustomerName()
    {
        return Mage::getSingleton('customer/session')->getCustomer()->getName();
    }
}