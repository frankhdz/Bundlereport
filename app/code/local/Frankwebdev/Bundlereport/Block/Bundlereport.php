<?php
class Frankwebdev_Bundlereport_Block_Bundlereport extends Mage_Core_Block_Template {
 
    public function _prepareLayout() {
        Mage::log('BLOCK _prepareLayout');
        return parent::_prepareLayout();
    }
 
    public function getBundlereport() {
    	Mage::log('BLOCK getBundlereport');
        if (!$this->hasData('bundlereport')) {
            $this->setData('bundlereport', Mage::registry('bundlereport'));
        }
        return $this->getData('bundlereport');
    }
 
}
