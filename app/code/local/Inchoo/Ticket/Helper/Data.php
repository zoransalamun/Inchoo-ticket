<?php

class Inchoo_Ticket_Helper_Data extends Mage_Core_Helper_Abstract
{
    /*
     * Get replies number for ticket
     * -we will get collection without loading resource model data,
     *  only count replies
     * status_change - we only count when status change is 0,
     *  becasue reply item with status change greater than 0 is not answer
     */
    public function getRepliesNumber($ticketId = 0)
    {
        $replyCollection = Mage::getModel('inchoo_ticket/reply')
            ->getCollection()
            ->addFilter('ticket_id', $ticketId)
            ->addFilter('status_change', 0);

        return $replyCollection->getSize();
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
        if(Mage::getStoreConfig('ticket_options/ticket_email/email_notification') == 1 &&
            strlen(Mage::getStoreConfig('ticket_options/ticket_email/ticket_email'))>0) {

            $mailer = Mage::getModel('core/email_template_mailer');
            $emailInfo = Mage::getModel('core/email_info');

            $storeId = Mage::app()->getStore()->getStoreId();
            $templateId = 'inchoo_ticket_email_template';
            //Set reciever
            $emailInfo->addTo(Mage::getStoreConfig('ticket_options/ticket_email/ticket_email'), 'Ticket admin');

            $mailer->addEmailInfo($emailInfo);

            //Email sender
            $mailer->setSender(Mage::getStoreConfig('trans_email/ident_general/email'));

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
                ->setIsForceCheck(false);

            $mailer->setQueue($emailQueue)->send();
        }
    }

    /*
     * Check string range
     */
    public function isValidLength($string,$minLength,$maxLength) {
        if(strlen($string)>=$minLength && strlen($string)<=$maxLength) {
            return true;
        }
        else return false;
    }
}