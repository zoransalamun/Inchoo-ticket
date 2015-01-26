<?php
/*
 * Grid for ticket list
 */
class Inchoo_Ticket_Block_Adminhtml_Ticket_List_Grid
    extends Mage_Adminhtml_Block_Widget_Grid
{
    /*
     * Set params for grid
     * -use ajax for load and save grid params to session
     * -sort by ticket_id DESC
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('inchoo_ticket_grid');
        $this->setDefaultSort('ticket_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /*
     * Prepare collection data for grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('inchoo_ticket/ticket')
            ->getResourceCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /*
     * Setup for columns
     */
    protected function _prepareColumns()
    {
        $helper = Mage::helper('inchoo_ticket');

        $this->addColumn('ticket_id', array(
            'header' => $helper->__('Ticket id #'),
            'index'  => 'ticket_id',
            'sortable'  => true
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('websites',
                array(
                    'header'=> $helper->__('Website'),
                    'width' => '100px',
                    'index'     => 'website_id',
                    'type'      => 'options',
                    'options'   => Mage::getModel('core/website')->getCollection()->toOptionHash(),
                ));
        }

        $this->addColumn('created_at', array(
            'header' => $helper->__('Created date'),
            'index'  => 'created_at',
            'type' => 'datetime',
        ));

        $this->addColumn('subject', array(
            'header' => $helper->__('Subject'),
            'index'  => 'subject'
        ));

        $this->addColumn('status', array(
            'header' => $helper->__('Status'),
            'index'  => 'status',
            'type'      => 'options',
            'options'   => array('closed' => Mage::helper('adminhtml')->__('Closed'), 'open' => Mage::helper('adminhtml')->__('Open')),
        ));

        $this->addColumn('replies', array(
            'header' => $helper->__('Replies'),
            'index'  => 'replies',
            'type'   => 'number'
        ));

        /*
         * Actions in grid
         * -delete and reply are allowed action
         */
        $this->addColumn('action', array(
            'header' => $helper->__('Actions'),
            'type' => 'action',
            'getter'  => 'getid',
            'actions' => array(
                array(
                    'caption' => 'Reply',
                    'url' => array('base' => '*/*/reply'),
                    'field' => 'id'
                ),
                array(
                    'caption' => 'Delete',
                    'url' => array('base' => '*/*/delete'),
                    'field' => 'id'
                ),
            ),
            'filter' => false,
            'sortable' => false,
            'index' => 'tickets'
        ));

        return parent::_prepareColumns();
    }

    /*
     * Set grid url for ajax load
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

    /*
     * Ger row url
     *  -on row click admin will go to reply form
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/reply', array('id'=>$row->getId()));
    }

    /*
     * Massaction prepare
     * -input checkbox will have ticket_id value
     * -actions: delete, open, close
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('ticket_id');
        $this->getMassactionBlock()->setFormFieldName('ticket_id');
        $this->getMassactionBlock()->addItem('delete', array(
            'label'=> Mage::helper('inchoo_ticket')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete', array('' => '')),
            'confirm' => Mage::helper('inchoo_ticket')->__('Are you sure want to delete selected tickets?')
        ));
        $this->getMassactionBlock()->addItem('open', array(
            'label'=> Mage::helper('inchoo_ticket')->__('Open'),
            'url' => $this->getUrl('*/*/massOpen', array('' => '')),
            'confirm' => Mage::helper('inchoo_ticket')->__('Are you sure want to open selected tickets?')
        ));
        $this->getMassactionBlock()->addItem('close', array(
            'label'=> Mage::helper('inchoo_ticket')->__('Close'),
            'url' => $this->getUrl('*/*/massClose', array('' => '')),
            'confirm' => Mage::helper('inchoo_ticket')->__('Are you sure want to close selected tickets?')
        ));
        return $this;
    }
}