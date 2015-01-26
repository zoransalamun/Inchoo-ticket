<?php
class Inchoo_Ticket_adminhtml_TicketController
    extends Mage_Adminhtml_Controller_Action
{
    /*
     * Method for load layout and set active menu item in main menu
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('ticket/inchoo_ticket')
        ;
        return $this;
    }

    /*
     * Main page in admin panel
     * -it will display ticket grid
     */
    public function indexAction()
    {
        $this->_initAction();
        $this->_addContent($this->getLayout()->createBlock('inchoo_ticket/adminhtml_ticket_list'));
        $this->renderLayout();
    }

    /*
     * Ajax grid action
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('inchoo_ticket/adminhtml_ticket_list_grid')->toHtml()
        );
    }

    /*
     * Admin reply on ticket at admin dashboard
     */
    public function replyAction()
    {
        //Load ticket
        $ticket = Mage::getModel('inchoo_ticket/ticket')
            ->load($this->getRequest()->getParam('id'));;

        /*
         * Check If ticket exist, if not redirect back to ticket index and show error
         */
        if(!$ticket->getId()) {
            $this->_getSession()->addError('That ticket does not exist!');
            return $this->_redirect('*/*/');
        }

        /*
         * Save ticket for later use
         * -it is needed to display ticket data in form and for loading reply data
         */
        Mage::register('ticket',$ticket);

        /*
         * load layout and create container block
         */
        $this->_initAction();
        $this->_title('Reply on ticket #'.$ticket->getId());

        /*
         * If there is error in form, return data
         */
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if(!empty($data)) {
            $ticket->addData($data);
        }

        $this->renderLayout();
    }

    /*
     * Open ticket
     */
    public function openAction()
    {
        $ticket = Mage::getModel('inchoo_ticket/ticket')
            ->load($this->getRequest()->getParam('id'));;

        /*
         * Check If ticket exist, if not redirect back to ticket index and show error
         */
        if(!$ticket->getId()) {
            $this->_getSession()->addError('That ticket does not exist!');
            return $this->_redirect('*/*/');
        }

        /*
         * Set ticket status to open and save
         */
        $ticket->setStatus('open');
        $ticket->save();

        /*
         * Create new reply entry, it will not be reply only will
         * set status change so it can be displayed at reply list
         */
        $currentUser = Mage::getSingleton('admin/session');
        $newReply = Mage::getModel('inchoo_ticket/reply');
        $newReply->setTicketId($ticket->getId());
        $newReply->setCustomerId(0);
        $newReply->setAdminId($currentUser->getUser()->getUserId());
        $newReply->setStatusChange(1);
        $newReply->setMessage('-');
        $newReply->save();

        $this->_getSession()->addSuccess('You have opened this ticket.');
        return $this->_redirect('*/*/reply/id/'.$ticket->getId().'/');
    }

    /*
     * Close ticket
     */
    public function closeAction()
    {
        //Load ticket by id
        $ticket = Mage::getModel('inchoo_ticket/ticket')
            ->load($this->getRequest()->getParam('id'));

        /*
         * Check If ticket exist, if not redirect back to ticket index and show error
         */
        if(!$ticket->getId()) {
            $this->_getSession()->addError('That ticket does not exist!');
            return $this->_redirect('*/*/');
        }

        /*
         * Set ticket status to closed and save
         */
        $ticket->setStatus('closed');
        $ticket->save();

        /*
         * Create new reply entry, it will not be reply only will
         * set status change so it can be displayed at reply list
         */
        $currentUser = Mage::getSingleton('admin/session');
        $newReply = Mage::getModel('inchoo_ticket/reply');
        $newReply->setTicketId($ticket->getId());
        $newReply->setCustomerId(0);
        $newReply->setAdminId($currentUser->getUser()->getUserId());
        $newReply->setStatusChange(2);
        $newReply->setMessage('-');
        $newReply->save();

        $this->_getSession()->addSuccess('You have closed this ticket.');

        $this->_redirect('*/*/reply/id/'.$ticket->getId().'/');
    }
    /*
     * Delete ticket
     */
    public function deleteAction()
    {
        //Load ticket by id
        $ticket = Mage::getModel('inchoo_ticket/ticket')
            ->load($this->getRequest()->getParam('id'));

        /*
         * Check If ticket exist, if not redirect back to ticket index and show error
         */
        if(!$ticket->getId()) {
            $this->_getSession()->addError('That ticket does not exist!');
            return $this->_redirect('*/*/');
        }

        $this->_getSession()->addSuccess('You have deleted ticket #'.$ticket->getId().'.');

        /*
         * Delete ticket
         */
        $ticket->delete();

        $this->_redirect('*/*/');
    }

    /*
     * Reply process form
     */
    public function replyprocessAction()
    {
        /*
         * Check if request is post, if not redirect to admin ticket index page
         */
        $post = $this->getRequest()->getPost();
        if(!$post) {
            $this->_redirect('*/*/');
        }

        $errors = false;
        $ticketId = 0;

        /*
         * Validation for input
         */
        if (!Zend_Validate::is(trim($post['ticket-id']) , 'NotEmpty')) {
            $this->_redirect('*/*/');
        } else {
            $ticketId = $post['ticket-id'];
        }

        if (!Zend_Validate::is(trim($post['message']), 'NotEmpty')) {
            $this->_getSession()->addError('Please enter message!');
            $errors = true;
        } else {
            //Check length
            if(!Mage::helper('inchoo_ticket')->isValidLength($post['message'],1,5000)) {
                $this->_getSession()->addError('Message is not in required range!(Minimum 1 character and maximum 5000 characters)');
                $errors = true;
            }
        }

        if($errors) {
            if($ticketId>0) $this->_redirect('*/*/reply/id/'.$ticketId.'/');
            else $this->_redirect('*/*/');
        }
        else {

            $ticket = Mage::getModel('inchoo_ticket/ticket')
                ->load((int)$post['ticket-id']);

            if ($ticket->getId()) {

                $currentUser = Mage::getSingleton('admin/session')->getUser()->getUserId();

                //Check if ticket is closed
                if ($ticket->getStatus() === 'closed') {
                    $this->_getSession()->addError('Ticket is closed! To reply please open ticket.');
                    $this->_redirect('*/*/');
                }

                /*
                 * Add new reply entry
                 */
                $newReply = Mage::getModel('inchoo_ticket/reply');
                $newReply->setTicketId($ticket->getTicketId());
                $newReply->setCustomerId(0);
                $newReply->setAdminId($currentUser);
                $newReply->setStatusChange(0);
                $newReply->setMessage($post['message']);
                $newReply->save();

                if ($newReply->getReplyId() > 0) {
                    /*
                     * Increment replies in ticket table and save
                     */
                    $ticket->setReplies($ticket->getReplies() + 1);
                    $ticket->save();
                    $this->_getSession()->addSuccess('You have replied on ticket');
                } else {
                    $this->_getSession()->addError('Reply is not saved! Unknown error :/');
                }

                /*
                 * Redirect back to reply form
                 */
                $this->_redirect('*/*/reply/id/' . $ticket->getId() . '/');

            } else {
                $this->_getSession()->addError('Ticket does not exist!');
                $this->_redirect('*/*/');
            }
        }
    }

    /*
     * Mass delete tickets
     */
    public function massDeleteAction()
    {
        /*
         * Get ticket id's from url
         */
        $tacketIds = $this->getRequest()->getParam('ticket_id');
        if(!is_array($tacketIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('inchoo_ticket')->__('Please select tickets!'));
        } else {
            try {
                $ticketModel = Mage::getModel('inchoo_ticket/ticket');
                foreach ($tacketIds as $ticketId) {
                    $ticketModel->load($ticketId)->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('inchoo_ticket')->__(
                        'Total of %d tickets(s) were deleted.', count($tacketIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    /*
      * Mass close tickets
      */
    public function massCloseAction()
    {
        $tacketIds = $this->getRequest()->getParam('ticket_id');
        if(!is_array($tacketIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('inchoo_ticket')->__('Please select tickets!'));
        } else {
            try {
                $ticketModel = Mage::getModel('inchoo_ticket/ticket');
                $closed = 0;
                $closedBefore = 0;
                foreach ($tacketIds as $ticketId) {
                    $ticketModel->load($ticketId);
                    if($ticketModel->getStatus() === 'open') {
                        $ticketModel->setStatus('closed');
                        $ticketModel->save();
                        $closed++;
                    } else {
                        $closedBefore++;
                    }
                }
                if($closedBefore>0) {
                    Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('inchoo_ticket')->__(
                            'Total of %d tickets(s) were closed. %d tickets were alraedy closed.', $closed, $closedBefore
                        )
                    );
                } else {
                    Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('inchoo_ticket')->__(
                            'Total of %d tickets(s) were closed.', $closed
                        )
                    );
                }
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    /*
      * Mass open tickets
      */
    public function massOpenAction()
    {
        $tacketIds = $this->getRequest()->getParam('ticket_id');
        if(!is_array($tacketIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('inchoo_ticket')->__('Please select tickets!'));
        } else {
            try {
                $ticketModel = Mage::getModel('inchoo_ticket/ticket');
                $opened = 0;
                $openedBefore = 0;
                foreach ($tacketIds as $ticketId) {
                    $ticketModel->load($ticketId);
                    if($ticketModel->getStatus() === 'closed') {
                        $ticketModel->setStatus('open');
                        $ticketModel->save();
                        $opened++;
                    } else {
                        $openedBefore++;
                    }
                }
                if($openedBefore>0) {
                    Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('inchoo_ticket')->__(
                            'Total of %d tickets(s) were opened. %d tickets were alraedy open.', $opened, $openedBefore
                        )
                    );
                } else {
                    Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('inchoo_ticket')->__(
                            'Total of %d tickets(s) were opened.', $opened
                        )
                    );
                }
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

}