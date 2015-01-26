<?php
/*
 * Block for displaying ticket replies
 */
class Inchoo_Ticket_Block_View_Content_Reply
    extends Mage_Core_Block_Template
{
    protected $_replies;

    protected function _construct()
    {
        parent::_construct();
        $this->setupReplies();
    }

    /*
     * Setup replies collection data
     *  -ticket data is in registry, loaded by ticket view content block
     */
    protected function setupReplies()
    {
        $ticket =  Mage::registry('ticket');
        if($ticket) {
            $ticketId = $ticket->getTicketId();
            if ($ticketId > 0) {
                $this->_replies = Mage::getModel('inchoo_ticket/reply')
                    ->getCollection()
                    ->setOrder('reply_id','DESC')
                    ->addFilter('ticket_id', $ticketId);
            }
        }
    }

    /*
     * Get saved replies
     */
    public function getReplies()
    {
        return $this->_replies;
    }

    /*
     * We need to check first who has replied
     *  -customer and admin user can reply so we will return
     *   name of customer or admin
     */
    public function getUserName($customerId = 0, $adminId = 0)
    {
        if($customerId>0) {
            $customer = Mage::getModel('customer/customer')
                ->load($customerId);
            return $customer->getName();
        } else if($adminId>0) {
            $admin = Mage::getModel('admin/user')
                ->load($adminId);
            $test = $admin->getFirstname().' '.$admin->getLastname();
            return $admin->getFirstname().' '.$admin->getLastname();
        }
    }

    /*
     * Retrieve text for status change
     */
    public function getStatusChangeText($status=0,$createdAt = '')
    {
        switch($status) {
            case 1: {
                return 'has changed at '.$createdAt.' ticket status to open.';break;
            }
            case 2: {
                return 'has changed at '.$createdAt.' ticket status to closed.';break;
            }
            default : {
                return 'has changed at '.$createdAt.' ticket status to unknown :/';break;
            }
        }
    }

    /*
     * Retrieve status change background
     */
    public function getStatusChangeBackground($status=0)
    {
        switch ($status) {
            case 1: { return '#AAFF7F'; break; }
            case 2: { return '#FFA28E'; break; }
            default : { return '#E8E0D1'; break; }
        }
    }

}