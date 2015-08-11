<?php
 
class Frankwebdev_Bundlereport_Adminhtml_BundlereportController extends Mage_Adminhtml_Controller_Action {
 
    protected function _initAction() {
         Mage::log('INIT ACTION');
        $this->loadLayout();
        Mage::log('/INIT ACTION');
        //Mage::log($this);


        return $this;
    }

    public function _initReportAction($blocks){
        
        Mage::log('INIT REPORT ACTION FROM OTHER SYSTEM');

        if (!is_array($blocks)) {
            $blocks = array($blocks);
        }

        $requestData = Mage::helper('adminhtml')->prepareFilterString($this->getRequest()->getParam('filter'));
        $requestData = $this->_filterDates($requestData, array('from', 'to'));
       // $requestData['groupid'] = Mage::getSingleton('core/session')->getBundleskufilter();
        $requestData['store_ids'] = $this->getRequest()->getParam('store_ids');



        $params = new Varien_Object();

        foreach ($requestData as $key => $value) {
            if (!empty($value)) {
                $params->setData($key, $value);
            }
        }

        foreach ($blocks as $block) {
            if ($block) {
                $block->setPeriodType($params->getData('period_type'));
                $block->setFilterData($params);
            }
        }

        return $this;
    }


    public function indexAction() {
         Mage::log('INDEX ACTION');

        $gridBlock = $this->getLayout()->getBlock('adminhtml_bundlereport.grid');
        $filterFormBlock = $this->getLayout()->getBlock('grid.bundlefilter.form');
        
        $this->_initReportAction(array(
            $gridBlock,
            $filterFormBlock
        ));

        $this->_initAction()
             ->renderLayout();
    }
 
    public function exportCsvAction() {
         Mage::log('EXPORT CSV ACTION');
        $fileName = 'bundlereport.csv';
        /*$content = $this->getLayout()->createBlock('bundlereport/adminhtml_bundlereport_grid')
                        ->getCsv();*/
        $this->_sendUploadResponse($fileName, $content);
    }
 
    public function exportXmlAction() {
         Mage::log('EXPORT XML ACTION');
        $fileName = 'bundlereport.xml';
        /*$content = $this->getLayout()->createBlock('bundlereport/adminhtml_bundlereport_grid')
                        ->getXml();*/
        $this->_sendUploadResponse($fileName, $content);
    }
 
    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream') {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK', '');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename=' . $fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
 
}