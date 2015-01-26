<?php
/*
 * Grid container for ticket grid
 */
class Inchoo_Ticket_Block_Adminhtml_Ticket_List
    extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /*
     * Setup grid container params
     */
    public function __construct()
    {
        $this->_blockGroup = 'inchoo_ticket';
        /*
         * Grid will be displayed in Inchoo/Ticket/Adminhtml/Ticket/List/Grid.php
         */
        $this->_controller = 'adminhtml_ticket_list';
        $this->_headerText = Mage::helper('inchoo_ticket')->__('Ticket list');

        parent::__construct();

        /*
         * Remove add button
         * -we don't need add button becasue admin will not ask question to himself
         */
        $this->removeButton('add');
    }
}