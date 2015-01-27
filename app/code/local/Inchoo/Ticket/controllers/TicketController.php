<?php
/**
 * Ticket Controller for frontend
 */
class Inchoo_Ticket_TicketController extends Mage_Core_Controller_Front_Action
{
    const FLAG_NO_DISPATCH              = 'no-dispatch';

    /*
     * Get customer session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }

    public function preDispatch()
    {
        parent::preDispatch();

        if (!$this->_getSession()->isLoggedIn()) {
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            $this->_redirect('customer/account/login/');
            return;
        }
    }

    /*
     * Default action
     * -home page for ticket
     */
    public function indexAction()
    {
        //User must be logged in
        //$this->onlyLoggedInUser();

        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->renderLayout();
    }

    /*
     * Add new ticket
     */
    public function newAction()
    {
        //User must be logged in
        //$this->onlyLoggedInUser();
        $this->loadLayout();
        $newTicketId = 0;

        /*
         * Validation
         */
        $post = $this->getRequest()->getPost();
        if($post) {
            $errors = false;

            if (!Zend_Validate::is(trim($post['subject']) , 'NotEmpty')) {
                Mage::getSingleton('customer/session')->addError('Please enter subject!');
                $errors = true;
            } else {
                //Check length
                if(!Mage::helper('inchoo_ticket')->isValidLength($post['subject'],1,250)) {
                    Mage::getSingleton('customer/session')->addError('Subject is not in required range!(Minimum 1 character and maximum 250 characters)');
                    $errors = true;
                }
            }

            if (!Zend_Validate::is(trim($post['message']), 'NotEmpty')) {
                Mage::getSingleton('customer/session')->addError('Please enter message!');
                $errors = true;
            } else {
                //Check length
                if(!Mage::helper('inchoo_ticket')->isValidLength($post['message'],1,5000)) {
                    Mage::getSingleton('customer/session')->addError('Message is not in required range!(Minimum 1 character and maximum 5000 characters)');
                    $errors = true;
                }
            }

            if(!$errors) {
                /*
                 * Creating new ticket
                 */
                $currentUser = Mage::getSingleton('customer/session');
                $currentWebsite = Mage::app()->getStore()->getWebsiteId();

                $newTicket = Mage::getModel('inchoo_ticket/ticket');
                $newTicket->setSubject($this->getRequest()->getParam('subject'));
                $newTicket->setMessage($this->getRequest()->getParam('message'));
                $newTicket->setWebsiteId($currentWebsite);
                $newTicket->setCustomerId($currentUser->getCustomerId());
                $newTicket->setStatus('open');

                $newTicket->save();
                if($newTicket->getTicketId()) {
                    $newTicketId = $newTicket->getTicketId();
                    Mage::getSingleton('customer/session')->addSuccess('You have successfully added new ticket!');
                    /*
                     * Send email to admin
                     */
                    $newTicket->emailNotification($newTicket,$currentUser);

                } else {
                    Mage::getSingleton('customer/session')->addError('Error with saving ticket!');
                }

            }
        }

        /*
         * If new ticket is created redirect to view ticket page
         *  -if not, render form again and show errors
         */
        if($newTicketId>0) {
            $this->_redirect('inchooticket/ticket/view/id/'.$newTicketId);
        }
        else {
            $this->_initLayoutMessages('customer/session');
            $this->renderLayout();
        }
    }

    /*
     * Reply on ticket
     * -its only for processing form, does not have view
     */
    public function replyAction()
    {
        //User must be logged in
        //$this->onlyLoggedInUser();
        $redirectWhere = 'inchooticket/ticket/';

        /*
         * Get post request
         */
        $post = $this->getRequest()->getPost();

        if(!$post) {
            $this->_forward('noRoute');
        }

        $errors = false;
        $ticketId = 0;

        /*
         * ticked-id must be declared
         */
        if (!Zend_Validate::is(trim($post['ticket-id']) , 'NotEmpty')) {
            $this->_forward('noRoute');
        } else {
            $ticketId = $post['ticket-id'];
        }


        if (!Zend_Validate::is(trim($post['message']), 'NotEmpty')) {
            Mage::getSingleton('customer/session')->addError('Please enter message!');
            $errors = true;
            if($ticketId>0) $redirectWhere = 'inchooticket/ticket/view/id/'.$ticketId.'/';
        } else {
            //Check length
            if(!Mage::helper('inchoo_ticket')->isValidLength($post['message'],1,5000)) {
                Mage::getSingleton('customer/session')->addError('Message is not in required range!(Minimum 1 character and maximum 5000 characters)');
                $errors = true;
                if($ticketId>0) $redirectWhere = 'inchooticket/ticket/view/id/'.$ticketId.'/';
            }
        }

        if(!$errors) {
            /*
             * Load ticket
             */
            $ticket = Mage::getModel('inchoo_ticket/ticket')
                ->load((int)$post['ticket-id']);

            if($ticket->getId()) {

                $currentUser = Mage::getSingleton('customer/session')->getCustomerId();
                $currentWebsite = Mage::app()->getStore()->getWebsiteId();

                /*
                 * Check if user is owner for this ticket and its same
                 * website like current
                 */
                if($currentUser === $ticket->getCustomerID() &&
                    $currentWebsite === $ticket->getWebsiteId()) {

                    //Check if ticket is closed
                    if($ticket->getStatus() === 'closed') {
                        Mage::getSingleton('customer/session')->addError('Ticket is closed! To reply please open ticket.');
                    }
                    else {
                        $redirectWhere = 'inchooticket/ticket/view/id/' .$ticket->getId(). '/';

                        //Add new reply
                        $newReply = Mage::getModel('inchoo_ticket/reply');
                        $newReply->setTicketId($ticket->getTicketId());
                        $newReply->setCustomerId($currentUser);
                        $newReply->setAdminId(0);
                        $newReply->setStatusChange(0);
                        $newReply->setMessage($post['message']);
                        $newReply->save();

                        if ($newReply->getReplyId() > 0) {
                            Mage::getSingleton('customer/session')->addSuccess('You have replied on ticket');
                        } else {
                            Mage::getSingleton('customer/session')->addError('Reply is not saved! Unknown error :/');
                        }
                    }
                } else {
                    $this->_forward('noRoute');
                }
            } else {
                $this->_forward('noRoute');
            }
        }

        $this->_redirect($redirectWhere);
    }

    /*
     * Close ticket action
     */
    public function closeAction()
    {
        //User must be logged in
        //$this->onlyLoggedInUser();

        $ticketId = $this->getRequest()->getParam('id');
        if(!$ticketId) {
            $this->_forward('noRoute');
        }

        //Load ticket
        $ticket = Mage::getModel('inchoo_ticket/ticket')
            ->load($ticketId);

        if(!$ticket->getId()) {
            $this->_forward('noRoute');
        }

        $currentUser = Mage::getSingleton('customer/session')->getCustomerId();
        $currentWebsite = Mage::app()->getStore()->getWebsiteId();

        if($currentUser === $ticket->getCustomerID() &&
            $currentWebsite === $ticket->getWebsiteId()) {

            //Add new reply
            $newReply = Mage::getModel('inchoo_ticket/reply');
            $newReply->setTicketId($ticket->getTicketId());
            $newReply->setCustomerId($currentUser);
            $newReply->setAdminId(0);
            $newReply->setStatusChange(2);
            $newReply->setMessage('-');
            $newReply->save();

            if($newReply->getReplyId()>0) {
                /*
                 * Set closed status and save ticket
                 */
                $ticket->setStatus('closed');
                $ticket->save();
                Mage::getSingleton('customer/session')->addSuccess('You have closed ticket');
            } else {
                Mage::getSingleton('customer/session')->addError('Ticket is not closed! Unknown error :/');
            }

            $this->_redirect('inchooticket/ticket/view/id/'.$ticket->getTicketId().'/');
        } else {
            $this->_forward('noRoute');
        }
    }

    /*
     * Open ticket action
     */
    public function openAction()
    {
        //User must be logged in
        //$this->onlyLoggedInUser();

        $ticketId = $this->getRequest()->getParam('id');
        if(!$ticketId) {
            $this->_forward('noRoute');
        }

        //Load ticket
        $ticket = Mage::getModel('inchoo_ticket/ticket')
            ->load($ticketId);

        if(!$ticket->getId()) {
            $this->_forward('noRoute');
        }

        $currentUser = Mage::getSingleton('customer/session')->getCustomerId();
        $currentWebsite = Mage::app()->getStore()->getWebsiteId();

        /*
         * Check if ticket customer id = current user id and check if
         * current website is equal to ticket website
         */
        if($currentUser === $ticket->getCustomerID() &&
            $currentWebsite === $ticket->getWebsiteId()) {

            //Add new reply
            $newReply = Mage::getModel('inchoo_ticket/reply');
            $newReply->setTicketId($ticket->getTicketId());
            $newReply->setCustomerId($currentUser);
            $newReply->setAdminId(0);
            $newReply->setStatusChange(1);
            $newReply->setMessage('-');
            $newReply->save();

            if($newReply->getReplyId()>0) {
                /*
                 * Set status to open and save
                 */
                $ticket->setStatus('open');
                $ticket->save();
                Mage::getSingleton('customer/session')->addSuccess('You have opened ticket');
            } else {
                Mage::getSingleton('customer/session')->addError('Ticket is not opened! Unknown error :/');
            }

            $this->_redirect('inchooticket/ticket/view/id/'.$ticket->getTicketId().'/');
        } else {
            $this->_forward('noRoute');
        }
    }

    /*
     * View ticket action
     */
    public function viewAction()
    {
        //User must be logged in
        //$this->onlyLoggedInUser();

        /*
         * Check if id is in get param
         */
        if(!$this->getRequest()->getParam('id')) {
            $this->_forward('noRoute');
        }

        $ticketData = Mage::getModel('inchoo_ticket/ticket')
            ->load($this->getRequest()->getParam('id'));

        /*
         * If id is not set redirect to 404
         */
        if(!$ticketData->getId()) {
            $this->_forward('noRoute');
        }

        /*
         * Check if current website is not equal ticket website,
         * if true than redirect on 404
         */
        $currentWebsite = Mage::app()->getStore()->getWebsiteId();
        if($ticketData->getWebsiteId() !== $currentWebsite) {
            $this->_forward('noRoute');
        }
        /*
         * If current customer is equal to customer id in ticket data
         */
        if($ticketData->getCustomerId() ===  Mage::getSingleton('customer/session')->getCustomerId()) {
            $this->_ticket = $ticketData;
            Mage::register('ticket', $this->_ticket);
            $this->loadLayout();

            $this->_initLayoutMessages('customer/session');
            $this->renderLayout();
        } else {
            $this->_forward('noRoute');
        }
    }

    /*
     * My tickets list
     *  -full list with pagination
     */
    public function listAction()
    {
        //User must be logged in
        //$this->onlyLoggedInUser();

        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');

        $this->renderLayout();
    }
}