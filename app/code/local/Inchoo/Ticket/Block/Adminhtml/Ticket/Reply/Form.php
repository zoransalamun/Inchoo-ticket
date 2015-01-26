<?php
/*
 * Reply form
 */
class Inchoo_Ticket_Block_Adminhtml_Ticket_Reply_Form
    extends Mage_Adminhtml_Block_Widget_Form
{
    /*
     *  Form fields
     */
    protected function _prepareForm()
    {
        $ticket = Mage::registry('ticket');

        $form   = new Varien_Data_Form(array(
            'id'        => 'reply_form',
            'action'    => $this->getData('action'),
            'method'    => 'post'
        ));

        /*
         * Fieldsed with ticket data it will not disply any input field
         */
        $fieldset   = $form->addFieldset('base_fieldset', array(
            'legend'    => 'Ticked data',
            'class'     => 'fieldset-wide'
        ));

        $fieldset->addField('id', 'hidden', array(
            'name'      => 'id',
            'value'     => $ticket->getId(),
        ));

        $fieldset->addField('created_at', 'note', array(
            'label' => 'Created date:',
            'text' => $ticket->getCreatedAt(),
        ));

        $fieldset->addField('customer_id', 'note', array(
            'label' => 'Customer:',
            'text' => Mage::getModel('customer/customer')->load($ticket->getCustomerId())->getName().' (#'.$ticket->getCustomerId().')',
        ));

        $fieldset->addField('subject', 'note', array(
            'label' => 'Subject:',
            'text' => $this->escapeHtml($ticket->getSubject()),
        ));

        $fieldset->addField('message_ticket', 'note', array(
            'label' => 'Message:',
            'text' => $this->escapeHtml($ticket->getMessage()),
        ));

        $fieldset->addField('status', 'note', array(
            'label' => 'Status:',
            'text' => $ticket->getStatus(),
        ));

        /*
         * Fieldset for reply form
         */
        $fieldsetReply   = $form->addFieldset('reply_fieldset', array(
            'legend'    => 'Reply form',
            'class'     => 'fieldset-wide'
        ));
        if($ticket->getStatus() === 'open') {
            $fieldsetReply->addField('ticket-id', 'hidden', array(
                'name' => 'ticket-id',
                'value' => $ticket->getId()
            ));
            $fieldsetReply->addField('message', 'textarea', array(
                'name' => 'message',
                'label' => 'Reply message',
                'container_id' => 'message_textpolje',
                'value' => ''
            ));
        } else {
            $fieldsetReply->addField('closed_info', 'note', array(
                'label' => 'Reply message:',
                'text' => 'Ticket is closed, please open ticket to reply!',
            ));
        }

        /*
         * Use form_after for new block that will display replies for this ticket
         */
        $this->setChild('form_after', $this->getLayout()->createBlock('inchoo_ticket/adminhtml_ticket_replies_list'));

        //Set form action url
        $form->setAction($this->getUrl('*/*/replyprocess'));

        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}