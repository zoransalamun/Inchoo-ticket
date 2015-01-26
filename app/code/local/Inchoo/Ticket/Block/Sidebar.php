<?php
/*
 * Sidebar for all frontend ticket pages
 */
class Inchoo_Ticket_Block_Sidebar extends Mage_Core_Block_Template
{
    protected $_menuItems = array();

    /*
     * Action for add menu item
     *  -helps user to add menu item in layout file without changeing block html
     */
    public function addMenuItem($url='', $name='')
    {
        /*
         * Only add if url and name is entered
         */
        if(strlen($url)>0 && strlen($name)>0) {
            $this->_menuItems[] = array(
                'url' => $url,
                'name' => $name
            );
        }
    }

    /*
     * Get controller from url
     *  -url is set in layout config inside sidebar block
     */
    protected function getControllerFromParams($url='')
    {
        $urlParts = explode('/',$url);
        if(isset($urlParts[1])) {
            if(strlen($urlParts[1])>0) {
                return $urlParts[1];
            }
        }
        return 'index';
    }

    /*
     * Get action from url
     *  -url is set in layout config inside sidebar block
     */
    protected function getActionFromParams($url='')
    {
        $urlParts = explode('/',$url);
        if(isset($urlParts[2])) {
            if(strlen($urlParts[2])>0) {
                return $urlParts[2];
            }
        }
        return 'index';
    }

    /*
     * Get menu items
     *  -for block html
     */
    public function getMenuItems()
    {
        return $this->_menuItems;
    }

    /*
     * Check if menu item is active
     *  -this check if current controller and action are
     *   equal to menu item controller and action
     *  -module check is not needed becasue this sidebar
     *   only display at this module frontend
     */
    public function isMenuItemActive($url='')
    {
        $item = array(
            'controller' => $this->getControllerFromParams($url),
            'action' => $this->getActionFromParams($url)
        );

        $currentController = $this->getRequest()->getControllerName();
        $currentAction = $this->getAction()->getRequest()->getActionName();
        if($currentController === $item['controller']) {
            if($currentAction === $item['action']) {
                return true;
            }
        }
        return false;
    }
}