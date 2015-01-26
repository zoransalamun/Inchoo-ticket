<?php
/*
 * Form block for ticket reply
 */
class Inchoo_Ticket_Block_View_Content_Form
    extends Mage_Core_Block_Template
{
    /*
     * Return input value if validation failed
     */
    public function getInputValue($key = '')
    {
        $post = $this->getRequest()->getPost();
        if($post) {
            return $this->getRequest()->getParam($key);
        } else {
            if($key === 'url_slike') {
                return Mage::getStoreConfig('inchoo/opcenito/url_slike');
            }
        }
    }

    /*
     * Get loaded ticket id
     */
    public function getTicketId()
    {
        $ticket =  Mage::registry('ticket');
        if($ticket) {
            $ticketId = $ticket->getTicketId();
            if ($ticketId > 0) {
                return $ticketId;
            }
        }
        return 0;
    }
}