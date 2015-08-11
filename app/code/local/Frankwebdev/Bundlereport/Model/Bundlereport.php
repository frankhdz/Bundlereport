<?php
class Frankwebdev_Bundlereport_Model_Bundlereport extends Mage_Reports_Model_Mysql4_Order_Collection
{
    protected $_from = '';
    protected $_to = '';
    
    function __construct() {
        //Mage::log('CONSTRUCT');
        parent::__construct();
        $this->setResourceModel('sales/order');
        $this->_init('sales/order','entity_id');
    }

    public function setDateRange($from, $to){
       //Mage::log('SET DATE RANGE');
       $this->_from = $from;
       $this->_to = $to;
       $this->_reset();
       return $this;
   }

    public function getOrderBundleRelatedItems($parentID){

        //get the parent id
        //Mage::log($parentID);
        $conn = Mage::getSingleton('core/resource')->getConnection('core_read');
        $sql = "SELECT item_id,parent_item_id FROM sales_flat_order_item WHERE item_id='".$parentID."'  ";

        foreach($conn->fetchAll($sql) as $row){
            //retrieve 


        }

        //return join condition string
        $related = "";

        return $related;
    }

    public function getBundleArray($sku){
        //Mage::log('GET BUNDLE SKU : '.$sku );
        $sku  = str_replace(" ", "", $sku);
        $conn = Mage::getSingleton('core/resource')->getConnection('core_read');
        $sql = "SELECT `item_id`,`product_id`,`sku`, `created_at`,`product_type`  
        FROM sales_flat_order_item 
        WHERE (sku = '".$sku."') AND (product_type='bundle') GROUP BY sku ";

        //Mage::log($sql);

        $i = 0;
        foreach($conn->fetchAll($sql) as $row){
                //retrieve id 
            //Mage::log("CO : ".$i++);
            //Mage::log($row);
           // //Mage::log();
           /* $product['sku'] = $row['sku'];
            $product['id']= $row['id'];
            $product['children']= $this->getSingleBundleChildren($row['id']);*/

            $children = $this->getSingleBundleChildren($row['product_id']);

            //Mage::log("CHILDREN LIST START");
            //Mage::log($children);
           
            return $children;
        }

    }

    public function getSingleBundleChildren($id){

        //

        //Mage::log('GET SINGLE BUNDLE CHILDREN');
        $conn = Mage::getSingleton('core/resource')->getConnection('core_read');
        $sql = "SELECT `product_id` FROM catalog_product_bundle_selection WHERE  parent_product_id='".$id."'" ;

        $conn = Mage::getSingleton('core/resource')->getConnection('core_read');

                //$bundle[$row['product_id']][0] = array($row['product_id']);

        foreach($conn->fetchAll($sql) as $row){

                   // Mage::log($row['product_id']);
            $children[$row['product_id']] = $row['product_id']; 

        }

        return $children;
    }


    public function getBundleChildren($storeID, $dateFrom, $dateTo){

        Mage::log('getBundleChildren');



        if(!isset($storeID) || $storeID==null){
            $whereStoreID = '';
            //Mage::log('all stores');
        }else{

            $whereStoreID = 'AND (store_id = '.$storeID.')';
            //Mage::log('from store : '.$storeID);
        }

            //$product = Mage::getModel('bundle/product_type')->getParentIdsByChild($childId);

        $conn = Mage::getSingleton('core/resource')->getConnection('core_read');
        $sql = "SELECT `store_id`, `product_id`, `sku`, `created_at`,`product_type`  
        FROM sales_flat_order_item 
        WHERE (created_at BETWEEN '".$dateFrom."'
            AND '".$dateTo."')  
            AND (product_type = 'bundle')
            ".$whereStoreID."
            GROUP BY sku ";



        //Mage::log('get ROW');
        //Mage::log($sql);

               //******************************** Write query to get children products from catalog_product_bundle_selection; group by parent


        $bundle = array();
            foreach($conn->fetchAll($sql) as $row){
             //Mage::log("PRODUCT ID : ".$row['product_id']);
                        //query retrieve bundle children

             $c_sql = "SELECT `parent_product_id`,`product_id` FROM catalog_product_bundle_selection WHERE  parent_product_id='".$row['product_id']."'" ;

             $c_conn = Mage::getSingleton('core/resource')->getConnection('core_read');

             $bundle[$row['product_id']][0] = array($row['product_id']);

                 foreach($c_conn->fetchAll($c_sql) as $c_row){

                              //  //Mage::log($c_row['product_id']);
                    $bundle[$row['product_id']][1][$c_row['product_id']] = $c_row['product_id']; 

                }
            }
        //Mage::log($bundle);
        return $bundle;
    }

    public function ordersWithBundle($bundle,$from,$to){

        $order_list = "";
        $conn = Mage::getSingleton('core/resource')->getConnection('core_read');
        $sql = "SELECT * FROM sales_flat_order_item WHERE product_id='$bundle' AND ((created_at>='$from') AND (created_at <='$to')) ";

        Mage::log($sql);
        foreach($conn->fetchAll($sql) as $row){
            Mage::log($from." ".$to." | ".$bundle);
            Mage::log("ORDER WITH PARENT : ".$row['order_id']);
            $order_list .= $row['order_id']."," ;
        }
        $order_list = rtrim($order_list,",");
        return $order_list;
    }

    private function getBundleId($sku){
        Mage::log("CUSTOM SEARCH : ".$sku);
       $_product = Mage::getModel('catalog/product')->loadByAttribute('sku',$sku);

      // Mage::log($_product->getId());
       return $_product->getId();

    }

    public function joinFields($from, $to, $storeIds = array()){
        //Mage::log('MODEL : JOIN FIELDS');
            //$this->populate_tmp($from, $to);
            #$this->_reset();
        //Mage::log("GET STORE ID"); 
            ////Mage::log(Mage::app()->getRequest()->getParams('selopt'));

        if (count($storeIds)==1){
            //Mage::log("count store ids");
            $store_id = $storeIds[0];
            $where_store = ' AND (st.store_id = '.$store_id.')';
                //$orderwhere = ' AND (orderitem.store_id = '.$store_id.') ';
        }else{
            //Mage::log("count store ids : 0");
            $where_store = '';

        }


        //Mage::log("GET SESSION FILTER FROM FORM");
        $bundlesku = Mage::getSingleton('core/session')->getBundleskufilter();
        //Mage::log($bundlesku);
        //search for sku
        if($bundlesku!=''){
            //add filter to query
            //get the children of a single sku
           // $bundlesearch = $this->getBundleArray($bundlesku);
            //get bundle id
            $bundleID = $this->getBundleId($bundlesku);
            Mage::log($bundleID);
             $orderwithbundle = $this->ordersWithBundle($bundleID,$from,$to);
             $childList = $this->ordersWithBundle($bundleID,$from,$to);
            //retrieve only order related items
            //$skufilter = ' AND () '
            //$skufilter = '';
            //Mage::log('CUSTOM SEARCH CONDITION');
            //Mage::log($bundlesearch);

            /*$childList = "";
            foreach($bundlesearch as $item => $itemval){
                //Mage::log('BUNDLE CHILDREN ::: '.$item." ".$itemval);
                //$childList.= $itemval.",";
                $childList .= "'".$child."',";

            }*/

            //$childList = "'11021','11008','11023','11024','11025','11026','11027'";


        }else{
            //Mage::log("STORE SELECTION ID : ".$store_id);
            if(!isset($store_id)){
                $store_id = null;
            }
            $childProductsArray = $this->getBundleChildren($store_id, $from, $to);

            $childList = '';
            $orderwithbundle = "";
            foreach($childProductsArray as $bundleArray=>$val ){
                        //$childList .= " '".$child."',";
                 //Mage::log('VAL START');
                 //Mage::log($val);
                 //Mage::log('VAL END');

                 //$childList .= "'".$val[0][0]."',";

                 /*this gets all the order with the parent bundle. Need a way to get the bundle items in the order. Maybe get an array with the
                    items then compare to the bundled items list?
                */
                $orderwithbundle .= $this->ordersWithBundle($val[0][0],$from,$to).",";//
          

                foreach($val[1] as $child){
                    //Mage::log("CHILD : ".$child);
                    $childList .= "'".$child."',";
                }

            }
        }
        $orderwithbundle = rtrim($orderwithbundle,",");
        
        $orderwithbundle_array = array_unique(explode(",",$orderwithbundle));
        //convert oorderwithbundle to array
        //Mage::log($orderwithbundle_array);

        //get list of items in the order
        $countOrders  = 0;
        Mage::log("BUILD LIST");
        foreach($orderwithbundle_array as $order_id){
           // Mage::log('SEARCH THIS ORDER : '.$order_id."    ".($countOrders++));
            $getorder = Mage::getModel('sales/order')->load($order_id);
            $getitems = $getorder->getAllItems();
            $orderlist_compare = array();
            //loops through items to get id
           // Mage::log("ORDER ITEMS");
            $count = 0;
            foreach ($getitems as $itemId => $item){
               //$name[] = $item->getName();
               //$unitPrice[]=$item->getPrice();
               //$sku[]=$item->getSku();
               //$orderids[$item->getProductId()]=$order_id;
               //$pidx = $item->getProductId();
               //Mage::log("       ".$pidx);
               
               //$orderids[$item->getProductId()]=$order_id;
               $orderids[$countOrders++]=array($order_id,$item->getProductId());
               //$orderidsv2[$countOrders]=array($order_id,$item->getProductId());
               //$orderids[1]=$item->getProductId();
                //Mage::log($item->getProductId());
                //Mage::log($item->getQtyToInvoice());
                //Mage::log(get_class_methods($item));
               //$qty[]=$item->getQtyToInvoice();
            }


        }

       // Mage::log($orderids);
        $childList = rtrim($childList,',');

        $childListArray = explode(",",str_replace("'", "", $childList));
        Mage::log("BUNDLE CHILD LIST");
        //Mage::log($childListArray);
        Mage::log($orderids);

        $searchList = array();
        $searchList_idonly = "";
        $orderid_only = "";
        $whereList = "";
        
        //check if order item exists in child
        foreach($orderids as $p_id ){
            $orderNum = $p_id[0];
            $productNum = $p_id[1];    


            Mage::log("ORDER ID : ".$orderNum);
            foreach($childListArray as $child){
               // Mage::log("CHECK : ".$child."    ".$p_id."     ORDER[".$o_id."]");
                if($productNum == $child){
                    
                   // Mage::log("NEEDLE : ".$child." : ".)
                    //Mage::log("TRUE");
                   
                    
                    $searchList[$orderNum] = $orderNum ; 
                    $searchList_idonly.= $orderNum."," ;
                    
                    $orderid_only = "'".$orderNum."'";
                    $item_idonly = "'".$productNum."'" ;
                    
                    $whereList .= " (orderitem.product_id = $item_idonly AND order_id = $orderid_only)  OR"; 
                    //$whereList .= " (orderitem.product_id = $item_idonly )  OR";

                }
            }


        }
        $whereList = rtrim($whereList,'OR');
        Mage::log("NEW SEARCH ARRAY");
       // Mage::log($searchList);

        $orderid_only_array = explode(",",$orderid_only);
        $orderid_only_array_unique = array_unique($orderid_only_array); 
        $orderid_only = implode(",",$orderid_only_array_unique);



        if($childList != "" || $childList != null ){
            Mage::log('RETURN');
            //Mage::log($childList);
            //make a loop to search for item and order id?
            //$joinChildren = ' AND (orderitem.product_id IN ('.rtrim($searchList_idonly,",").')) AND (orderitem.order_id IN ('.rtrim($orderid_only,",").')) ';
            //$joinChildren = ' AND (orderitem.product_id IN ('.rtrim($searchList_idonly,",").')) ';
            $joinChildren = ' AND ('.$whereList.') ';
            Mage::log($joinChildren);
        }else{
            //Mage::log('NO RETURN');
            //Mage::log($childList);

            $joinChildren = 'AND (orderitem.product_id IN (0))';

        }

        //$childList = '1121,4244';
        //Mage::log("CHILD LIST : ".$childList);

       
        //get a list of the orders that have the parent order
        //then 
        

        $this->getSelect()->reset()
        ->from(
            array('orderitem' => $this->getTable('sales/order_item')),
            array(
                        //Select Columns
                'Site'           => "st.name",
                'sku'            => "orderitem.sku",
                'orderitemqty'   => 'SUM(qty_ordered)',
                //'count'          => 'COUNT(*)',
                'ProductName'    => 'name',
                'Type'           => 'product_type',
                'pid'            => 'product_id',
                'orderid'        => 'order_id',
                //'orderitemqty' => 'qty_ordered',
               // 'parentsku' => 'order_id',

                        //'status' => 'order.status',

        ))
        ->joinInner(array(
                 'st' => 'core_store'),
                 'st.store_id = orderitem.store_id'
                 )
       /* ->joinLeft(array(
                 'orders' => 'sales_flat_order'),
                 'orders.entity_id = orderitem.order_id'
                 )*/


        //->where('((orderitem.created_at>="'.$from.'") AND (orderitem.created_at <="'.$to.'")) AND (orders.status != "cancelled") '.$skufilter.$joinChildren.$where_store )
        
        ->where('((orderitem.created_at>="'.$from.'") AND (orderitem.created_at <="'.$to.'")) '.$joinChildren.$where_store )
        ->group('orderitem.sku')
        ->order('st.name', 'ASC')


/*
        $this->getSelect()->reset()
        ->order('orderitem.product_type')
        ->from(
            array('orders' => $this->getTable('sales/order')),
            array(
                        //Select Columns
                'Site' => 'store.name',

                        //'status' => 'order.status',

                ))
        ->joinInner(
           array('store' => 'core_store',
            'store.store_id = order.store_id'
            ),
           array()
           )

        






        //get ordered items from order

        ->joinLeft(
            array('orderitem' => $this->getTable('sales/order_item')),
            'orderitem.order_id = orders.entity_id',
            //'orderitem.parent_item_id = orderitem.item_id'
            //'orderitem.order_id = orders.entity_id',
                   // 'orderitem.product_type = "bundle"',
            array(
                       //'orderitem'  => "orderitem.product_id" 
                'Site' => "st.name",
                'sku'  => "orderitem.sku",

                'pid'  => "orderitem.product_id",
                'Type'  => "orderitem.product_type",
                //'orderitemqty' => 'SUM(DISTINCT(orderitem.qty_ordered)) - SUM(DISTINCT(orderitem.qty_refunded))',
                #'orderitemqty' => 'CEILING(SUM(orderitem.qty_ordered)/COUNT(`orderitem`.`product_id` ) - SUM(orderitem.qty_refunded)/COUNT(`orderitem`.`product_id` )) ',
                //'orderitemqty' => 'SUM(DISTINCT(orderitem.qty_ordered)) - SUM(DISTINCT(orderitem.qty_refunded))',
                'orderitemqty' => 'COUNT(parentid.product_id)',
                'ProductName'  => "orderitem.name",
                //'Total' => 'SUM(orderitem.base_price * orderitem.qty_invoiced) - SUM(orderitem.base_price * orderitem.qty_refunded)',

                )
            )
        ->joinInner(array(
                 'st' => 'core_store'),
                 'st.store_id = orderitem.store_id'
                 )
        ->joinLeft(
            array('parentid' => $this->getTable('bundle/selection')),
            'parentid.product_id = orderitem.product_id',
                   // 'orderitem.product_type = "bundle"',
            array(
                       //'orderitem'  => "orderitem.product_id" 
                'parentid'  => "parentid.parent_product_id",


                )
            )

    //break up child arrays 
    ->where('((orders.created_at>="'.$from.'") AND (orders.created_at <="'.$to.'")) AND (orders.status != "cancelled") '.$skufilter.$joinChildren.$where_store )
    //->group('orders.entity_id')
    ->group('orderitem.product_id')
    //->group('orders.entity_id')
    ->order('parentid.parent_product_id', 'ASC')
    ;
            // uncomment next line to get the query log:
    */;
    Mage::log('BUNDLE SQL '.$this->getSelect()->__toString());
    return $this;
    }

    public function setStoreIds($storeIds)
    {
       //Mage::log('SET STORE IDS');
       if ($storeIds)
        $this->joinFields($this->_from, $this->_to, $storeIds); 
    else
        $this->joinFields($this->_from, $this->_to);
    return $this;
    }

}
?>