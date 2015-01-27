<?php
/**
 * Ticket model
 */
class Inchoo_Ticket_Model_Ticket
    extends Mage_Core_Model_Abstract
{
    /*
     * Add resource model on construct
     */
    protected function _construct()
    {
        $this->_init('inchoo_ticket/ticket');
    }

    /*
     * Send email notification
     *  -it will queue email
     */
    public function emailNotification($newTicket,$currentUser)
    {
        /*
         * Check if admin option for notification is equal to yes(1), and
         * check if email in admin options is entered
         */
        if(Mage::getStoreConfig('ticket_options/ticket_email/email_notification') &&
            strlen(Mage::getStoreConfig('ticket_options/ticket_email/ticket_email'))>0) {

            $mailer = Mage::getModel('core/email_template_mailer');
            $emailInfo = Mage::getModel('core/email_info');

            $storeId = Mage::app()->getStore()->getStoreId();
            $templateId = 'inchoo_ticket_email_template';
            //Set reciever
            $emailInfo->addTo(Mage::getStoreConfig('ticket_options/ticket_email/ticket_email'), 'Ticket admin');

            $mailer->addEmailInfo($emailInfo);

            //Email sender
            $mailer->setSender('general');//blok za send naziv u configuration/general/Store Email Addresses/
                                          //on spaja trans_email/ident_ SENDER /name i /email

            $mailer->setStoreId($storeId);
            $mailer->setTemplateId($templateId);
            $mailer->setTemplateParams(array(
                'ticket' => $newTicket,
                'customer' => $currentUser
            ));

            /** @var $emailQueue Mage_Core_Model_Email_Queue */
            $emailQueue = Mage::getModel('core/email_queue');
            $emailQueue->setEntityId($newTicket->getId())
                ->setEntityType('ticket')
                ->setEventType('new_ticket')
                ->setIsForceCheck(true);

            $mailer->setQueue($emailQueue)->send();
        }
    }
}