<?php
/**
 * Content block for listing users tickets
 */
class Inchoo_Ticket_Block_List_Content
    extends Mage_Core_Block_Template
{
    /*
     * Setup data for list
     */
    public function __construct()
    {
        parent::__construct();

        $currentWebsite = Mage::app()->getStore()->getWebsiteId();

        $tickets = Mage::getModel('inchoo_ticket/ticket')
            ->getCollection()
            ->addFieldToSelect('*')
            ->addFieldToFilter('customer_id', Mage::getSingleton('customer/session')->getCustomer()->getId())
            ->setOrder('ticket_id', 'DESC')
            ->addFilter('website_id', $currentWebsite)
        ;

        //Set tickets to block data
        $this->setTickets($tickets);
    }

    /*
     * Prepare layout for ticket list
     * -also create new block for pager and set collection to pager
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $pager = $this->getLayout()->createBlock('page/html_pager', 'sales.order.history.pager')
            ->setCollection($this->getTickets());
        $this->setChild('pager', $pager);
        $this->getTickets()->load();
        return $this;
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    public function getViewUrl($order)
    {
        return $this->getUrl('*/*/view', array('order_id' => $order->getId()));
    }


}