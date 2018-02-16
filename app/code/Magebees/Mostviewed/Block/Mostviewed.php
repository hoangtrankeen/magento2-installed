<?php
/***************************************************************************
 Extension Name	: Mostviewed Products 
 Extension URL	: http://www.magebees.com/most-viewed-products-extension-for-magento-2.html
 Copyright		: Copyright (c) 2016 MageBees, http://www.magebees.com
 Support Email	: support@magebees.com 
 ***************************************************************************/
namespace Magebees\Mostviewed\Block;

class Mostviewed extends \Magento\Catalog\Block\Product\AbstractProduct
{
    protected $_orderstatus;
    protected $_mvmanualCollection;
    protected $_datetime;
    protected $_collection;
    protected $_stock;
    protected $_config;
    protected $_product_visibility;
    protected $_sliderconfig;
    protected $_moduleManager;
    protected $urlHelper;
    protected $_imageHelper;
    protected $_storeManager;
    protected $_mostvieweds;
    protected $_productCollectionFactory;
    protected $pager;
    protected $_defaultToolbarBlock = 'Magento\Catalog\Block\Product\ProductList\Toolbar';
    const PAGE_VAR_NAME = 'np';
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Magebees\Mostviewed\Model\ResourceModel\Customcollection\Collection $mvmanualCollection,
        \Magento\CatalogInventory\Helper\Stock $stockHelper,
        \Magento\Reports\Model\ResourceModel\Product\CollectionFactory $reportModel,
        \Magento\Catalog\Model\Product\Visibility $visibilityModel,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $datetime,
        \Magento\Sales\Model\Order\Status\History $orderstatus,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    ) {
    
        $this->_coreResource = $resource;
        $this->urlHelper = $urlHelper;
        $this->reportModel = $reportModel;
        $this->stockHelper = $stockHelper;
        $this->visibilityModel = $visibilityModel;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_mvmanualCollection = $mvmanualCollection;
        $this->_datetime=$datetime;
        $this->_orderstatus=$orderstatus;
        $this->resourceConnection = $resourceConnection;
        $this->_objectManager = $objectManager;
        parent::__construct($context, $data);
    }

    public function getConfig()
    {
        return $this->_scopeConfig->getValue('mostviewed/setting', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getSliderconfig()
    {
        return $this->_scopeConfig->getValue('mostviewed/slidersetting', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    
    public function _toHtml()
    {
        $this->_config=$this->getConfig();
        if ($this->_config['enable']=="0") {
            return '';
        }
            
        if (!$this->getTemplate()) {
            $this->setTemplate('mostviewed_grid.phtml');
        }
            return parent::_toHtml();
    }
    
    
    public function setWidgetOptions()
    {
        
        $this->setShowHeading((bool)$this->getWdShowHeading());
        $this->setHeading($this->getWdHeading());
        $this->setProductType($this->getWdProductType());
        $this->setMostviewed($this->getWdMostviewed());
        $this->setCategories($this->getWdCategories());
        $this->setSortBy($this->getWdSortBy());
        $this->setSortOrder($this->getWdSortOrder());
        $this->setProductsPrice((bool)$this->getWdPrice());
        $this->setDescription((bool)$this->getWdDescription());
        $this->setAddToCart((bool)$this->getWdCart());
        $this->setAddToWishlist((bool)$this->getWdWishlist());
        $this->setAddToCompare((bool)$this->getWdCompare());
        $this->setOutOfStock((bool)$this->getWdOutStock());
        $this->setAjaxscrollPage((bool)$this->getWdAjaxscrollPage());
        //Template Settings
        $this->setNoOfProduct((int)$this->getWdNoOfProduct());
        $this->setProductsPerRow((int)$this->getWdProductsPerRow());
        $this->setProductsPerPage($this->getWdProductsPerPage());
        $this->setShowSlider((bool)$this->getWdSlider());
        
        //slider Settings
        $this->setAutoscroll((bool)$this->getWdAutoscroll());
        //$this->setPagination((bool)$this->getWdPagination());
        $this->setNavarrow((bool)$this->getWdNavarrow());
    }
    
    public function setConfigValues()
    {
        $this->_config=$this->getConfig();
        $this->_sliderConfig=$this->getSliderconfig();
        $this->setEnabled((bool)$this->_config['enable']);
        $this->setShowHeading((bool)$this->_config['show_heading']);
        $this->setProductType($this->_config['product_type']);
        $this->setHeading($this->_config['heading']);
        $this->setMostviewed($this->_config['mostviewed']);
        $this->setCategories($this->_config['categories']);
        $this->setSortBy($this->_config['sort_by']);
        $this->setSortOrder($this->_config['sort_order']);
        $this->setProductsPrice((bool)$this->_config['price']);
        $this->setDescription((bool)$this->_config['description']);
        $this->setAddToCart((bool)$this->_config['cart']);
        $this->setAddToWishlist((bool)$this->_config['wishlist']);
        $this->setAddToCompare((bool)$this->_config['compare']);
        $this->setOutOfStock((bool)$this->_config['out_stock']);
        $this->setAjaxscrollPage((bool)$this->_config['enable_ajaxscroll_page']);
        //Template Settings
        $this->setNoOfProduct((int)$this->_config['no_of_product']);
        $this->setProductsPerRow((int)$this->_config['products_per_row']);
        $this->setProductsPerPage($this->_config['per_page_value']);
        $this->setShowSlider((bool)$this->_config['slider']);

        //slider Settings
        $this->setAutoscroll((bool)$this->_sliderConfig['autoscroll']);
        //$this->setPagination((bool)$this->_sliderConfig['pagination']);
        $this->setNavarrow((bool)$this->_sliderConfig['navarrow']);
    }
    
    protected function _beforeToHtml()
    {
        
        if ($this->getType()=="Magebees\Mostviewed\Block\Widget\Mostviewedwidget\Interceptor") {
            $this->setWidgetOptions();
        } elseif ($this->getType()=="Magebees\Mostviewed\Block\Widget\Mostviewedwidget") {
            $this->setWidgetOptions();
        } else {
            $this->setConfigValues();
        }
        $this->setProductCollection($this->getMostviewedsCollection());
        return parent::_beforeToHtml();
    }
    
    public function getPagerHtml()
    {
        $total_limit=$this->getNoOfProduct();
            $pagination=$this->getProductsPerPage();
            $page_arr=explode(",", $pagination);
            $limit=[];
        foreach ($page_arr as $page) {
            $limit[$page]=$page;
        }
        
        if ($this->getProductCollection()->getSize()) {
            if (!$this->pager) {
                $this->pager = $this->getLayout()->createBlock(
                    'Magento\Catalog\Block\Product\Widget\Html\Pager',
                    'mostviewed.pager'
                );

                $this->pager->setAvailableLimit($limit)
                ->setLimitVarName('mv_limit')
                ->setPageVarName('mp')
                ->setShowPerPage(true)
                ->setTotalLimit($total_limit)
                ->setCollection($this->getProductCollection());
            }
            if ($this->pager instanceof \Magento\Framework\View\Element\AbstractBlock) {
                return $this->pager->toHtml();
            }
        }
        return '';
    }
    
    /****Get Product collection for Auto Mostviewed*****/
    public function getAutoMostviewedCollection()
    {
        $product_ids=[];
        $storeId=$this->_storeManager->getStore()->getId();
        $collection = $this->reportModel->create();
        $connection=$this->resourceConnection->getConnection();
        $viewindextable = $this->_coreResource->getTableName('report_viewed_product_index');
        $select=$collection->getSelect()->reset()->from(
            ['viewed_magebees' => $viewindextable]
        );
        $result=$connection->query($select)->fetchAll();
        foreach ($result as $res) {
            $product_ids[]=$res['product_id'];
        }
        
        //return $collection;
        return $product_ids;
    }
    /****Get Product collection for manually Mostviewed*****/

    public function getManualMostviewedCollection()
    {
        $product_ids=$this->getProductsIds();
        return $product_ids;
    }
    public function getMostviewedsCollection()
    {
        switch ($this->getProductType()) {
            case 2: //Auto
                    $product_ids = $this->getAutoMostviewedCollection();
                    
                break;
            case 1: //Manually
                $product_ids = $this->getManualMostviewedCollection();
                break;
            case 0:
                $collection1 = $this->getAutoMostviewedCollection();
                $collection2 = $this->getManualMostviewedCollection();
                $product_ids = array_unique(array_merge($collection1, $collection2));
                break;
            default:
                $product_ids = $this->getAutoMostviewedCollection();
                break;
        }
        
        $storeId=$this->_storeManager->getStore()->getId();
                $collection = $this->_productCollectionFactory->create();
                $collection->addAttributeToSelect('name')
               ->addMinimalPrice()
               ->addFinalPrice()
               ->addAttributeToSelect('*')
               ->setStore($storeId)
               ->addStoreFilter($storeId)
               ->addFieldToFilter('entity_id', ['in' =>$product_ids])
               ->addAttributeToFilter('visibility', 4);
               
               
        //Display out of stock products
        if (!$this->getOutOfStock()) {
            $this->stockHelper->addInStockFilterToCollection($collection);
        }
    
        
        //Display By Category
        
        if ($this->getMostviewed()==2) {
            if ($this->getCategories()) {
                $categorytable = $this->_coreResource->getTableName('catalog_category_product');
                $collection->getSelect()
                        ->joinLeft(['ccp' => $categorytable], 'e.entity_id = ccp.product_id', 'ccp.category_id')
                        ->group('e.entity_id')
                        ->where("ccp.category_id IN (".$this->getCategories().")");
            }
        }
        
        //Set Sort Order
        if ($this->getSortOrder()=='rand') {
            $collection->getSelect()->order('rand()');
        } else {
                    $collection->addAttributeToSort($this->getSortBy(), $this->getSortOrder());
        }
        $total_limit=$this->getNoOfProduct();
        $collection->getSelect()->limit($total_limit);
        if (!$this->getShowSlider()) {
            $pagination=$this->getProductsPerPage();
            $page_arr=explode(",", $pagination);
            $limit=[];
            foreach ($page_arr as $page) {
                $limit[$page]=$page;
            }
            $default_limit=current($limit);
         //get values of current page. if not the param value then it will set to 1
            $page=($this->getRequest()->getParam('mp'))? $this->getRequest()->getParam('mp') : 1;
        //get values of current limit. if not the param value then it will set to 1
            $pageSize=($this->getRequest()->getParam('mv_limit'))? $this->getRequest()->getParam('mv_limit') :$default_limit;
            $collection->setPageSize($pageSize);
            $collection->setCurPage($page);
        }
        
        return $collection;
    }
    
    public function getProductsIds()
    {
        $storeId=$this->_storeManager->getStore()->getId();
        $customcollection=$this->_mvmanualCollection->getData();
    
        foreach ($customcollection as $custom) {
            if ($custom['store_id']==$storeId) {
                $product_ids=$custom['entity_id'];
            }
        }
        
        if (empty($product_ids)) {
            foreach ($customcollection as $custom) {
                $store_arr=[0,$storeId];
                foreach ($store_arr as $store) {
                    if ($custom['store_id']==$store) {
                        $product_ids[]=$custom['entity_id'];
                    }
                }
            }
            if (!empty($product_ids)) {
                $new_entityId= implode(",", $product_ids);
                $new= explode(",", $new_entityId);
                $entity=array_unique($new);
            } else {
                return $product_ids=[0];
            }
            return $entity;
        } else {
            $entity= explode(",", $product_ids);
            return $entity;
        }
    }
        
    public function getImageHelper()
    {
        return $this->_imageHelper;
    }

    public function getAddToCartPostParams(\Magento\Catalog\Model\Product $product)
    {
        $url = $this->getAddToCartUrl($product);
        return [
            'action' => $url,
            'data' => [
            'product' => $product->getEntityId(),
            \Magento\Framework\App\Action\Action::PARAM_NAME_URL_ENCODED =>
                $this->urlHelper->getEncodedUrl($url),
            ]
        ];
    }
    public function getUniqueSliderKey()
    {
        $key = uniqid();
        return $key;
    }
}
