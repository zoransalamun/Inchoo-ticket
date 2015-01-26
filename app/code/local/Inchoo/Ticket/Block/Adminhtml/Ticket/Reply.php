<?php
/*
 * Reply form containter
 */
class Inchoo_Ticket_Block_Adminhtml_Ticket_Reply
    extends Mage_adminhtml_Block_Widget_Form_Container
{

    /*
     * Setup form container paramas
     */
    public function __construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'inchoo_ticket';
        $this->_controller = 'adminhtml_ticket';
        //We need to set mode so form can be located in Reply folder in Adminhtml/Ticket
        $this->_mode = 'reply';

        parent::__construct();
        $ticket = Mage::registry('ticket');

        /*
         * We will remove save button because we don't need it
         */
        $this->_removeButton('save');

        /*
         * Add new button for opening or closing ticket
         * -button will depend on current ticket status
         */
        if ($ticket->getStatus() === 'open'){
            $this->_addButton('close', array(
                'label' => Mage::helper('inchoo_ticket')->__('Close ticket'),
                'onclick' => "window.location='".$this->getUrl('adminhtml/ticket/close/',array('id' => $ticket->getId()))."'",
                'class' => 'save',
            ), 0);
        } else {
            $this->_addButton('open', array(
                'label' => Mage::helper('inchoo_ticket')->__('Open ticket'),
                'onclick' => "window.location='".$this->getUrl('adminhtml/ticket/open/',array('id' => $ticket->getId()))."'",
                'class' => 'save',
            ), 0);
        }

        /*
         * Add reply button
         * -it will be used like save button but reply title is better than save
         *  because it will be used to process admin reply
         */
        $this->_addButton('reply', array(
            'label'     => Mage::helper('inchoo_ticket')->__('Reply'),
            'onclick'   => 'reply_form.submit();',
            'class'     => 'save',
        ), 1);
    }

    /*
     * Header text for reply form page
     */
    public function getHeaderText()
    {
        return $this->escapeHtml(Mage::helper('inchoo_ticket')->__('Ticket view & reply'));
    }
}