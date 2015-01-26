<?php
/**
 * Replies list in admin reply form
 */

class Inchoo_Ticket_Block_Adminhtml_Ticket_Replies_List
    extends Mage_Core_Block_Template
{
    /*
     * Set template
     */
    protected function _construct()
    {
        $this->setTemplate('inchoo/replieslist.phtml');
        parent::_construct();
    }

    public function getReplies()
    {
        //Get current ticket, we need id for replies collection
        $ticket = Mage::registry('ticket');

        $replies = Mage::getModel('inchoo_ticket/reply')
            ->getCollection()
            ->setOrder('reply_id','DESC')
            ->addFilter('ticket_id', $ticket->getId());

        return $replies;
    }

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