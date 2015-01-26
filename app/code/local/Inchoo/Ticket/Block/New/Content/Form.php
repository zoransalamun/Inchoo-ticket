<?php
/**
 * Form block for new ticket
 */
class Inchoo_Ticket_Block_New_Content_Form
    extends Mage_Core_Block_Template
{
    /*
     * Return input value if validation failed
     */
    public function getInputValue($key = '')
    {
        $post = $this->getRequest()->getPost();
        if($post) {
            return $this->getRequest()->getParam($key);
        } else {
            if($key === 'url_slike') {
                return Mage::getStoreConfig('inchoo/opcenito/url_slike');
            }
        }
    }
}