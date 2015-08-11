<?php
class Frankwebdev_Bundlereport_Block_Adminhtml_Bundlereport_Grid extends Mage_Adminhtml_Block_Report_Grid {
 
    public function __construct() {
          Mage::log('GRID CONSTRUCT');
       // parent::__construct();
        $this->setId('bundlereportgrid');
        $this->setDefaultSort('Type');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(false);
        $this->setSubReportSize(false);
    }
 
   protected function _prepareCollection() {
        Mage::log('PREPARE COLLECTION');
        parent::_prepareCollection();
       
        $this->getCollection()->initReport('bundlereport/bundlereport');
         
        return $this;
    }
 
    protected function _prepareColumns() {
        $this->addColumn('pid', array(
            'header'    =>Mage::helper('reports')->__('ID'),
            'align'     =>'left',
            'index'     =>'pid',
            'total'     =>'',
            'type'      =>'text'
        ));

        $this->addColumn('Site', array(
            'header'    =>Mage::helper('reports')->__('Site'),
            'align'     =>'left',
            'index'     =>'Site',
            'total'     =>'',
            'sortable'  => true,
            'type'      =>'text'
        ));
        $this->addColumn('sku', array(
            'header'    =>Mage::helper('reports')->__('Sku'),
            'align'     =>'left',
            'index'     =>'sku',
            'total'     =>'',
            'sortable'  => true,
            'type'      =>'text' 
        ));
        

       /* $this->addColumn('parentsku', array(
            'header'    =>Mage::helper('reports')->__('Parent ID'),
            'align'     =>'left',
            'index'     =>'parentsku',
            'total'     =>'',
            'type'      =>'text'
        ));*/
        


        $this->addColumn('ProductName', array(
            'header'    =>Mage::helper('reports')->__('Product Name'),
            'align'     =>'left',
            'index'     =>'ProductName',
            'total'     =>'',
            'type'      =>'text'
        ));

        

        
       /* $this->addColumn('childsku', array(
            'header'    =>Mage::helper('reports')->__('Child Sku'),
            'align'     =>'left',
            'index'     =>'childsku',
            'total'     =>'',
            'type'      =>'text'
        ));*/
        $this->addColumn('Type', array(
            'header'    =>Mage::helper('reports')->__('Type'),
            'align'     =>'left',
            'index'     =>'Type',
            'total'     =>'',
            'type'      =>'text'
        ));
        $this->addColumn('orderitemqty', array(
            'header'    =>Mage::helper('reports')->__('Ordered Qty.'),
            'align'     =>'left',
            'index'     =>'orderitemqty',
            'total'     =>'',
            'type'      =>'number'
        ));
        /*$this->addColumn('Total', array(
            'header'    =>Mage::helper('reports')->__('Value'),
            'align'     =>'left',
            'index'     =>'Total',
            'total'     =>'sum',
            'type'      =>'currency',
            'currency_code' => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE)
        ));*/
       /* $this->addColumn('view', array(
            'header'    =>Mage::helper('reports')->__('Detail'),
            'align'     =>'left',
            'index'     =>'view',
            'type'      =>'action',
            'getter'     => 'getId',
            'actions'   => array(
                    array(
                        'caption' => Mage::helper('catalog')->__('View Bundle Items'),
                        'url'     => array(
                            'base'=>'bundlereport/adminhtml_bundlview',
                            'params'=>array('store'=>'bundlereport/adminhtml_bundlitemereport/view')
                        ),
                        'field'   => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',

        ));
*/
        /*
        $this->addColumn('Orders', array(
            'header'    =>Mage::helper('reports')->__('Orders'),
            'align'     =>'right',
            'index'     =>'Orders',
            'total'     =>'sum',
            'type'      =>'number'
        ));
         
        $this->addColumn('Items', array(
            'header'    =>Mage::helper('reports')->__('Items Ordered'),
            'align'     =>'right',
            'index'     =>'Items',
            'total'     =>'sum',
            'type'      =>'number'
        ));
        $this->addColumn('Tax', array(
            'header'    =>Mage::helper('reports')->__('Tax'),
            'align'     =>'right',
            'index'     =>'Tax',
            'total'     =>'sum',
            'type'      => 'currency',
            'currency_code' => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE)
        ));
        
        $this->addColumn('Discount', array(
            'header'    =>Mage::helper('reports')->__('Discount'),
            'align'     =>'right',
            'index'     =>'Discount',
            'total'     =>'sum',
            'type'      => 'currency',
            'currency_code' => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE)
        ));
        
        $this->addColumn('Invoiced', array(
            'header'    =>Mage::helper('reports')->__('Invoiced'),
            'align'     =>'right',
            'index'     =>'Invoiced',
            'total'     =>'sum',
            'type'      => 'currency',
            'currency_code' => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE)
        ));*/
        
        
        $this->addExportType('*/*/exportCsv', Mage::helper('bundlereport')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('bundlereport')->__('XML'));
        return parent::_prepareColumns();
    }
 
    public function getRowUrl($row) {
        return false;
    }
 
    public function getReport($from, $to) {
        if ($from == '') {
            $from = $this->getFilter('report_from');
        }
        if ($to == '') {
            $to = $this->getFilter('report_to');
        }


        $cfilter = $this->getFilter('filtersku');
        Mage::log('CFILTER');
        Mage::log($cfilter);

        Mage::getSingleton('core/session')->setBundleskufilter(urldecode($cfilter));
        /*$totalObj = Mage::getModel('reports/totals');
        $totals = $totalObj->countTotals($this, $from, $to);
        $this->setTotals($totals);
        $this->addGrandTotals($totals);*/
        return $this->getCollection()->getReport($from, $to);
    }
    
   
}