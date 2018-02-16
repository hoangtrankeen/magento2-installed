<?php
/***************************************************************************
 Extension Name	: Mostviewed Products 
 Extension URL	: http://www.magebees.com/most-viewed-products-extension-for-magento-2.html
 Copyright		: Copyright (c) 2016 MageBees, http://www.magebees.com
 Support Email	: support@magebees.com 
 ***************************************************************************/
namespace  Magebees\Mostviewed\Block\Adminhtml\Product;

use Magento\Store\Model\Store;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    protected $moduleManager;
    protected $_type;
    protected $_setsFactory;
    protected $_stockStatus;
    protected $_visibility;
    protected $_websiteFactory;
    protected $_customcollection;
    
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magebees\Mostviewed\Model\ResourceModel\Customcollection\Collection $customcollection,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Catalog\Model\Product\Type $type,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $setsFactory,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $status,
        \Magento\Catalog\Model\Product\Visibility $visibility,
        \Magento\Store\Model\WebsiteFactory $websiteFactory,
        array $data = []
    ) {
        $this->_productFactory = $productFactory;
        $this->moduleManager = $moduleManager;
        $this->_type = $type;
        $this->_customcollection = $customcollection;
        $this->_setsFactory = $setsFactory;
        $this->_stockStatus = $status;
        $this->_visibility = $visibility;
        $this->_websiteFactory = $websiteFactory;
        
        parent::__construct($context, $backendHelper, $data);
    }
    
    protected function _construct()
    {
        parent::_construct();
        $this->setId('productGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }
    
    protected function _getStore()
    {
        $storeId = (int)$this->getRequest()->getParam('store', 0);
        return $this->_storeManager->getStore($storeId);
    }
    
    protected function _prepareCollection()
    {
        $storeId = (int)$this->getRequest()->getParam('store', 0);
        $customcollection=$this->_customcollection->getData();
        foreach ($customcollection as $custom) {
            if (($custom['store_id']==$storeId) && ($storeId!=0)) {
                $entityId_str=$custom['entity_id'];
                if (empty($entityId_str)) {
                    $entityId_str=0;
                }
                $entity= explode(",", $entityId_str);
            } else {
                $entity=0;
            }
            if ($storeId==0) {
                $entityId_str[]=$custom['entity_id'];
            }
             $store_ids[]=$custom['store_id'];
        }
        
        if ($customcollection) {
            if ($storeId==0) {
                $new_entityId= implode(",", $entityId_str);
                $new= explode(",", $new_entityId);
                $entity=array_unique($new);
            } elseif (!in_array($storeId, $store_ids)) {
                $entity=0;
            } else {
                $entity= explode(",", $entityId_str);
            }
        } else {
            $entity=0;
        }
        
        $store = $this->_getStore();
        $productCollection = $this->_productFactory->create()->getCollection()->addAttributeToSelect(
            'sku'
        )->addAttributeToSelect(
            'name'
        )->addAttributeToSelect(
            'attribute_set_id'
        )->addAttributeToSelect(
            'type_id'
        )->setStore(
            $store
        );

        $productCollection->setStoreId($store->getId());
        $productCollection->addStoreFilter($store);
        $productCollection->joinAttribute(
            'name',
            'catalog_product/name',
            'entity_id',
            null,
            'inner',
            Store::DEFAULT_STORE_ID
        );
        $productCollection->joinAttribute(
            'custom_name',
            'catalog_product/name',
            'entity_id',
            null,
            'inner',
            $store->getId()
        );
        $productCollection->joinAttribute(
            'status',
            'catalog_product/status',
            'entity_id',
            null,
            'inner',
            $store->getId()
        );
        $productCollection->joinAttribute(
            'visibility',
            'catalog_product/visibility',
            'entity_id',
            null,
            'inner',
            $store->getId()
        );
        $productCollection->joinAttribute('price', 'catalog_product/price', 'entity_id', null, 'left', $store->getId());
        $productCollection->addAttributeToSelect('price');
        $productCollection->addFieldToFilter('entity_id', ['in' => $entity]);
        $this->setCollection($productCollection);
        $this->getCollection()->addWebsiteNamesToResult();
        parent::_prepareCollection();
        return $this;
    }

    
    protected function _addColumnFilterToCollection($filterColumn)
    {
        if ($this->getCollection()) {
            if ($filterColumn->getId() == 'websites') {
                $this->getCollection()->joinField(
                    'websites',
                    'catalog_product_website',
                    'website_id',
                    'product_id=entity_id',
                    null,
                    'left'
                );
            }
        }
        return parent::_addColumnFilterToCollection($filterColumn);
    }
    
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('product');
        $this->getMassactionBlock()->addItem(
            'display',
            [
                        'label' => __('Delete'),
                        'url' => $this->getUrl('mostviewed/*/massdelete'),
                        'confirm' => __('Are you sure want to delete mostviewed products?'),
                        'selected'=>true
                ]
        );
        return $this;
    }

    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            [
                'header' => __('ID'),
                'type' => 'number',
                'index' => 'entity_id',
                'header_css_class' => 'col-id entity_id',
                'column_css_class' => 'col-id entity_id'
            ]
        );
        $this->addColumn(
            'name',
            [
                'header' => __('Name'),
                'index' => 'name',
                'class' => 'col-name name'
            ]
        );

        $store_detail = $this->_getStore();
        if ($store_detail->getId()) {
            $this->addColumn(
                'custom_name',
                [
                    'header' => __('Name in %1', $store_detail->getName()),
                    'index' => 'custom_name',
                    'header_css_class' => 'col-name custom_name',
                    'column_css_class' => 'col-name custom_name'
                ]
            );
        }

        $this->addColumn(
            'type',
            [
                'header' => __('Type'),
                'index' => 'type_id',
                'type' => 'options',
                'options' => $this->_type->getOptionArray()
            ]
        );

        $optionSets = $this->_setsFactory->create()->setEntityTypeFilter(
            $this->_productFactory->create()->getResource()->getTypeId()
        )->load()->toOptionHash();

        $this->addColumn(
            'set_name',
            [
                'header' => __('Attribute Set'),
                'index' => 'attribute_set_id',
                'type' => 'options',
                'options' => $optionSets,
                'header_css_class' => 'col-attr-name set_name',
                'column_css_class' => 'col-attr-name set_name'
            ]
        );
        $this->addColumn(
            'sku',
            [
                'header' => __('SKU'),
                'index' => 'sku'
            ]
        );
        $store_detail = $this->_getStore();
        $this->addColumn(
            'price',
            [
                'header' => __('Price'),
                'type' => 'price',
                'currency_code' => $store_detail->getBaseCurrency()->getCode(),
                'index' => 'price',
                'header_css_class' => 'col-price',
                'column_css_class' => 'col-price'
            ]
        );
        $this->addColumn(
            'status',
            [
                'header' => __('Status'),
                'index' => 'status',
                'type' => 'options',
               'options' => $this->_stockStatus->getOptionArray()
            ]
        );
        if (!$this->_storeManager->isSingleStoreMode()) {
            $this->addColumn(
                'websites',
                [
                    'header' => __('Websites'),
                    'sortable' => false,
                    'index' => 'websites',
                    'type' => 'options',
                    'options' => $this->_websiteFactory->create()->getCollection()->toOptionHash(),
                    'header_css_class' => 'col-websites',
                    'column_css_class' => 'col-websites'
                ]
            );
        }
        $block = $this->getLayout()->getBlock('grid.bottom.links');
        if ($block) {
            $this->setChild('grid.bottom.links', $block);
        }
        return parent::_prepareColumns();
    }
    
    public function getGridUrl()
    {
        return $this->getUrl('mostviewed/manage/index', ['_current' => true]);
    }
}
