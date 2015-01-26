<?php
/*
 * Content block for view ticket
 */
class Inchoo_Ticket_Block_View_Content
    extends Mage_Core_Block_Template
{
    /*
     * Return loaded ticket saved in ticket registry
     */
    public function getTicket()
    {
        return Mage::registry('ticket');
    }

    /*
     * Return loaded ticket id saved in ticket registry
     */
    public function getTicketId()
    {
        return Mage::registry('ticket') ? Mage::registry('ticket')->getTicketId() : 0;
    }

}