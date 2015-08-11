<?php
class Frankwebdev_Bundlereport_Block_Adminhtml_Bundlereport extends Mage_Adminhtml_Block_Widget_Grid_Container {
 
    public function __construct() {
    	Mage::log('Frankwebdev_Bundlereport_Block_Adminhtml_Bundlereport');
        $this->_controller = 'adminhtml_bundlereport';
        $this->_blockGroup = 'bundlereport';
        $this->_headerText = Mage::helper('bundlereport')->__('Bundle Report');
        //parent::__construct();
       // $this->setTemplate('bundlereport/grid.phtml');
        $this->_removeButton('add');
        
    }
 
}