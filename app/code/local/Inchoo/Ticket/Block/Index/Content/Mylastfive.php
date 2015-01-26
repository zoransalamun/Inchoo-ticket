<?php
/*
 * Block for ticket index page
 * -it will display customer last five tickets
 */
class Inchoo_Ticket_Block_Index_Content_Mylastfive
    extends Mage_Core_Block_Template
{
    protected $_customerLastTickets;

    /*
     * Setup customer ticket collection
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setupCustomerLastTickets();
    }

    /*
     * Setup last five customer tickets
     */
    protected function setupCustomerLastTickets()
    {
        /*
         * currentUser will be used for limiting collection on customer_id
         * currentWebsite will be used to filter tickets by website id
         */
        $currentUser = Mage::getSingleton('customer/session')->getCustomerId();
        $currentWebsite = Mage::app()->getStore()->getWebsiteId();

        $this->_customerLastTickets = Mage::getModel('inchoo_ticket/ticket')
            ->getCollection()
            ->setPageSize(5)//Max 5 tickets
            ->setOrder('ticket_id','DESC')//order by id to get newest tickets
            ->addFilter('customer_id', $currentUser)
            ->addFilter('website_id', $currentWebsite);
    }

    /*
     * Returns last cutomer tickets collection
     */
    public function getLastTickets()
    {
        return $this->_customerLastTickets;
    }
}